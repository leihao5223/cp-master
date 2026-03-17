<?php
require_once '../xy_config.php';

// 简单的管理员验证（你需要根据实际调整）
session_start();
if (!isset($_SESSION['admin_id'])) {
    die('请先登录后台');
}

// 处理修改时间的请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_time'])) {
    $table = $_POST['table'];
    $id_field = $_POST['id_field'];
    $id_value = $_POST['id_value'];
    $time_field = $_POST['time_field'];
    $new_time = $_POST['new_time'];
    
    // 验证时间格式
    if (strtotime($new_time) === false) {
        $error = "时间格式不正确，请使用 YYYY-MM-DD HH:MM:SS 格式";
    } else {
        // 更新数据库
        $sql = "UPDATE `$table` SET `$time_field` = '$new_time' WHERE `$id_field` = '$id_value'";
        if (mysql_query($sql)) {
            $success = "修改成功！";
        } else {
            $error = "修改失败：" . mysql_error();
        }
    }
}

// 获取所有需要修改时间的表
$tables = [
    'xy_bets' => '投注记录',
    'xy_recharges' => '充值记录',
    'xy_withdraws' => '提现记录',
    'xy_members' => '会员表',
    'xy_orders' => '订单表',
];

// 当前选中的表
$current_table = isset($_GET['table']) ? $_GET['table'] : 'xy_bets';

// 查询当前表的数据
$sql = "SELECT * FROM `$current_table` ORDER BY id DESC LIMIT 50";
$result = mysql_query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>时间修改工具 - 后台</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1a1a1a; border-bottom: 2px solid #00D4FF; padding-bottom: 10px; }
        .table-switcher { margin: 20px 0; }
        .table-switcher a { display: inline-block; padding: 8px 15px; margin-right: 5px; background: #e9ecef; color: #333; text-decoration: none; border-radius: 4px; }
        .table-switcher a.active { background: #00D4FF; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        tr:hover { background: #f8f9fa; }
        .time-input { width: 160px; padding: 5px; border: 1px solid #ced4da; border-radius: 3px; font-family: monospace; }
        .edit-btn { background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .edit-btn:hover { background: #218838; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .note { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin: 20px 0; }
        code { background: #e9ecef; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⏱️ 时间修改工具</h1>
        
        <div class="note">
            <strong>⚠️ 使用说明：</strong> 此工具用于直接修改数据库中的时间记录，修改后前端显示的时间会立即变化。
            请谨慎操作，建议修改前先备份数据。
        </div>
        
        <!-- 表切换 -->
        <div class="table-switcher">
            <?php foreach ($tables as $table_key => $table_name): ?>
                <a href="?table=<?php echo $table_key; ?>" class="<?php echo $current_table == $table_key ? 'active' : ''; ?>">
                    <?php echo $table_name; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- 数据表格 -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th>内容</th>
                    <th>金额</th>
                    <th>当前时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysql_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <?php 
                        // 尝试获取用户信息
                        if (isset($row['user_id'])) {
                            $user_id = $row['user_id'];
                            $user_sql = "SELECT username FROM xy_members WHERE uid = $user_id";
                            $user_res = mysql_query($user_sql);
                            $user = mysql_fetch_assoc($user_res);
                            echo $user ? $user['username'] : "ID: $user_id";
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        // 根据不同的表显示不同内容
                        if ($current_table == 'xy_bets') {
                            echo "期号: " . ($row['issue'] ?? '') . " 号码: " . ($row['numbers'] ?? '');
                        } elseif ($current_table == 'xy_recharges') {
                            echo "充值方式: " . ($row['method'] ?? '');
                        } elseif ($current_table == 'xy_withdraws') {
                            echo "提现方式: " . ($row['method'] ?? '');
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        if (isset($row['amount'])) {
                            echo "¥" . $row['amount'];
                        } elseif (isset($row['money'])) {
                            echo "¥" . $row['money'];
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        // 找出时间字段
                        $time_field = null;
                        $possible_fields = ['addtime', 'bet_time', 'recharge_time', 'withdraw_time', 'create_time', 'update_time', 'time'];
                        foreach ($possible_fields as $field) {
                            if (isset($row[$field]) && $row[$field] != '0000-00-00 00:00:00') {
                                $time_field = $field;
                                echo $row[$field];
                                break;
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($time_field): ?>
                        <form method="POST" style="display: flex; gap: 5px;" onsubmit="return confirm('确定要修改这条记录的时间吗？')">
                            <input type="hidden" name="table" value="<?php echo $current_table; ?>">
                            <input type="hidden" name="id_field" value="id">
                            <input type="hidden" name="id_value" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="time_field" value="<?php echo $time_field; ?>">
                            <input type="datetime-local" name="new_time" class="time-input" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($row[$time_field])); ?>">
                            <button type="submit" name="update_time" class="edit-btn">修改</button>
                        </form>
                        <?php else: ?>
                        无时间字段
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; color: #666; font-size: 12px;">
            * 显示最近50条记录，如需修改更早的记录请直接操作数据库
        </div>
    </div>
</body>
</html>