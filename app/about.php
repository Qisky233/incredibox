<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Incredibox Mustard</title>
    <link rel="stylesheet" href="./assets/css/index.css">
    <style>
        /* 新增关于页样式 */
        .about-hero {
            background: linear-gradient(45deg, #FFD70033, #ffffff);
            padding: 60px 0;
            margin-bottom: 40px;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .mission-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .contact-info {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            margin-top: 40px;
        }

        .contact-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .contact-item i {
            font-size: 24px;
            color: #FFD700;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .about-hero {
                padding: 40px 15px;
            }
            
            .mission-card {
                padding: 20px;
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
                    <a href="/about.php" title="About" class="active">About</a>
                </div>
            </nav>
        </div>
    </header>
    <main>
        <div class="about-hero">
            <div class="container">
                <h1>About Incredibox Mustard</h1>
                <p class="lead">Your Ultimate Destination for Incredibox Mods</p>
            </div>
        </div>

        <div class="container about-content">
            <div class="mission-card">
                <h2>Our Mission</h2>
                <p>We are committed to providing Incredibox fans around the world with the best quality music module resources and creating an interactive community with unlimited creativity. Through careful selection and continuous updates, we help users discover the latest and most interesting game mods.</p>
            </div>

            <section>
                <h2>Why Choose Us?</h2>
                <ul>
                    <li>✓ A library of quality modules updated daily</li>
                    <li>✓ Verified secure download link</li>
                    <li>✓ Active creator community support</li>
                    <li>✓ Multi-platform compatibility testing</li>
                </ul>
            </section>

            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <h3>Email Support</h3>
                    <p>contact@mustardmods.com</p>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-comments"></i>
                    <h3>Community</h3>
                    <p>Join Discord Server</p>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Based In</h3>
                    <p>Creative Studio, Paris</p>
                </div>
            </div>
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
    
    // 添加Font Awesome图标库
    const faScript = document.createElement('script');
    faScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js';
    document.head.appendChild(faScript);
</script>
</body>
</html>