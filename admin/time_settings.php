<?php
require_once '../xy_config.php';
require_once 'admin_auth.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'withdraw_wait' => intval($_POST['withdraw_wait']),
        'deposit_wait' => intval($_POST['deposit_wait']),
        'bet_interval' => intval($_POST['bet_interval']),
        'order_expire' => intval($_POST['order_expire']),
    ];
    
    foreach ($settings as $key => $value) {
        $value = intval($value);
        $sql = "UPDATE xy_time_settings SET setting_value = $value, update_time = NOW() WHERE setting_key = '$key'";
        mysql_query($sql);
    }
    
    $success_msg = $lang['save_success'] ?? '设置已保存！';
}

$settings = [];
$sql = "SELECT setting_key, setting_value FROM xy_time_settings";
$result = mysql_query($sql);
while ($row = mysql_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $conf['language']; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['admin_time_settings'] ?? '时间设置'; ?></title>
    <style>
        body { font-family: Arial; background: #0A0E1B; color: white; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #1A1F35; padding: 30px; border-radius: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #00D4FF; }
        input { width: 100%; padding: 10px; background: #2A2F45; border: 1px solid #00D4FF; color: white; border-radius: 5px; }
        button { background: #00D4FF; color: #0A0E1B; padding: 15px; border: none; width: 100%; font-size: 16px; cursor: pointer; border-radius: 5px; }
        .success { background: #00FF88; color: #0A0E1B; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .note { color: #888; font-size: 12px; margin-top: 20px; }
        .language-switcher { text-align: right; margin-bottom: 20px; }
        .language-switcher a { color: #00D4FF; margin-left: 10px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="language-switcher">
            <a href="?lang=zh">中文</a> | <a href="?lang=en">English</a>
        </div>
        
        <h1>⏱️ <?php echo $lang['admin_time_settings'] ?? '时间设置管理'; ?></h1>
        
        <?php if (isset($success_msg)): ?>
            <div class="success">✅ <?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><?php echo $lang['time_withdraw_wait'] ?? '提现等待时间'; ?> (<?php echo $lang['minutes'] ?? '分钟'; ?>)</label>
                <input type="number" name="withdraw_wait" value="<?php echo $settings['withdraw_wait'] ?? 5; ?>" min="0" max="1440" required>
            </div>
            
            <div class="form-group">
                <label><?php echo $lang['time_deposit_wait'] ?? '充值确认时间'; ?> (<?php echo $lang['minutes'] ?? '分钟'; ?>)</label>
                <input type="number" name="deposit_wait" value="<?php echo $settings['deposit_wait'] ?? 2; ?>" min="0" max="1440" required>
            </div>
            
            <div class="form-group">
                <label><?php echo $lang['time_bet_interval'] ?? '投注间隔'; ?> (<?php echo $lang['minutes'] ?? '分钟'; ?>)</label>
                <input type="number" name="bet_interval" value="<?php echo $settings['bet_interval'] ?? 1; ?>" min="0" max="60" required>
            </div>
            
            <div class="form-group">
                <label><?php echo $lang['time_order_expire'] ?? '订单过期时间'; ?> (<?php echo $lang['minutes'] ?? '分钟'; ?>)</label>
                <input type="number" name="order_expire" value="<?php echo $settings['order_expire'] ?? 30; ?>" min="1" max="1440" required>
            </div>
            
            <button type="submit"><?php echo $lang['save'] ?? '保存设置'; ?></button>
        </form>
        
        <div class="note">
            <?php echo $lang['time_settings_note'] ?? '修改后立即生效，所有用户的时间记录将实时更新'; ?>
        </div>
    </div>
    
    <script>
        document.querySelectorAll('.language-switcher a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = this.getAttribute('href').split('=')[1];
                document.cookie = "lang=" + lang + "; path=/";
                location.reload();
            });
        });
    </script>
</body>
</html>