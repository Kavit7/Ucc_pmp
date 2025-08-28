<?php
use app\widgets\Alert;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PremierProperty - Management Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        
        .full-screen-container {
            height: 80vh;
            width: 70vw;
            display: flex;
            margin-left:auto;
            margin-right:auto;
            margin-top:70px;
            
            
        }
        
        .background-section {
            flex: 0 0 50%;
            position: relative;
            overflow: hidden;
        }
        
        .background-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 255, 0.4), rgba(11, 19, 43, 0.9));
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            color: white;
        }
        .input-group {
    box-shadow: none !important;
    border-radius: 2px;
   
}
.input-group:focus-within {
    
    border-radius: 2px;
}
.form-control:focus {
    box-shadow: none !important;
    border-color: #5A09DE;
    
}
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-right: none;
}
        
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .feature-list {
            margin-top: 2rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .login-section {
            flex: 0 0 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-form {
            width: 100%;
            max-width: 400px;
        }
        
        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 2px;
        }
        
        .btn-primary {
            background-color: #7543D1;
            border: none;
            padding: 0.8rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color:white;
            color:#7543D1;
            border: 2px solid #7543D1 !important;;
                 }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        
        .divider span {
            padding: 0 1rem;
            color: #777;
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .social-btn {
            flex: 1;
            padding: 0.6rem;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        
        .social-btn:hover {
            background: #f8f9fa;
        }
        
        .copyright {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        
        @media (max-width: 992px) {
            .background-section {
                display: none;
            }
            
            .login-section {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <div class="full-screen-container">
        <div class="background-section">
            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Luxury Property">
            <div class="overlay">
                <div class="logo">PremierProperty</div>
                <h2>Expert Management for Your Real Estate Portfolio</h2>
                <p class="lead">Official management portal for PremierProperty assets and operations</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>Manage your property portfolio</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>Track performance metrics</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>Access tenant and lease information</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-toolbox"></i>
                        </div>
                        <div>Submit maintenance requests</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-section">
            <div class="login-form">
                <h2 class="text-center mb-4">Login to Your Account</h2>
                
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                            <span class="input-group-text toggle-password" style="cursor: pointer;"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                        <a href="#" class="float-end" style="color:#7543D1;">Forgot password?</a>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
                
                <div class="divider">
                    <span>Or continue with</span>
                </div>
                
                <div class="social-login">
                    <button class="social-btn">
                        <i class="fab fa-google"></i> Google
                    </button>
                    <button class="social-btn">
                        <i class="fab fa-microsoft"></i> Microsoft
                    </button>
                </div>
                
                <div class="text-center">
                    <p>Don't have an account? <a href="#" style="color:#7543D1;">Contact Administrator</a></p>
                </div>
                
                <div class="copyright">
                    <p>&copy; 2023 PremierProperty. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                alert('Please fill in all fields');
                return;
            }
            
            // Simulate successful login
            alert('Login successful! Redirecting to dashboard...');
            // In a real application, you would redirect to the dashboard page
            // window.location.href = 'dashboard.html';
        });
    </script>
</body>
</html>