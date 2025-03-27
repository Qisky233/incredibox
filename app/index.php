<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/index.css">
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
            <section>
                <div id="game-section">
                    <div id="game-notice">
                        <h1>Incredibox Mustard</h1>
                        <p>The game resource file is too large and the loading time is slow. Please wait patiently.</p>
                    </div>
                    <div id="game-container">
                        <iframe id="game_iframe" src="https://data.gameflare.com/games/10449/OKT1Jq81tFTX59/index.html" allowfullscreen></iframe>
                    </div>
                </div>
            </section>
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
            <section>
                <div id="about-section">
                    <h2>Incredibox Mustard (Colorbox Mustard)</h2>
                    <p>Incredibox Mustard: The Ultimate Fan-Made Music Experience</p>
                    <p>The innovative Incredibox Mustard has revolutionized how fans interact with music creation. As a standout modification of the original Incredibox platform, Incredibox Mustard brings fresh energy to the beloved music game. What makes Incredibox Mustard special is its unique approach to sound mixing and visual design, created with passion by dedicated community members.</p>

                    <h3>The Evolution of Incredibox Mustard</h3>
                    <p>Since its inception, Incredibox Mustard has grown from a simple mod to a comprehensive music creation platform. The development team behind Incredibox Mustard focused on creating an interface that balances accessibility with depth. Every update to Incredibox Mustard has brought new features that enhance the user experience, making it a favorite among both beginners and experienced music creators.</p>

                    <h3>Comprehensive Sound Library in Incredibox Mustard</h3>
                    <p>The sound library in Incredibox Mustard spans multiple genres and styles. From deep bass lines to crisp hi-hats, Incredibox Mustard provides creators with a vast array of musical elements. Each sound in Incredibox Mustard has been carefully crafted to ensure perfect harmony when mixed with others, allowing for countless creative combinations.</p>

                    <h3>Character Design and Animation</h3>
                    <p>The visual appeal of Incredibox Mustard comes from its distinctive character designs. Each Incredibox Mustard character represents different musical elements, with animations that sync perfectly to the beats. The attention to detail in Incredibox Mustard's character design makes the experience both visually and musically engaging.</p>

                    <h3>Advanced Mixing Features</h3>
                    <p>When using Incredibox Mustard, creators have access to sophisticated mixing tools. The intuitive interface of Incredibox Mustard allows for precise control over each musical element. Advanced users of Incredibox Mustard can create complex arrangements by layering multiple sounds and adjusting their timing perfectly.</p>

                    <h3>Community and Sharing</h3>
                    <p>The Incredibox Mustard community is vibrant and supportive. Users regularly share their Incredibox Mustard creations, inspiring others and pushing the boundaries of what's possible. The social features integrated into Incredibox Mustard make it easy to discover new compositions and connect with fellow creators.</p>

                    <h3>Technical Innovations</h3>
                    <p>Behind the scenes, Incredibox Mustard employs sophisticated audio processing technology. The development team has optimized Incredibox Mustard to run smoothly across different devices and browsers. These technical improvements make Incredibox Mustard more accessible and enjoyable for everyone.</p>

                    <h3>Creating Your First Mix</h3>
                    <p>Getting started with Incredibox Mustard is straightforward. Begin by selecting characters from the Incredibox Mustard roster and dropping them onto the stage. As you add more elements, your Incredibox Mustard creation will come to life. Experiment with different combinations to discover the unique sound possibilities in Incredibox Mustard.</p>

                    <h3>Advanced Techniques</h3>
                    <p>Once you're familiar with the basics of Incredibox Mustard, try exploring advanced techniques. Experienced Incredibox Mustard users often create complex arrangements by carefully timing their character combinations. The depth of Incredibox Mustard's mechanics allows for incredibly sophisticated musical compositions.</p>

                    <h3>Special Events and Updates</h3>
                    <p>The Incredibox Mustard community regularly hosts events and challenges. These events showcase the creative potential of Incredibox Mustard and bring the community together. Keep an eye out for new Incredibox Mustard updates that introduce fresh content and features.</p>

                    <h3>Tips for Success</h3>
                    <p>To make the most of your Incredibox Mustard experience, consider these tips from experienced users:</p>
                    <ul>
                        <li>Start with a strong rhythm base in your Incredibox Mustard mix</li>
                        <li>Experiment with different character combinations in Incredibox Mustard</li>
                        <li>Save your favorite Incredibox Mustard creations to refine later</li>
                        <li>Connect with other Incredibox Mustard users for inspiration</li>
                    </ul>

                    <h3>Future Developments</h3>
                    <p>The future of Incredibox Mustard looks bright, with planned updates and improvements on the horizon. The development team continues to gather feedback from the Incredibox Mustard community to enhance the experience. Stay tuned for exciting new features and additions to Incredibox Mustard.</p>

                    <h3>Join the Community</h3>
                    <p>Ready to start your Incredibox Mustard journey? Join the growing community of creators and music enthusiasts. Whether you're a casual user or aspiring musician, Incredibox Mustard offers endless possibilities for musical expression. Share your Incredibox Mustard creations and become part of this exciting musical movement! 🎵✨</p>
                </div>
            </section>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <a href="/privacy.html">privacy</a>
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