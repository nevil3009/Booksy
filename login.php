<?php
// Start session for login management
session_start();

// Database connection configuration
$dbHost = "localhost";
$dbName = "booksy";
$dbUser = "root";
$dbPass = "";

// Create database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submissions
$signupError = "";
$loginError = "";
$signupSuccess = "";

// Show login or signup form
$showLogin = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle signup
    if (isset($_POST['fullName'])) {
        $fullName = trim($_POST['fullName']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $interest = isset($_POST['interest']) ? trim($_POST['interest']) : "";
        $category = isset($_POST['category']) ? trim($_POST['category']) : "";

        if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword) || empty($category)) {
            $signupError = "All required fields must be filled out";
        } elseif ($password !== $confirmPassword) {
            $signupError = "Passwords do not match";
        } elseif (strlen($password) < 8) {
            $signupError = "Password must be at least 8 characters long";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $signupError = "Password must contain at least one uppercase letter";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $signupError = "Password must contain at least one number";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $signupError = "Email already exists. Please use a different email or login.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, interest, category, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->bind_param("sssss", $fullName, $email, $hashedPassword, $interest, $category);

                if ($stmt->execute()) {
                    $signupSuccess = "Registration successful! You can now log in.";
                    $showLogin = true;
                } else {
                    $signupError = "Registration failed: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
    // Handle login
    // Handle login
// Handle login
if (isset($_POST['loginEmail'])) {
    $email = trim($_POST['loginEmail']);
    $password = $_POST['loginPassword'];

    if (empty($email) || empty($password)) {
        $loginError = "Email and password are required";
        $showLogin = true;
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, email, password, category FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['category'] = $user['category'];
                $_SESSION['logged_in'] = true;
                
                // Check if user is admin
                if ($user['category'] === 'admin') {
                    $_SESSION['is_admin'] = true;
                    header("Location: admin-panel.php"); // Redirect to admin panel
                } else {
                    header("Location: index.php"); // Regular user page
                }
                exit();
            } else {
                $loginError = "Incorrect email or password";
                $showLogin = true;
            }
        } else {
            $loginError = "Incorrect email or password";
            $showLogin = true;
        }
        $stmt->close();
    }
}

