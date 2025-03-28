<?php
session_start();

require_once('../../../config.php');
require_once('../../../init.php');
require_once('../../../admin/admin-functions.php');

if(is_login() && USER_ADMIN && !ADMIN_DEMO){
    if(isset($_POST['action']) && isset($_POST['slug'])){
        if($_POST['action'] == 'unzip'){
            $curl = curl_request('https://api.cloudarcade.net/free-game-bundle/files/'.$_POST['slug'].'/info.json');
            $data = json_decode($curl, true);
            if(isset($data['title'])){
                $exist_game = Game::getBySlug($data['slug']);
                if($exist_game == null){
                    if(file_exists('../../../games/'.$data['slug'])){
                        echo json_encode([
                            'status' => 'warning',
                            'message' => 'Game folder already exists: '.$data['slug']
                        ]);
                    } else {
                        $target = '../../../game-file.zip';
                        $_ch = curl_init();
                        curl_setopt($_ch, CURLOPT_URL, 'https://api.cloudarcade.net/free-game-bundle/files/'.$_POST['slug'].'/'.$_POST['slug'].'.zip');
                        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($_ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($_ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                        $remoteFile = curl_exec($_ch);
                        curl_close($_ch);
                        if($remoteFile !== false){
                            $localFile = fopen($target, 'w');
                            if($localFile){
                                fwrite($localFile, $remoteFile);
                                fclose($localFile);
                                if(file_exists($target)){
                                    mkdir('../../../games/'.$_POST['slug'], 0755, true);
                                    $zip = new ZipArchive;
                                    $res = $zip->open($target);
                                    if ($res === TRUE) {
                                        $zip->extractTo('../../../games/'.$_POST['slug']);
                                        $zip->close();
                                        echo json_encode([
                                            'status' => 'ok',
                                            'message' => 'Game installed successfully'
                                        ]);
                                    } else {
                                        echo json_encode([
                                            'status' => 'error',
                                            'message' => 'Failed to extract zip file'
                                        ]);
                                    }
                                    unlink($target);
                                } else {
                                    echo json_encode([
                                        'status' => 'error',
                                        'message' => 'Local file not found'
                                    ]);
                                }
                            } else {
                                echo json_encode([
                                    'status' => 'error',
                                    'message' => 'Could not create local file'
                                ]);
                            }
                        } else {
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Could not download remote file'
                            ]);
                        }
                    }
                } else {
                    echo json_encode([
                        'status' => 'warning',
                        'message' => 'Game "'.htmlspecialchars($data['title']).'" already exists in database'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid game data'
                ]);
            }
        }
    }
}
?>