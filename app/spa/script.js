document.addEventListener('DOMContentLoaded', function() {
    // 拦截导航链接点击事件
    document.querySelectorAll('.nav-link, .sidebar-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            loadPage(page);
            
            // 更新URL而不刷新页面
            history.pushState({page: page}, '', `?page=${page}`);
        });
    });
    
    // 处理浏览器前进/后退
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            loadPage(e.state.page);
        }
    });
    
    // 加载页面内容的函数
    function loadPage(page) {
        fetch(`pages/${page}.php`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('main-content').innerHTML = html;
                
                // 更新活动菜单项样式
                document.querySelectorAll('.nav-link, .sidebar-link').forEach(link => {
                    link.classList.toggle('active', link.getAttribute('data-page') === page);
                });
            })
            .catch(err => console.error('加载页面失败:', err));
    }
});