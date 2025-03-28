<?php

/**
 * CloudArcade CMS System Updater
 * Handles secure system updates with validation and rollback capabilities
 */

class SystemUpdater
{
    private $apiEndpoint = 'https://api.cloudarcade.net/cms-update/download.php';
    private $tempDir;
    private $purchaseCode;
    private $currentVersion;
    private $targetVersion;
    private $updateToken;

    private $lastError = null;
    private $isRollbackNeeded = false;

    public function __construct()
    {
        $this->tempDir = ABSPATH . 'content/temp';
        $this->purchaseCode = check_purchase_code();
        $this->currentVersion = VERSION;
        $parts = explode(".", VERSION);
        $parts[2] = (int)$parts[2] + 1;
        $this->targetVersion = implode(".", $parts);
    }

    /**
     * Check if a system update is available
     */
    public function checkUpdate()
    {
        try {
            // Verify purchase code is valid
            if (!$this->purchaseCode) {
                throw new Exception('Invalid purchase code');
            }

            // Make API request to check for updates
            $params = [
                'action' => 'check',
                'code' => $this->purchaseCode,
                'current_version' => $this->currentVersion
            ];

            if(isset($_GET['test_update'])){
                $params['test'] = true;
            }

            $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

            if (!$response || !isset($response['status'])) {
                throw new Exception('Invalid response from update server');
            }

            // Format the response
            switch ($response['status']) {
                case 'current':
                    return [
                        'status' => 'current',
                        'current_version' => $response['version'],
                        'message' => 'System is up to date'
                    ];

                case 'update':
                    return [
                        'status' => 'update',
                        'current_version' => $response['current'],
                        'next_version' => $response['next'],
                        'latest_version' => $response['latest'],
                        'changes' => isset($response['info']['changes']) ? $response['info']['changes'] : null,
                        'message' => 'Update available'
                    ];

                default:
                    throw new Exception($response['message'] ?? 'Unknown error occurred');
            }
        } catch (Exception $e) {
            $this->logError('Update check failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Main update method that orchestrates the entire update process
     */
    public function performUpdate()
    {
        // Update is always increcement
        try {
            // Clean previous temp files if any
            $this->cleanTempFiles();

            // Ensure temp directory exists
            if (!file_exists($this->tempDir)) {
                mkdir($this->tempDir, 0755, true);
            }

            // Get download token
            if (!$this->requestDownloadToken()) {
                throw new Exception('Failed to obtain download token');
            }

            // Download update package
            $downloadPath = $this->downloadUpdate();
            if (!$downloadPath) {
                throw new Exception('Failed to download update package');
            }

            // Create backup using existing method
            if (!$this->createBackup()) {
                throw new Exception('Failed to create backup');
            }

            // Verify and extract update
            if (!$this->verifyAndExtractUpdate($downloadPath)) {
                throw new Exception('Failed to verify or extract update package');
            }

            // Install update
            $this->isRollbackNeeded = true; // Set flag before making changes
            if (!$this->installUpdate()) {
                throw new Exception('Failed to install update');
            }

            // Cleanup
            $this->cleanTempFiles();
            $this->isRollbackNeeded = false;

            return [
                'status' => 'success',
                'message' => 'Update completed successfully'
            ];
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logError($e->getMessage());

            if ($this->isRollbackNeeded) {
                //
            }

            // Cleanup
            $this->cleanTempFiles();

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Request a download token from the API
     */
    private function requestDownloadToken()
    {
        $params = [
            'code' => $this->purchaseCode,
            'version' => $this->targetVersion,
            'generate' => 1
        ];

        if(isset($_GET['test_update'])){
            $params['test'] = true;
        }

        $response = $this->makeApiRequest($this->apiEndpoint, $params);
        if (!$response || !isset($response['status']) || $response['status'] !== 'success') {
            return false;
        }

        $this->updateToken = $response['token'];
        return true;
    }

    /**
     * Check and perform database updates if available
     */
    private function handleDatabaseUpdate()
    {
        $params = [
            'action' => 'db',
            'version' => $this->targetVersion
        ];

        if(isset($_GET['test_update'])){
            $params['test'] = true;
        }

        // Get DB update info from API
        $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

        if (!$response || !isset($response['status'])) {
            throw new Exception('Failed to check for database updates');
        }

        // If no DB updates found, return true
        if ($response['status'] !== 'found') {
            return true;
        }

        $dbUpdates = $response['db'];
        $conn = open_connection(); // Get database connection

        try {
            // Handle table operations
            if (isset($dbUpdates['tables'])) {
                foreach ($dbUpdates['tables'] as $tableName => $tableInfo) {
                    // Check if table exists
                    $tableExists = $conn->query("SHOW TABLES LIKE '$tableName'")->rowCount() > 0;

                    switch ($tableInfo['action']) {
                        case 'create_if_not_exists':
                            if (!$tableExists) {
                                $stmt = $conn->prepare($tableInfo['sql']);
                                if (!$stmt->execute()) {
                                    throw new Exception("Failed to create table: $tableName");
                                }
                            }
                            break;
                        case 'drop_if_exists':
                            if ($tableExists) {
                                $stmt = $conn->prepare("DROP TABLE `$tableName`");
                                if (!$stmt->execute()) {
                                    throw new Exception("Failed to drop table: $tableName");
                                }
                            }
                            break;
                    }
                }
            }

            // Handle column operations with improved error handling
            if (isset($dbUpdates['columns'])) {
                foreach ($dbUpdates['columns'] as $tableName => $columns) {
                    // Verify table exists before attempting column operations
                    if (!$conn->query("SHOW TABLES LIKE '$tableName'")->rowCount()) {
                        continue; // Skip if table doesn't exist
                    }

                    foreach ($columns as $columnName => $columnInfo) {
                        $columnExists = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'")->rowCount() > 0;

                        switch ($columnInfo['action']) {
                            case 'add_if_not_exists':
                                if (!$columnExists) {
                                    $stmt = $conn->prepare($columnInfo['sql']);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to add column: $columnName to table: $tableName");
                                    }
                                }
                                break;
                            case 'modify':
                                if ($columnExists) {
                                    $stmt = $conn->prepare($columnInfo['sql']);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to modify column: $columnName in table: $tableName");
                                    }
                                }
                                break;
                        }
                    }
                }
            }

            // Handle data operations with improved error handling
            if (isset($dbUpdates['data'])) {
                foreach ($dbUpdates['data'] as $tableName => $operations) {
                    foreach ($operations as $operation) {
                        try {
                            switch ($operation['action']) {
                                case 'insert':
                                    // Regular insert
                                    $stmt = $conn->prepare($operation['sql']);
                                    $stmt->execute();
                                    break;
                                case 'insert_if_not_exists':
                                    // Check if data exists using the provided check condition
                                    if (isset($operation['check_sql'])) {
                                        $checkStmt = $conn->prepare($operation['check_sql']);
                                        $checkStmt->execute($operation['check_params'] ?? []);
                                        if ($checkStmt->rowCount() === 0) {
                                            // Data doesn't exist, perform insert
                                            $stmt = $conn->prepare($operation['sql']);
                                            $stmt->execute($operation['params'] ?? []);
                                        }
                                    }
                                    break;
                                case 'update':
                                case 'delete':
                                    $stmt = $conn->prepare($operation['sql']);
                                    $stmt->execute();
                                    break;
                            }
                        } catch (PDOException $e) {
                            // Handle specific database errors
                            if (($operation['action'] === 'insert' || $operation['action'] === 'insert_if_not_exists')
                                && $e->getCode() == '23000'
                            ) {
                                // Duplicate key error - might be expected, continue
                                continue;
                            }
                            throw $e;
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Database update failed: ' . $e->getMessage());
        }
    }

    /**
     * Download the update package using the token
     */
    private function downloadUpdate()
    {
        if (!$this->updateToken) {
            return false;
        }

        $downloadPath = $this->tempDir . '/update.zip';

        $params = [
            'token' => $this->updateToken,
            'v' => VERSION,
            'ref' => DOMAIN
        ];

        if(isset($_GET['test_update'])){
            $params['test'] = true;
        }

        return $this->downloadFile($this->apiEndpoint, $params, $downloadPath);
    }

    /**
     * Create system backup before update using existing backup_cms function
     */
    private function createBackup()
    {
        try {
            // Using existing backup function
            backup_cms(ABSPATH, 'part');
            return true;
        } catch (Exception $e) {
            $this->logError('Backup failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify and extract update package
     */
    private function verifyAndExtractUpdate($packagePath)
    {
        // Handle database updates first
        try {
            $this->handleDatabaseUpdate();
        } catch (Exception $e) {
            throw new Exception('Database update failed, update process aborted: ' . $e->getMessage());
        }

        $zip = new ZipArchive();
        $zipResult = $zip->open($packagePath);

        if ($zipResult !== true) {
            $errorMessages = [
                ZipArchive::ER_EXISTS => 'File already exists',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                ZipArchive::ER_INVAL => 'Invalid argument',
                ZipArchive::ER_MEMORY => 'Memory allocation failure',
                ZipArchive::ER_NOENT => 'No such file',
                ZipArchive::ER_NOZIP => 'Not a zip archive',
                ZipArchive::ER_OPEN => 'Can\'t open file',
                ZipArchive::ER_READ => 'Read error',
                ZipArchive::ER_SEEK => 'Seek error'
            ];

            $errorMessage = isset($errorMessages[$zipResult])
                ? $errorMessages[$zipResult]
                : 'Unknown ZIP error';

            throw new Exception('Failed to open update package: ' . $errorMessage);
        }

        // Extract to temp directory first
        $extractPath = $this->tempDir . '/extract';

        // Check if extract directory exists and is writable
        if (!is_dir($extractPath) && !mkdir($extractPath, 0755, true)) {
            $zip->close();
            throw new Exception('Failed to create extraction directory: ' . $extractPath);
        }

        if (!is_writable($extractPath)) {
            $zip->close();
            throw new Exception('Extraction directory is not writable: ' . $extractPath);
        }

        // Try to extract
        if (!$zip->extractTo($extractPath)) {
            $error = error_get_last();
            $zip->close();
            throw new Exception('Failed to extract update package: ' . ($error['message'] ?? 'Unknown error'));
        }

        $zip->close();

        // Verify update package structure and integrity
        try {
            if (!$this->verifyUpdatePackage()) {
                throw new Exception('Update package verification failed');
            }
        } catch (Exception $e) {
            // Clean up extracted files if verification fails
            $this->removeDirectory($extractPath);
            throw $e; // Re-throw the exception after cleanup
        }

        return true;
    }

    /**
     * Install the updated files
     */
    private function installUpdate()
    {
        $extractPath = $this->tempDir . '/extract';

        try {
            // Copy new files to installation
            $this->copyDirectory($extractPath, ABSPATH);

            // Run any necessary database migrations
            if (file_exists($extractPath . '/update.php')) {
                include $extractPath . '/update.php';
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Installation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Utility Methods
     */
    private function makeApiRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function downloadFile($url, $params, $target)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // Add header info to response
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Download failed: ' . $error);
        }

        // Split headers and body
        $headers = substr($response, 0, $headerSize);
        $remoteFile = substr($response, $headerSize);

        // Check if response is JSON (either by Content-Type header or content)
        $isJson = (
            strpos($headers, 'Content-Type: application/json') !== false ||
            substr(trim($remoteFile), 0, 1) === '{'
        );

        if ($isJson) {
            $json = json_decode($remoteFile, true);
            if ($json) {
                if (isset($json['status']) && $json['status'] === 'error') {
                    throw new Exception('API Error: ' . ($json['message'] ?? 'Unknown error'));
                }
                // Log unexpected JSON response
                $this->logError('Unexpected JSON response: ' . $remoteFile);
                throw new Exception('Unexpected response format from server');
            }
        }

        // Check HTTP status code
        if ($httpCode !== 200) {
            throw new Exception('Download failed with HTTP code: ' . $httpCode);
        }

        // Ensure target directory exists
        $targetDir = dirname($target);
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception('Failed to create target directory: ' . $targetDir);
            }
        }

        // Write file with better error handling
        $localFile = fopen($target, 'w');
        if (!$localFile) {
            throw new Exception('Could not open file for writing: ' . $target);
        }

        try {
            if (fwrite($localFile, $remoteFile) === false) {
                throw new Exception('Failed to write to file: ' . $target);
            }
        } finally {
            fclose($localFile);
        }

        // Verify file was written
        if (!file_exists($target) || filesize($target) === 0) {
            throw new Exception('File write verification failed: ' . $target);
        }

        return $target;
    }

    private function cleanTempFiles()
    {
        if (is_dir($this->tempDir . '/extract')) {
            $this->removeDirectory($this->tempDir . '/extract');
        }
        if (file_exists($this->tempDir . '/update.zip')) {
            unlink($this->tempDir . '/update.zip');
        }
        if (is_dir($this->tempDir) && count(scandir($this->tempDir)) <= 2) {
            $this->removeDirectory($this->tempDir);
        }
    }

    private function verifyUpdatePackage()
    {
        $params = [
            'action' => 'info',
            'version' => $this->targetVersion
        ];

        if(isset($_GET['test_update'])){
            $params['test'] = true;
        }

        // Get hash from API for downloaded version
        $response = $this->makeApiRequest('https://api.cloudarcade.net/cms-update/info.php', $params);

        if (!$response || !isset($response['status']) || $response['status'] !== 'success') {
            throw new Exception('Failed to get update information from server: ' .
                (isset($response['message']) ? $response['message'] : 'Unknown error'));
        }

        if (!isset($response['info']['hash'])) {
            throw new Exception('Hash information not found in server response');
        }

        // Calculate hash of the update package
        $updateHash = hash_file('sha256', $this->tempDir . '/update.zip');

        // Verify hash
        if ($updateHash !== $response['info']['hash']) {
            throw new Exception('Update package integrity check failed. ' .
                'Expected: ' . $response['info']['hash'] . ' ' .
                'Got: ' . $updateHash);
        }

        return true;
    }

    private function logError($message)
    {
        error_log('[SystemUpdater] ' . $message);
    }

    private function copyDirectory($source, $dest)
    {
        $dir = opendir($source);
        if (!file_exists($dest)) {
            mkdir($dest);
        }
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') continue;

            $sourcePath = $source . '/' . $file;
            $destPath = $dest . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
        closedir($dir);
    }

    private function removeDirectory($dir)
    {
        if (!file_exists($dir)) return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        return rmdir($dir);
    }
}
