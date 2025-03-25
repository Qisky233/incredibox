<?php
// 定义允许访问的页面
$allowedPages = ['home', 'about', 'contact'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowedPages) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP SPA 应用</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="app">
        <!-- 可复用的 Header 组件 -->
        <?php include 'components/header.php'; ?>
        
        <div class="container">
            <!-- 可复用的 Sidebar 组件 -->
            <?php include 'components/sidebar.php'; ?>
            
            <!-- 动态内容区域 -->
            <main id="main-content">
                <?php include "pages/{$page}.php"; ?>
            </main>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>