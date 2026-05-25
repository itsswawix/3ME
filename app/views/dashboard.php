<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Dashboard";
$activeMenu = "Dashboard";
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? 'Employee';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - 3ME HR System</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: radial-gradient(circle at 20% 30%, #eef2ff, #e0e7ff);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            font-size: 13px;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .main-content {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            max-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
        }

        .page-header h1 i {
            color: #4f46e5;
            margin-right: 8px;
        }

        .welcome-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            font-size: 28px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .welcome-card p {
            font-size: 16px;
            color: #64748b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .stat-info p {
            font-size: 13px;
            color: #64748b;
        }

        .quick-actions {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
        }

        .quick-actions h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .action-btn {
            padding: 16px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            text-decoration: none;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #f8fafc;
            border-color: #4f46e5;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 20px;
            color: #4f46e5;
        }

        .action-btn span {
            font-weight: 500;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="app-layout">
    
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-home"></i> Dashboard</h1>
        </div>

        <div class="welcome-card">
            <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
            <p>You are logged in as <strong><?php echo htmlspecialchars($userRole); ?></strong></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>247</h3>
                    <p>Total Employees</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <h3>235</h3>
                    <p>Active Employees</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>12</h3>
                    <p>On Leave Today</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <h3>8</h3>
                    <p>New Hires This Month</p>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-grid">
                <a href="employee.php" class="action-btn">
                    <i class="fas fa-users"></i>
                    <span>Manage Employees</span>
                </a>
                <a href="attendance.php" class="action-btn">
                    <i class="fas fa-clock"></i>
                    <span>Time & Attendance</span>
                </a>
                <a href="leave.php" class="action-btn">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Leave Management</span>
                </a>
                <a href="recruitment.php" class="action-btn">
                    <i class="fas fa-user-graduate"></i>
                    <span>Recruitment</span>
                </a>
                <a href="benefit.php" class="action-btn">
                    <i class="fas fa-shield-alt"></i>
                    <span>Benefits & Claims</span>
                </a>
                <a href="settings.php" class="action-btn">
                    <i class="fas fa-cog"></i>
                    <span>System Settings</span>
                </a>
            </div>
        </div>
    </main>
</div>

<script>
    console.log('✅ Dashboard loaded - User: <?php echo $userName; ?>');
</script>

</body>
</html>
