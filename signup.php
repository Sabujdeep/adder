<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    /* Global */
    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #4f46e5, #6366f1);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* Card */
    .signup-card {
      background: #fff;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    h2 {
      font-weight: 600;
      margin-bottom: 30px;
      color: #1f2937;
    }

    /* Inputs */
    .form-control {
      border-radius: 12px;
      padding: 12px;
      font-size: 14px;
      border: 1px solid #d1d5db;
      transition: all 0.2s;
    }

    .form-control:focus {
      border-color: #4f46e5;
      box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #4f46e5, #6366f1);
      border: none;
      border-radius: 12px;
      padding: 10px 20px;
      font-size: 16px;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }

    .btn-outline-secondary {
      border-radius: 12px;
      padding: 6px 14px;
      font-size: 14px;
    }

    .form-text {
      font-size: 13px;
      color: #6b7280;
    }

    .login-link-container {
      margin-top: 15px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="signup-card">
    <h2>Create Account</h2>

    <form id="signupForm" action="adminhandler.php" method="POST" autocomplete="off">
      <input type="hidden" name="action" value="signup">

      <div class="mb-3 text-start">
        <label for="user_name" class="form-label">User Name</label>
        <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Enter username" required>
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>

      <div class="login-link-container">
        <span class="form-text me-2">Already have an account?</span>
        <a href="login.php" class="btn btn-outline-secondary btn-sm">Login</a>
      </div>
    </form>
  </div>

  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="./signup.js"></script>
</body>

</html>
