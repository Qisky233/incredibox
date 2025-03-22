<template>
    <div>
        <header class="container">
            <nav>
                <a href="#" id="website-name" title="Incredibox Mustard">Incredibox Mustard</a>
                <div id="nav-toggle" onclick="toggleMenu()">&#9776;</div>
                <div id="nav-menu">
                    <a href="#" title="Home">Home</a>
                    <a href="#" title="Sprunki">Sprunki</a>
                    <a href="#" title="Incredibox">Incredibox</a>
                    <a href="#" title="Incredibox Mod">Incredibox Mod</a>
                </div>
            </nav>
        </header>
        <main class="container">
            <section>
                <div id="game-section">
                    <div id="game-notice">
                        <h1>{{ game.title }}</h1>
                        <p>{{ game.desc }}</p>
                    </div>
                    <div id="game-container">
<!--                        <iframe id="game_iframe" :src="" allowfullscreen></iframe>-->
                        <span v-html="game.iframe"></span>
                    </div>
                    <div id="game-info">
                        <h2>About the Game</h2>
                        <div id="game-info-content" v-html="game.info"></div>
                    </div>
                </div>
            </section>
        </main>
        <footer class="container">
            <div class="footer-content">
                <p>&copy; 2025 Incredibox Mustard</p>
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    props: {
        game: Object
    }
}
</script>
<style>
/* 基础样式 */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
}

header {
    background: linear-gradient(90deg, #ff6600, #ff8c42); /* 渐变色背景 */
    color: #fff;
    padding: 15px 0; /* 增加高度 */
    width: 100%;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* 增加阴影 */
    position: sticky;
    top: 0;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#website-name {
    font-size: 28px; /* 增大字体 */
    font-weight: bold;
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    font-family: 'Arial Black', sans-serif; /* 使用现代感强的字体 */
}

/* #website-name::before {
    content: '';
    display: inline-block;
    width: 40px;
    height: 40px;
    background: url('https://img.sprunki.net/image/24304.webp') no-repeat center center;
    background-size: cover;
    margin-right: 15px;
} */

#nav-menu a {
    color: #fff;
    text-decoration: none;
    margin: 0 20px; /* 增加间距 */
    font-size: 20px; /* 增大字体 */
    font-weight: bold;
    transition: color 0.3s ease, transform 0.3s ease; /* 增加动画效果 */
}

#nav-menu a:hover {
    color: #ffd700;
    transform: scale(1.1); /* 悬停时放大 */
}

#nav-toggle {
    display: none;
    font-size: 28px; /* 增大图标 */
    color: #fff;
    cursor: pointer;
}

/* 游戏区域样式 */
#game-section {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px auto;
}

#game-notice {
    margin-bottom: 20px;
}

#game-notice h1 {
    font-size: 2rem;
    margin-bottom: 10px;
}

#game-notice p {
    font-size: 1rem;
    color: #666;
}

#game-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    overflow: hidden;
    border-radius: 8px;
}

#game_iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

/* 游戏列表样式 */
#game-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

#game-list div {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

#game-list img {
    width: 100%;
    height: 150px; /* 设置固定高度 */
    object-fit: cover; /* 确保图片填充整个区域 */
    display: block;
}

#game-list p {
    padding: 10px;
    font-size: 1rem;
    color: #333;
    flex: 1; /* 确保标题部分占据剩余空间 */
    text-align: center; /* 居中对齐标题 */
}

/* 页尾样式 */
footer {
    background: linear-gradient(90deg, #ff6600, #ff8c42); /* 渐变色背景 */
    color: #fff;
    padding: 25px 0; /* 增加高度 */
    margin-top: 20px;
    text-align: center;
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.2); /* 增加阴影 */
}

.footer-content p {
    font-size: 18px; /* 增大字体 */
    font-weight: bold;
    margin: 0;
    font-family: 'Arial Black', sans-serif; /* 使用现代感强的字体 */
}
/* 响应式样式 */
@media (max-width: 768px) {
    #nav-toggle {
        display: block;
    }

    #nav-menu {
        display: none;
        flex-direction: column;
        width: 100%;
        position: absolute;
        top: 42px; /* 调整位置 */
        left: 0;
        background-color: #222;
        padding: 15px 0; /* 增加间距 */
        z-index: 10;
    }

    #nav-menu a {
        margin: 15px 0; /* 增加间距 */
        text-align: center;
    }

    #nav-menu.active {
        display: flex;
    }

    #game-container {
        padding-bottom: 100%; /* Adjust aspect ratio for smaller screens */
    }

    #game-list {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* 在小屏幕上，最小宽度调整为150px */
    }
}

@media (max-width: 480px) {
    #game-list {
        grid-template-columns: repeat(2, 1fr); /* 在最小分辨率下，每行显示两个项目 */
    }

    #website-name {
        font-size: 24px; /* 调整字体大小 */
    }

    #nav-menu a {
        font-size: 18px; /* 调整字体大小 */
    }

    .footer-content p {
        font-size: 16px; /* 调整字体大小 */
    }
}
</style>
