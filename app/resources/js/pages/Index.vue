<template>
    <div>
        <header class="container">
            <nav>
                <a href="#" id="website-name" title="Incredibox Mustard">Incredibox Mustard</a>
                <div id="nav-toggle" onclick="toggleMenu()">&#9776;</div>
                <div id="nav-menu">
                    <a href="#" title="Home">Home111</a>
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
                <div id="game-list">
                    <div v-for="game in games" :key="game.id" class="game-item">
                        <a :href="`/game/${encodeURIComponent(game.title)}`" target="_self" :title="game.title">
                            <img :src="game.coverUrl" :alt="game.title" loading="lazy" :title="game.title">
                            <p>{{ game.title }}</p>
                        </a>
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

<script setup>
import { ref, onMounted } from 'vue';

const games = ref([]);

onMounted(() => {
    fetch('https://incredibox.aluo18.top/api/lists')
        .then(response => response.json())
        .then(data => {
            games.value = data;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});
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
    background: linear-gradient(90deg, #ff6600, #ff8c42);
    color: #fff;
    padding: 15px 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: sticky;
    top: 0;
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#website-name {
    font-size: 28px;
    font-weight: bold;
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    font-family: 'Arial Black', sans-serif;
}

#nav-menu a {
    color: #fff;
    text-decoration: none;
    margin: 0 20px;
    font-size: 20px;
    font-weight: bold;
    transition: color 0.3s ease, transform 0.3s ease;
}

#nav-menu a:hover {
    color: #ffd700;
    transform: scale(1.1);
}

#nav-toggle {
    display: none;
    font-size: 28px;
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
    padding-bottom: 56.25%;
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
    height: 150px;
    object-fit: cover;
    display: block;
}

#game-list p {
    padding: 10px;
    font-size: 1rem;
    color: #333;
    flex: 1;
    text-align: center;
}

/* 页尾样式 */
footer {
    background: linear-gradient(90deg, #ff6600, #ff8c42);
    color: #fff;
    padding: 25px 0;
    margin-top: 20px;
    text-align: center;
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.2);
}

.footer-content p {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
    font-family: 'Arial Black', sans-serif;
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
        top: 42px;
        left: 0;
        background-color: #222;
        padding: 15px 0;
        z-index: 10;
    }

    #nav-menu a {
        margin: 15px 0;
        text-align: center;
    }

    #nav-menu.active {
        display: flex;
    }

    #game-container {
        padding-bottom: 100%;
    }

    #game-list {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}

@media (max-width: 480px) {
    #game-list {
        grid-template-columns: repeat(2, 1fr);
    }

    #website-name {
        font-size: 24px;
    }

    #nav-menu a {
        font-size: 18px;
    }

    .footer-content p {
        font-size: 16px;
    }
}
</style>
