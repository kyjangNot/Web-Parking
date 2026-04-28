<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Parkir</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1E3A8A, #3B82F6);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #F9FAFB;
            border-radius: 15px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-title {
            font-weight: 600;
            color: #111827;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6B7280;
        }

        .form-control {
            border-radius: 10px;
            padding-left: 40px;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #6B7280;
        }

        .input-group {
            position: relative;
        }

        .btn-login {
            background-color: #1E3A8A;
            color: white;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #3B82F6;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6B7280;
        }
        img {
    background: white;
    border-radius: 50%;
    padding: 5px;
}
    </style>
</head>
<body>

<div class="login-card">

    <div class="text-center mb-4">
    <img src="../assets/img/logo.png" alt="Logo Sekolah" width="80" class="mb-2">
    <h3 class="login-title">Sistem Parkir</h3>
    <p class="login-subtitle">Silakan login untuk melanjutkan</p>
</div>

    <form method="POST" action="proses_login.php">

        <!-- Username -->
        <div class="mb-3 input-group">
            <i class="bi bi-person input-icon"></i>
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>

        <!-- Password -->
        <div class="mb-3 input-group">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            <i class="bi bi-eye toggle-password" onclick="togglePassword()"></i>
        </div>

        <!-- Button -->
        <button type="submit" class="btn btn-login w-100">
            Login
        </button>

    </form>

</div>

<!-- Script -->
<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const icon = document.querySelector(".toggle-password");

        if (password.type === "password") {
            password.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            password.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>

</body>
</html>