<?php
add_to_hook('head_bottom', function() {
    ?>
    <style type="text/css">
    .hover-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        object-fit: cover;
        pointer-events: none;
    }
    </style>
    <?php
});

add_to_hook('footer', function() {
    ?>
    <div id="vhp-parameter" 
         data-debug="<?php echo get_plugin_pref_bool('video-hover-play', 'enable_debug', false) ? '1' : '0'; ?>" 
         data-thumbnail-selector="<?php echo get_plugin_pref('video-hover-play', 'thumbnail_selector', false) ? get_plugin_pref('video-hover-play', 'thumbnail_selector', false) : 'a > .list-game > .list-thumbnail'; ?>" 
         style="display: none;"></div>
    <script type="text/javascript" src="<?php echo DOMAIN . PLUGIN_PATH ?>video-hover-play/hover-video.js"></script>
    <?php
});
?>