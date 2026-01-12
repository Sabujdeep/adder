<?php
session_start();
include "../db_conn.php";  // your database connection

// Fetch all students
$sql = "SELECT * FROM student ORDER BY student_id DESC";
$result = $conn->query($sql);
$student_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Inter", "Segoe UI", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 16px 30px;
            color: #1f2937;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-weight: 700;
            font-size: 22px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
        }

        .nav-links li a {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-links li a:hover {
            background: #f3f4f6;
            color: #667eea;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #fff;
            margin: 0 0 30px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .page-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .stats {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .stat-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .add-btn {
            text-decoration: none;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 16px 20px;
            background: linear-gradient(135deg, #d1fae5, #ecfdf5);
            color: #065f46;
            border-left: 4px solid #10b981;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead {
            background: linear-gradient(135deg, #f3f4f6, #ffffff);
        }

        thead th {
            padding: 16px;
            text-align: left;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        tbody td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
            box-shadow: inset 0 0 0 1px #e5e7eb;
        }

        .student-name {
            font-weight: 600;
            color: #111827;
        }

        .student-id {
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
        }

        .cell-label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .action-btn {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .edit-btn {
            background: #dbeafe;
            color: #1e40af;
        }

        .edit-btn:hover {
            background: #bfdbfe;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #fee2e2;
            color: #991b1b;
        }

        .delete-btn:hover {
            background: #fecaca;
            transform: translateY(-1px);
        }

        .file-link {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .file-link:hover {
            background: #eff0ff;
            text-decoration: underline;
        }

        .no-file {
            color: #9ca3af;
            font-size: 13px;
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .empty-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .empty-text {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .empty-action {
            text-decoration: none;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .empty-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.4);
        }

        .responsive-hidden {
            display: table-cell;
        }

        @media (max-width: 1024px) {
            .responsive-hidden {
                display: none;
            }

            thead th {
                padding: 12px;
                font-size: 11px;
            }

            tbody td {
                padding: 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .top-section {
                flex-direction: column;
            }

            .page-card {
                padding: 20px;
            }

            h1 {
                font-size: 24px;
            }

            .table-wrapper {
                margin: -20px;
                padding: 20px;
                overflow-x: auto;
            }

            table {
                font-size: 12px;
            }

            thead th {
                padding: 10px;
            }

            tbody td {
                padding: 10px;
            }

            .actions-cell {
                flex-direction: column;
                gap: 4px;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">👨‍🎓 Student Management</div>
        <ul class="nav-links">
            <li><a href="index.php">🏠 Dashboard</a></li>
            <li><a href="manage_student.php">📋 Manage Students</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>📚 Manage Students</h1>

        <div class="page-card">
            <div class="top-section">
                <div class="stats">
                    <div class="stat-badge">
                        👥 Total Students: <strong><?php echo $student_count; ?></strong>
                    </div>
                </div>
                <a href="add_student.php" class="add-btn">➕ Add New Student</a>
            </div>

            <?php
                if (!empty($_SESSION['student_msg'])) {
                    echo '<div class="alert">' . htmlspecialchars($_SESSION['student_msg']) . '</div>';
                    $_SESSION['student_msg'] = "";
                }
            ?>

            <?php if ($student_count > 0): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th class="responsive-hidden">Email</th>
                                <th class="responsive-hidden">Phone</th>
                                <th class="responsive-hidden">Joining Year</th>
                                <th class="responsive-hidden">Aadhaar</th>
                                <th>Document</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <span class="student-id">#<?php echo str_pad($row['student_id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td>
                                        <div class="student-name"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                                        <span class="cell-label"><?php echo htmlspecialchars($row['dob'] ?? 'N/A'); ?></span>
                                    </td>
                                    <td class="responsive-hidden">
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color: #667eea; text-decoration: none;">
                                            <?php echo htmlspecialchars($row['email']); ?>
                                        </a>
                                    </td>
                                    <td class="responsive-hidden">
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </td>
                                    <td class="responsive-hidden">
                                        <span class="stat-badge" style="background: #f3f4f6; color: #374151; display: inline-block;">
                                            <?php echo htmlspecialchars($row['joining_year']); ?>
                                        </span>
                                    </td>
                                    <td class="responsive-hidden">
                                        <span style="font-family: monospace; font-size: 12px; color: #374151;">
                                            <?php echo htmlspecialchars($row['adhaar_number']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['file_upload'])): ?>
                                            <a href="../uploads/students/<?php echo htmlspecialchars($row['file_upload']); ?>" target="_blank" class="file-link">
                                                📄 View
                                            </a>
                                        <?php else: ?>
                                            <span class="no-file">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="add_student.php?action=edit&student_id=<?php echo $row['student_id']; ?>" class="action-btn edit-btn">✏️ Edit</a>
                                            <a href="delete_student.php?student_id=<?php echo $row['student_id']; ?>" class="action-btn delete-btn" 
                                               onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">🗑️ Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📚</div>
                    <div class="empty-title">No Students Yet</div>
                    <div class="empty-text">Get started by adding your first student to the system</div>
                    <a href="add_student.php" class="empty-action">➕ Add Your First Student</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>