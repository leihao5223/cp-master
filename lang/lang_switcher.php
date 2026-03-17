<?php
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'zh';
?>
<div class="language-selector" style="position: fixed; top: 10px; right: 10px; z-index: 9999; background: rgba(26,31,53,0.9); padding: 10px 15px; border-radius: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
    <a href="?lang=zh" style="color: <?php echo $current_lang == 'zh' ? '#00D4FF' : '#fff'; ?>; text-decoration: none; margin: 0 5px; font-weight: <?php echo $current_lang == 'zh' ? 'bold' : 'normal'; ?>;">中文</a>
    <span style="color: #666;">|</span>
    <a href="?lang=en" style="color: <?php echo $current_lang == 'en' ? '#00D4FF' : '#fff'; ?>; text-decoration: none; margin: 0 5px; font-weight: <?php echo $current_lang == 'en' ? 'bold' : 'normal'; ?>;">English</a>
</div>