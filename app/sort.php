<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/index.css">
<style>
/* 新增样式 */
main .container {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.sidebar {
    width: 200px;
    background: #f5f5f5;
    border-radius: 8px;
    padding: 20px;
    position: sticky;
    top: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.sidebar-title {
    font-size: 1.2em;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item a {
    display: block;
    padding: 10px 15px;
    color: #666;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.category-item.active a {
    background: #FFD700; /* 芥末黄主题色 */
    color: #333;
    font-weight: 500;
}

.category-item a:hover {
    background: rgba(255, 215, 0, 0.2);
}

.main-content {
    flex: 1;
}

/* 响应式适配 */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 90%;
        position: static;
    }
}
</style>
</head>
<body>
<div id="app">
    <header>
        <div class="container">
        <nav>
                <a href="/" id="website-name" title="Incredibox Mustard">Incredibox Mustard</a>
                <div id="nav-toggle" onclick="toggleMenu()">&#9776;</div>
                <div id="nav-menu">
                    <a href="/" title="Home">Home</a>
                    <a href="/sort.php" title="Sort">Sort</a>
                    <a href="/blog.php" title="Blog">Blog</a>
                    <a href="/about.php" title="About">About</a>
                </div>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">
            <!-- 新增侧边栏 -->
            <aside class="sidebar">
                <h3 class="sidebar-title">Tag</h3>
                <ul class="category-list">
                    <li class="category-item active">
                        <a href="#">Incredibox</a>
                    </li>
                </ul>
            </aside>
            <section>
                <h2>Play Incredibox Mod</h2>
                <!-- 游戏列表内容 -->
                <div id="game-list">
                    <?php
                    // 数据库连接
                    function getDb() {
                        $db = new SQLite3(__DIR__ . '/database/db.sqlite'); // 确保路径正确
                        return $db;
                    }

                    // 连接到 SQLite 数据库
                    try {
                        $db = getDb();

                        // 查询数据
                        $stmt = $db->query("SELECT * FROM list");
                        while ($game = $stmt->fetchArray(SQLITE3_ASSOC)) { // 使用 fetchArray 获取结果
                            $encodedTitle = str_replace(' ', '_', $game['title']);
                            echo '<div>';
                            // echo '<a href="/game.php?title=' . htmlspecialchars($encodedTitle) . '" target="_blank">';  // urlencode($game['title'])
                            echo '<a href="' . htmlspecialchars($encodedTitle) . '.html">';
                            echo '<img src="' . htmlspecialchars($game['coverUrl']) . '" alt="' . htmlspecialchars($game['title']) . '" loading="lazy" title="' . htmlspecialchars($game['title']) . '">';
                            echo '<p>' . htmlspecialchars($game['title']) . '</p>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } catch (Exception $e) { // 捕获通用异常
                        echo '<p>Error fetching games: ' . $e->getMessage() . '</p>';
                    } finally {
                        // 关闭数据库连接
                        $db->close();
                    }
                    ?>
                </div>
            </section>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 Incredibox Mustard</p>
            </div>
        </div>
    </footer>
</div>
<script>
    function toggleMenu() {
        const menu = document.getElementById('nav-menu');
        menu.classList.toggle('active');
    }
</script>
</body>
</html>