<?php
// 获取所有分类
$categories = fetch_all_categories();
$current_category = null;

// 检查是否是AJAX请求
$is_ajax = isset($_GET['ajax_request']);

// 确定当前显示的分类
if(!empty($categories)) {
    // 如果有URL参数指定分类
    if(isset($_GET['category_slug'])) {
        $current_category = Category::getBySlug($_GET['category_slug']);
    }
    
    // 如果没有指定或指定分类不存在，使用第一个分类
    if(!$current_category) {
        $current_category = $categories[0];
    }
}

// 获取当前分类的游戏列表
if($current_category) {
    $items_per_page = get_setting_value('category_results_per_page', 12);
    $data = get_game_list_category_id($current_category->id, $items_per_page, 0);
    $games = $data['results'];
    $total_games = $data['totalRows'];
    $total_page = $data['totalPages'];
    
    // 如果是AJAX请求，只返回内容部分
    if(isset($_GET['ajax_request'])) {
        // 确保设置了正确的Content-Type
        header('Content-Type: text/html; charset=utf-8');
        
        if($current_category && !empty($games)) {
            ob_start();
            ?>
            <h1><?php echo esc_string($current_category->name); ?></h1>
            <div class="row">
                <?php foreach ($games as $game): ?>
                    <?php include(TEMPLATE_PATH . '/includes/grid.php'); ?>
                <?php endforeach; ?>
            </div>
            <?php
            echo ob_get_clean();
        } else {
            echo '<div class="alert alert-info"><?php _e("没有找到游戏"); ?></div>';
        }
        exit; // 确保在AJAX响应后终止脚本
    }
}

// 设置页面标题等元信息
if($current_category) {
    $page_title = _t('%a Games', $current_category->name).' | '.SITE_DESCRIPTION;
    $meta_description = isset($current_category->meta_description) && $current_category->meta_description != '' 
        ? $current_category->meta_description 
        : _t('Play %a Games', $current_category->name).' | '.SITE_DESCRIPTION;
}
?>


<?php include TEMPLATE_PATH . "/includes/header.php" ?>
<div class="main-container">
    <!-- 侧边栏 - 分类 -->
    <!-- <aside class="sidebar">
        <h3>游戏分类</h3>
        <?php 
        $categories = fetch_all_categories();
        echo '<ul class="category-list">';
        foreach ($categories as $item) {
            echo '<li><a href="'. get_permalink('category', $item->slug) .'">'. esc_string($item->name) .'</a></li>';
        }
        echo '</ul>';
        ?>
    </aside> -->

    <aside class="sidebar">
        <?php list_categories_sidebar(); ?>
    </aside>

    <!-- 主内容区 -->
    <div class="main-content container" id="category-content">
        <?php if($current_category && !empty($games)): ?>
            <h1><?php echo esc_string($current_category->name); ?></h1>
            <div class="row">
                <?php foreach ($games as $game): ?>
                    <?php include(TEMPLATE_PATH . '/includes/grid.php'); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info"><?php _e('没有找到游戏'); ?></div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categoryLinks = document.querySelectorAll('.load-category');
    
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var slug = this.dataset.slug;
            var contentArea = document.getElementById('category-content');
            
            contentArea.innerHTML = '<div class="loading"><?php _e("Loading..."); ?></div>';
            
            var xhr = new XMLHttpRequest();
            var requestUrl = window.location.pathname + '?category_slug=' + encodeURIComponent(slug) + '&ajax_request=1';
            xhr.open('GET', requestUrl, true);
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    // 直接使用响应文本，不需要解析
                    contentArea.innerHTML = xhr.responseText;
                    history.pushState(null, null, '?category_slug=' + slug);
                } else {
                    contentArea.innerHTML = '<div class="alert alert-danger"><?php _e("请求失败，状态码: "); ?>' + xhr.status + '</div>';
                }
            };
            
            xhr.onerror = function() {
                contentArea.innerHTML = '<div class="alert alert-danger"><?php _e("网络连接错误"); ?></div>';
            };
            
            xhr.send();
        });
    });
    
    window.onpopstate = function() {
        location.reload();
    };
});
</script>
<?php include TEMPLATE_PATH . "/includes/footer.php" ?>

<style>
/* 基础布局样式 */
.main-container {
    display: flex;
    flex-wrap: wrap; /* 允许在移动端换行 */
    gap: 20px;
    margin: 0 8%;
}

.sidebar {
    flex: 0 0 250px; /* 固定宽度250px */
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
}

.main-content {
    flex: 1; /* 占据剩余空间 */
    min-width: 0; /* 防止内容溢出 */
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-list li {
    margin-bottom: 10px;
}

.category-list a {
    display: block;
    padding: 8px 12px;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s;
}

.category-list a:hover {
    background: #e0e0e0;
    color: #000;
}

/* 移动端响应式设计 */
@media (max-width: 768px) {
    .main-container {
        flex-direction: column; /* 改为垂直排列 */
        margin: 0;
    }
    
    .sidebar {
        flex: 0 0 auto; /* 自动高度 */
        width: 100%; /* 全宽 */
        margin-bottom: 20px;
    }
    
    .category-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .category-list li {
        margin-bottom: 0;
    }
    
    .category-list a {
        padding: 6px 10px;
        background: #eee;
    }
}
</style>