<header>
    <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
    <h1>Dashboard</h1>
</header>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #333;
    color: white;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
}

header h1 {
    margin: 0;
}

.toggle-btn {
    background-color: transparent;
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    display: none;
}

@media (max-width: 768px) {
    .toggle-btn {
        display: block;
    }
}
</style>