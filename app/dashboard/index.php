<?php
// 定义允许访问的页面
$allowedPages = ['static', 'article', 'list', 'add', 'ad'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowedPages) ? $_GET['page'] : 'static';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP SPA 应用</title>
<style>
/* 基本重置 */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
}

#app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* 头部样式 */
.app-header {
    background: #333;
    color: white;
    padding: 1rem;
    text-align: center;
}

.main-nav {
    margin-top: 1rem;
}

.main-nav a {
    color: white;
    margin: 0 1rem;
    text-decoration: none;
}

.main-nav a:hover {
    text-decoration: underline;
}

/* 主体布局 */
.container {
    display: flex;
    flex: 1;
}

/* 侧边栏样式 */
.sidebar {
    width: 200px;
    background: #f4f4f4;
    padding: 1rem;
}

.sidebar ul {
    list-style: none;
    margin-top: 1rem;
}

.sidebar li {
    margin-bottom: 0.5rem;
}

.sidebar a {
    text-decoration: none;
    color: #333;
}

.sidebar a:hover {
    color: #666;
}

/* 主内容区样式 */
#main-content {
    flex: 1;
    padding: 2rem;
    transition: margin-left 0.3s ease; /* 添加过渡效果 */
}


#main-content .page-content {
    margin-top: 60px;
    margin-left: 250px;
}

/* 活动菜单项样式 */
.active {
    font-weight: bold;
    color: #007bff !important;
}

@media (max-width: 768px) {
    #main-content .page-content {
        margin-top: 60px;
        margin-left: 0;
    }
}
</style>
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

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    </script>
    <script src="script.js"></script>
</body>
</html>