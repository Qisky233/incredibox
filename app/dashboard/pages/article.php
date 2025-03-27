<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Incredibox Mustard</title>
    <link rel="stylesheet" href="./assets/css/index.css">
    <style>
        /* 添加博客样式 */
        .blog-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .blog-post {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .post-title {
            color: #FFD700;
            margin-bottom: 15px;
        }

        .post-content {
            line-height: 1.8;
            color: #444;
        }

        .post-meta {
            color: #888;
            font-size: 0.9em;
            margin-top: 20px;
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
        <div class="blog-container">
            <?php
            function getDb() {
                $db = new SQLite3(__DIR__ . '/database/db.sqlite');
                
                // 验证数据库文件存在
                if (!file_exists($dbPath)) {
                    die('<div class="error">Database not found</div>');
                }
                
                try {
                    $db = new SQLite3($dbPath);
                    $db->enableExceptions(true); // 启用异常处理
                    return $db;
                } catch (Exception $e) {
                    die('<div class="error">Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>');
                }
            }

            try {
                $db = getDb();
                
                // 执行查询
                $query = "SELECT 
                            title, 
                            content,
                            strftime('%Y-%m-%d', created_at) as post_date,
                            author 
                          FROM blog 
                          ORDER BY created_at DESC";
                
                $stmt = $db->prepare($query);
                $result = $stmt->execute();

                // 检查查询结果
                if (!$result) {
                    throw new Exception("Query execution failed");
                }

                $hasPosts = false;
                
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $hasPosts = true;
                    echo '
                    <article class="blog-post">
                        <h2 class="post-title">' . htmlspecialchars($row['title']) . '</h2>
                        <div class="post-content">' . nl2br(htmlspecialchars($row['content'])) . '</div>
                        <div class="post-meta">
                            Posted on ' . htmlspecialchars($row['post_date']) . '
                            ' . (!empty($row['author']) ? 'by ' . htmlspecialchars($row['author']) : '') . '
                        </div>
                    </article>';
                }

                if (!$hasPosts) {
                    echo '<div class="no-posts">No blog posts found</div>';
                }

            } catch (Exception $e) {
                echo '<div class="error">Error loading posts: ' . htmlspecialchars($e->getMessage()) . '</div>';
            } finally {
                if (isset($db)) {
                    $db->close();
                }
            }
            ?>
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