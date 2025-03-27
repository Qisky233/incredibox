<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/index.css">
<style>
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