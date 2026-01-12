<!-- <?php
include "../db_conn.php";  // Connecting to the database
session_start();
?> -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Details - User Management</title>
    <!-- Bootstrap 5 CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./Styles/style.css">
    <style>
.btn-link {
    display: inline-block;
    text-decoration: none;
    background: #4f46e5;        /* Purple button */
    color: #fff;                /* White text */
    padding: 10px 18px;         /* Some padding */
    border-radius: 8px;         /* Rounded corners */
    font-weight: 500;
    font-size: 14px;
    transition: 0.3s;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.btn-link:hover {
    background: #4338ca;       /* Darker purple on hover */
    box-shadow: 0 6px 10px rgba(0,0,0,0.15);
    transform: translateY(-2px); /* subtle lift effect */
}
</style>
  <style>
   
  </style>
  </head>
  <body>
    <div class="mainContainer" >
      <div class="container mt-5 text-center">
        <h1>Welcome!</h1>
        <p>
<a href="manage_student.php" class="btn-link">Manage Students</a>
          <a href="logout.php" class="btn-link">Logout</a>
    </p>
        </div>
        
  </body>
</html>