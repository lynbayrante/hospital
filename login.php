<?php
// login.php
session_start();
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            // Query the admin table using PDO connection ($pdo from db.php)
            $stmt = $pdo->prepare("SELECT id, username, password, FirstName, LastName FROM admin WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // Check if user exists and password matches plain-text or hashed password
            if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['FirstName'];
                $_SESSION['last_name'] = $user['LastName'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hospital System</title>
    <!-- Bootstrap Icons para sa Medical Icon at Eye Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            /* Hospital-themed smooth background gradient */
            background: linear-gradient(135deg, #f607b2 0%, #df06f3 50%, #dbeafe 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background light accents */
        body::before,
        body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
        }

        body::before {
            width: 350px;
            height: 350px;
            background: rgba(43, 1, 57, 0.15);
            top: -50px;
            left: -50px;
        }

        body::after {
            width: 400px;
            height: 400px;
            background: rgba(99, 8, 234, 0.15);
            bottom: -80px;
            right: -80px;
        }

        .card {
            position: relative;
            z-index: 1;
            background: #ed22ad;
            width: 100%;
            max-width: 420px;
            padding: 40px 32px;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }

        /* Hospital Logo/Icon Wrapper */
        .brand-icon-wrapper {
            width: 64px;
            height: 64px;
            background-color: #c7178c;
            color: #ecf1f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            font-size: 2rem;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.12);
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        /* Container para sa password input at eye icon */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 12px 42px 12px 16px;
            font-size: 0.95rem;
            color: #0f172a;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        }

        /* Toggle Eye Icon Styling */
        .toggle-password {
            position: absolute;
            right: 14px;
            cursor: pointer;
            color: #64748b;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: #0d6efd;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            background-color: #0d6efd;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card">
    <!-- Hospital Medical Cross Icon -->
    <div class="brand-icon-wrapper">
        <i class="bi bi-hospital"></i>
    </div>

    <h3 class="card-title">Hospital Login</h3>
    
    <?php if ($error): ?>
        <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" style="padding-right: 16px;" required placeholder="admin">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" class="form-control" required placeholder="Enter password">
                <i class="bi bi-eye-slash toggle-password" id="toggleIcon" onclick="togglePasswordVisibility()"></i>
            </div>
        </div>
        <button type="submit" class="btn-primary">Log In</button>
    </form>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    }
}
</script>

</body>
</html>