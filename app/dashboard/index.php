<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Section -->
    <?php include('./component/header.html'); ?>

    <!-- Sidebar Section -->
    <?php include('./component/sidebar.html'); ?>

    <!-- Main Content Section -->
    <div class="main-content">
        <?php
        // Get the current request URI
        $requestUri = $_SERVER['REQUEST_URI'];
        // Extract the path after the base URL
        $path = parse_url($requestUri, PHP_URL_PATH);
        // Remove leading slash
        $path = ltrim($path, '/');
        // Split the path into parts
        $pathParts = explode('/', $path);
        // Get the first part of the path (e.g., 'dashboard', 'static')
        $page = $pathParts[0] ?? 'index';

        // Define the base directory for pages
        $baseDir = './page/';

        // Check if the requested page exists
        if ($page === '' || $page === 'dashboard') {
            $page = 'index'; // Default to index if no page is specified or if it's the dashboard
        }

        $filePath = $baseDir . $page . '.php';

        if (file_exists($filePath)) {
            include($filePath);
        } else {
            // If the page does not exist, include the 404 page
            include($baseDir . '404.html');
        }
        ?>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>