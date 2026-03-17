<?php
require_once '../xy_config.php';

// 简单的管理员验证（你需要根据实际调整）
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// 获取一些统计数据
$total_users = mysql_query("SELECT COUNT(*) as count FROM xy_members")->fetch_assoc()['count'];
$today_bets = mysql_query("SELECT COUNT(*) as count FROM xy_bets WHERE DATE(addtime) = CURDATE()")->fetch_assoc()['count'];
$pending_withdraws = mysql_query("SELECT COUNT(*) as count FROM xy_withdraws WHERE status=0")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - 首页</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: #f0f2f5;
            color: #333;
        }
        
        .layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* 左侧菜单 */
        .sidebar {
            width: 260px;
            background: #1a1f35;
            color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid #2a2f45;
        }
        
        .sidebar-header h2 {
            color: #00D4FF;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            color: #8a8fb0;
            font-size: 14px;
        }
        
        .menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 25px;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            color: #b0b5d6;
            text-decoration: none;
            display: block;
        }
        
        .menu-item:hover {
            background: #2a2f45;
            color: #fff;
        }
        
        .menu-item.active {
            background: #2a2f45;
            border-left-color: #00D4FF;
            color: #fff;
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            display: inline-block;
        }
        
        /* 右侧内容区 */
        .main {
            flex: 1;
            background: #f0f2f5;
        }
        
        .header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title h1 {
            color: #1a1f35;
            font-size: 24px;
        }
        
        .header-title p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info span {
            color: #1a1f35;
            font-weight: 500;
        }
        
        .logout-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #ff6b81;
        }
        
        /* 内容区 */
        .content {
            padding: 30px;
        }
        
        /* 统计卡片 */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-title {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-number {
            color: #1a1f35;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-desc {
            color: #00D4FF;
            font-size: 12px;
        }
        
        /* 快捷操作 */
        .quick-actions {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 30px;
        }
        
        .quick-actions h3 {
            color: #1a1f35;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: #f8f9fa;
            color: #1a1f35;
            text-decoration: none;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e9ecef;
            display: block;
        }
        
        .action-btn:hover {
            background: #00D4FF;
            color: white;
            border-color: #00D4FF;
        }
        
        .action-btn i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }
        
        .action-btn span {
            font-size: 14px;
            font-weight: 500;
        }
        
        /* ⭐ 特别标注的新工具 */
        .action-btn.special {
            background: #1a1f35;
            color: #00D4FF;
            border: 2px solid #00D4FF;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn.special:hover {
            background: #00D4FF;
            color: #1a1f35;
        }
        
        .action-btn.special::after {
            content: "NEW";
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 10px;
            transform: rotate(15deg);
        }
        
        /* 最近活动 */
        .recent-activity {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 30px;
        }
        
        .recent-activity h3 {
            color: #1a1f35;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #00D4FF;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .activity-time {
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- 左侧菜单 -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>彩票后台</h2>
                <p>Admin Panel v1.0</p>
            </div>
            
            <div class="menu">
                <a href="index.php" class="menu-item active">
                    <i>📊</i> 仪表盘
                </a>
                <a href="users.php" class="menu-item">
                    <i>👥</i> 用户管理
                </a>
                <a href="bets.php" class="menu-item">
                    <i>🎲</i> 投注记录
                </a>
                <a href="recharges.php" class="menu-item">
                    <i>💰</i> 充值管理
                </a>
                <a href="withdraws.php" class="menu-item">
                    <i>💸</i> 提现管理
                </a>
                <a href="games.php" class="menu-item">
                    <i>🎮</i> 游戏管理
                </a>
                <a href="settings.php" class="menu-item">
                    <i>⚙️</i> 系统设置
                </a>
                <!-- ⭐ 新加的时间修改工具 -->
                <a href="edit_time.php" class="menu-item" style="color: #00D4FF; font-weight: bold; background: #2a2f45;">
                    <i>⏱️</i> 时间修改工具
                </a>
                <a href="reports.php" class="menu-item">
                    <i>📈</i> 报表统计
                </a>
                <a href="logs.php" class="menu-item">
                    <i>📋</i> 系统日志
                </a>
            </div>
        </div>
        
        <!-- 右侧内容 -->
        <div class="main">
            <div class="header">
                <div class="header-title">
                    <h1>仪表盘</h1>
                    <p>欢迎回来，管理员</p>
                </div>
                <div class="user-info">
                    <span>管理员</span>
                    <a href="logout.php" class="logout-btn">退出登录</a>
                </div>
            </div>
            
            <div class="content">
                <!-- 统计卡片 -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title">总用户数</div>
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-desc">较昨日 +12%</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">今日投注</div>
                        <div class="stat-number"><?php echo $today_bets; ?></div>
                        <div class="stat-desc">笔数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">待处理提现</div>
                        <div class="stat-number"><?php echo $pending_withdraws; ?></div>
                        <div class="stat-desc">笔</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">今日充值</div>
                        <div class="stat-number">¥ 12,345</div>
                        <div class="stat-desc">较昨日 +5%</div>
                    </div>
                </div>
                
                <!-- 快捷操作区 -->
                <div class="quick-actions">
                    <h3>快捷操作</h3>
                    <div class="actions-grid">
                        <a href="add_user.php" class="action-btn">
                            <i>➕</i>
                            <span>新增用户</span>
                        </a>
                        <a href="recharges.php?status=pending" class="action-btn">
                            <i>💰</i>
                            <span>待处理充值</span>
                        </a>
                        <a href="withdraws.php?status=pending" class="action-btn">
                            <i>💸</i>
                            <span>待处理提现</span>
                        </a>
                        <a href="edit_time.php" class="action-btn special">
                            <i>⏱️</i>
                            <span>时间修改工具</span>
                        </a>
                        <a href="reports.php" class="action-btn">
                            <i>📊</i>
                            <span>生成报表</span>
                        </a>
                        <a href="settings.php" class="action-btn">
                            <i>⚙️</i>
                            <span>系统设置</span>
                        </a>
                    </div>
                </div>
                
                <!-- 最近活动 -->
                <div class="recent-activity">
                    <h3>最近活动</h3>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon">💰</div>
                            <div class="activity-content">
                                <div class="activity-title">用户 user123 充值 1000元</div>
                                <div class="activity-time">5分钟前</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">💸</div>
                            <div class="activity-content">
                                <div class="activity-title">用户 test456 申请提现 500元</div>
                                <div class="activity-time">15分钟前</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">🎲</div>
                            <div class="activity-content">
                                <div class="activity-title">新投注记录 200元</div>
                                <div class="activity-time">32分钟前</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon">👤</div>
                            <div class="activity-content">
                                <div class="activity-title">新用户注册：newbie789</div>
                                <div class="activity-time">1小时前</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>