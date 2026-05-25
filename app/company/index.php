<?php
/**
 * Company Portal - Main Landing Page
 * Provides access to job requisition posting and attendance file upload
 */

$pageTitle = "Company Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
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

        .portal-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .portal-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .portal-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .portal-header p {
            font-size: 14px;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .portal-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
            transition: all 0.3s ease;
            text-align: center;
        }

        .portal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }

        .portal-card-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            color: white;
        }

        .job-portal .portal-card-icon {
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
        }

        .attendance-portal .portal-card-icon {
            background: linear-gradient(145deg, #10b981, #059669);
        }

        .portal-card h2 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .portal-card p {
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 13px;
        }

        .portal-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: white;
        }

        .job-portal .portal-btn {
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
        }

        .job-portal .portal-btn:hover {
            background: linear-gradient(145deg, #4338ca, #6d28d9);
            transform: translateY(-2px);
        }

        .attendance-portal .portal-btn {
            background: linear-gradient(145deg, #10b981, #059669);
        }

        .attendance-portal .portal-btn:hover {
            background: linear-gradient(145deg, #059669, #047857);
            transform: translateY(-2px);
        }

        .features-list {
            list-style: none;
            text-align: left;
            margin: 24px 0;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            color: #475569;
        }

        .features-list li i {
            color: #10b981;
            font-size: 16px;
            width: 20px;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 60px;
        }

        .stat-card {
            background: rgba(255,255,255,0.9);
            padding: 10px 18px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: center;
        }

        .stat-card i {
            color: #4f46e5;
            font-size: 16px;
        }

        .stat-card .stat-number {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .stat-card .stat-label {
            color: #64748b;
            font-size: 11px;
            margin-left: 6px;
        }

        .footer-info {
            text-align: center;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.3);
            color: #64748b;
        }

        @media (max-width: 768px) {
            .portal-container {
                padding: 20px;
            }
            
            .portal-header h1 {
                font-size: 28px;
            }
            
            .portal-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            
            .portal-card {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="portal-container">
        <!-- Header -->
        <div class="portal-header">
            <h1><i class="fas fa-building" style="color: #4f46e5; margin-right: 12px;"></i>Company Portal</h1>
            <p>Access job requisition posting and attendance management tools for your organization</p>
        </div>

        <!-- Main Portal Cards -->
        <div class="portal-grid">
            <!-- Job Requisition Portal -->
            <div class="portal-card job-portal">
                <div class="portal-card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h2>Job Requisition Portal</h2>
                <p>Post new job openings, manage requisitions, and track hiring progress for your organization.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Create and submit job requisitions</li>
                    <li><i class="fas fa-check"></i> Track approval status</li>
                    <li><i class="fas fa-check"></i> Manage job postings</li>
                    <li><i class="fas fa-check"></i> View hiring analytics</li>
                </ul>
                
                <a href="job-portal.php" class="portal-btn">
                    <i class="fas fa-arrow-right"></i>
                    Access Job Portal
                </a>
            </div>

            <!-- Attendance Upload Portal -->
            <div class="portal-card attendance-portal">
                <div class="portal-card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h2>Attendance Upload Portal</h2>
                <p>Upload attendance data files, manage time records, and process employee attendance information.</p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check"></i> Upload CSV/Excel files</li>
                    <li><i class="fas fa-check"></i> Preview data before import</li>
                    <li><i class="fas fa-check"></i> Bulk attendance processing</li>
                    <li><i class="fas fa-check"></i> Error validation & reporting</li>
                </ul>
                
                <a href="attendance-upload.php" class="portal-btn">
                    <i class="fas fa-arrow-right"></i>
                    Access Upload Portal
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <i class="fas fa-clipboard-list"></i>
                <span class="stat-number">24</span>
                <small class="stat-label">Active Requisitions</small>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <span class="stat-number">1,247</span>
                <small class="stat-label">Employees</small>
            </div>
            <div class="stat-card">
                <i class="fas fa-upload"></i>
                <span class="stat-number">156</span>
                <small class="stat-label">Files Uploaded</small>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <span class="stat-number">98.5%</span>
                <small class="stat-label">Success Rate</small>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <p><i class="fas fa-shield-alt" style="color: #10b981; margin-right: 8px;"></i>Secure • Reliable • Easy to Use</p>
            <p style="margin-top: 8px; font-size: 12px;">© 2024 Company Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>