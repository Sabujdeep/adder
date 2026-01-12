<!-- <?php
include "../db_conn.php";  // Connecting to the database
session_start();
?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Student Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Inter", "Segoe UI", sans-serif;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container-main {
            width: 100%;
            max-width: 600px;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 60px 40px;
            text-align: center;
            backdrop-filter: blur(10px);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 64px;
            margin-bottom: 16px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .logo-subtitle {
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
        }

        h1 {
            font-size: 32px;
            color: #111827;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .welcome-text {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .actions-section {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-link:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-link span {
            position: relative;
            z-index: 1;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-action:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2, #667eea);
        }

        .btn-secondary-action {
            background: #f3f4f6;
            color: #374151;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .btn-secondary-action:hover {
            background: #e5e7eb;
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .btn-logout {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-logout:hover {
            background: #fecaca;
            transform: translateY(-4px);
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }

        .footer-text {
            color: #9ca3af;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .feature-item {
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: #f0f4ff;
            border-color: #667eea;
        }

        .feature-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .feature-title {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 600px) {
            .dashboard-card {
                padding: 40px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .logo-text {
                font-size: 24px;
            }

            .welcome-text {
                font-size: 14px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .actions-section {
                gap: 12px;
            }

            .btn-link {
                padding: 12px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="dashboard-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">👨‍🎓</div>
                <div class="logo-text">Student Hub</div>
                <div class="logo-subtitle">Management System</div>
            </div>

            <!-- Welcome Section -->
            <h1>Welcome Back! 👋</h1>
            <p class="welcome-text">
                Manage your student database with ease. Access all the student and their documents from here.
            </p>

          
            <!-- Divider -->
            <div class="divider"></div>

            <!-- Action Buttons -->
            <div class="actions-section">
                <a href="manage_student.php" class="btn-link btn-primary-action">
                    <span>📋 Manage Students</span>
                </a>
                <a href="logout.php" class="btn-link btn-logout">
                    <span>🚪 Logout</span>
                </a>
            </div>

            <!-- Footer -->
            <div class="footer-text">
                
            </div>
        </div>
    </div>
</body>
</html>