// Check if user is already logged in
// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin-panel.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .form-section {
            width: 50%;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        .form-content {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 2.75rem;
            font-weight: 900;
            text-align: center;
            background: linear-gradient(to right, #3b82f6, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            animation: fadeInUp 0.6s ease-out both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            outline: none;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .form-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
            background-color: #fff;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-field {
            padding-right: 4rem;
            /* space for the toggle button */
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #333;
            background: none;
            border: none;
            font-size: 0.9rem;
            padding: 0;
            font-weight: 500;
        }

        select.form-control {
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: url("/Booksy/Assets/login-signup/leer.jpg") no-repeat right 0 center;
        }

        .terms-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .terms-group input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .terms-group a {
            color: #60a5fa;
            text-decoration: none;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background-color: #60a5fa;
            color: white;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #3b82f6;
        }

        .image-section {
            width: 50%;
            position: relative;
            overflow: hidden;
        }

        .illustration {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
            transition: filter 0.5s ease;
        }

        .toggle-form {
            text-align: center;
            margin-top: 1rem;
        }

        .toggle-form span {
            color: #60a5fa;
            cursor: pointer;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }

        .requirement.met {
            color: #22c55e;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .success-message {
            color: #22c55e;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .form-section,
            .image-section {
                width: 100%;
            }

            .image-section {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-section">
            <div class="form-content">
                <form id="signupForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-header">
                        <h2>Join & Read</h2>
                        <?php if (!empty($signupError)): ?>
                            <div class="error-message"><?php echo $signupError; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($signupSuccess)): ?>
                            <div class="success-message"><?php echo $signupSuccess; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Full name" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                    </div>
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input type="password" class="form-control password-field" placeholder="Password" name="password" required>
                            <button type="button" class="password-toggle">Show</button>
                        </div>
                        <div class="password-requirements">
                            <div class="requirement" data-requirement="length">At least 8 characters</div>
                            <div class="requirement" data-requirement="uppercase">One uppercase letter</div>
                            <div class="requirement" data-requirement="number">One number</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password" name="confirmPassword" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Area of interest" name="interest">
                    </div>
                    <div class="form-group">
    <select class="form-control" name="category">
        <option value="">Category</option>
        <option value="student">Student</option>
        <option value="reader">Reader</option>
        <option value="library">Library</option>
        <option value="admin">Admin</option>
    </select>
</div>
                    <div class="terms-group">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
                    </div>
                    <button type="submit" class="submit-btn">Sign up</button>
                    <div class="toggle-form">
                        Already have an account? <span onclick="toggleForm('login')">Login</span>
                    </div>
                </form>

                <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: none;">
                    <div class="form-header">
                        <h2>Welcome</h2>
                        <p>Login to continue your reading journey</p>
                        <?php if (!empty($loginError)): ?>
                            <div class="error-message"><?php echo $loginError; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Email" name="loginEmail" required>
                    </div>
                    <div class="form-group">
                        <div class="password-wrapper">
                            <input type="password" class="form-control password-field" placeholder="Password" name="loginPassword" required>
                            <button type="button" class="password-toggle">Show</button>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Login</button>
                    <div class="toggle-form">
                        Don't have an account? <span onclick="toggleForm('signup')">Sign up</span>
                    </div>
                </form>
            </div>
        </div>
        <div class="image-section">
            <img src="/Booksy/Assets/login-signup/leer.jpg" alt="Reading illustration" class="illustration" id="illustration">
        </div>
    </div>

    <script>
        const signupForm = document.getElementById('signupForm');
        const loginForm = document.getElementById('loginForm');
        const illustration = document.getElementById('illustration');

        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    this.textContent = 'Show';
                }
            });
        });

        const passwordInput = signupForm.querySelector('[name="password"]');
        const requirements = {
            length: str => str.length >= 8,
            uppercase: str => /[A-Z]/.test(str),
            number: str => /[0-9]/.test(str)
        };

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            Object.keys(requirements).forEach(req => {
                const element = document.querySelector(`[data-requirement="${req}"]`);
                if (requirements[req](password)) {
                    element.classList.add('met');
                } else {
                    element.classList.remove('met');
                }
            });
        });

        function updateSignupColor() {
            const formFields = {
                fullName: signupForm.querySelector('[name="fullName"]'),
                email: signupForm.querySelector('[name="email"]'),
                password: signupForm.querySelector('[name="password"]'),
                confirmPassword: signupForm.querySelector('[name="confirmPassword"]'),
                interest: signupForm.querySelector('[name="interest"]'),
                category: signupForm.querySelector('[name="category"]'),
                terms: signupForm.querySelector('[name="terms"]')
            };

            let completedSteps = 0;
            const totalSteps = 7;

            Object.entries(formFields).forEach(([key, field]) => {
                if (field.type === 'checkbox') {
                    if (field.checked) completedSteps++;
                } else {
                    if (field.value.trim()) completedSteps++;
                }
            });

            const grayscalePercentage = 100 - (completedSteps * (100 / totalSteps));
            illustration.style.filter = `grayscale(${grayscalePercentage}%)`;
        }

        signupForm.querySelectorAll('input, select').forEach(field => {
            if (field.type === 'checkbox') {
                field.addEventListener('change', updateSignupColor);
            } else {
                field.addEventListener('input', updateSignupColor);
            }
        });

        function updateLoginColor() {
            const email = loginForm.querySelector('[name="loginEmail"]');
            const password = loginForm.querySelector('[name="loginPassword"]');

            let completedSteps = 0;
            const totalSteps = 2;

            if (email.value.trim()) completedSteps++;
            if (password.value.trim()) completedSteps++;

            const grayscalePercentage = 100 - (completedSteps * (100 / totalSteps));
            illustration.style.filter = `grayscale(${grayscalePercentage}%)`;
        }

        loginForm.querySelectorAll('input').forEach(field => {
            field.addEventListener('input', updateLoginColor);
        });

        function toggleForm(form) {
            if (form === 'login') {
                signupForm.style.display = 'none';
                loginForm.style.display = 'block';
                updateLoginColor();
            } else {
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
                updateSignupColor();
            }
        }

        // Client-side validation
        signupForm.addEventListener('submit', function(e) {
            const password = this.querySelector('[name="password"]').value;
            const confirmPassword = this.querySelector('[name="confirmPassword"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
            
            // Check password requirements
            if (!requirements.length(password) || !requirements.uppercase(password) || !requirements.number(password)) {
                e.preventDefault();
                alert('Password does not meet all requirements');
            }
        });
    </script>
    <!-- Add this inside your <head> or just before </body> in the HTML -->
<?php if ($showLogin || !empty($loginError)) : ?>
<script>
    window.onload = function () {
        document.getElementById('signupForm').style.display = 'none';
        document.getElementById('loginForm').style.display = 'block';
    }
</script>
<?php endif; ?>

<?php if (!empty($signupSuccess)) : ?>
<script>
    window.onload = function () {
        document.getElementById('signupForm').style.display = 'none';
        document.getElementById('loginForm').style.display = 'block';
    }
</script>
<?php endif; ?>
</body>
</html>