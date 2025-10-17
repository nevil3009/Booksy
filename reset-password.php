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

$message = "";
$errorMessage = "";
$showResetForm = false;
$token = "";

// Check if token is provided
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token is valid and not expired
    $stmt = $conn->prepare("SELECT pr.id, pr.user_id, pr.expires_at, u.email 
                           FROM password_resets pr 
                           JOIN users u ON pr.user_id = u.id 
                           WHERE pr.token = ? AND pr.expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $showResetForm = true;
        $resetData = $result->fetch_assoc();
    } else {
        $errorMessage = "Invalid or expired reset token. Please request a new password reset link.";
    }
    $stmt->close();
}

// Process password reset form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newPassword'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "Both fields are required";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match";
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $errorMessage = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $errorMessage = "Password must contain at least one number";
    } else {
        // Verify token again
        $stmt = $conn->prepare("SELECT pr.id, pr.user_id 
                               FROM password_resets pr 
                               WHERE pr.token = ? AND pr.expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $resetData = $result->fetch_assoc();
            $userId = $resetData['user_id'];
            $resetId = $resetData['id'];
            
            // Update user password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedPassword, $userId);
            
            if ($updateStmt->execute()) {
                // Delete the used token
                $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE id = ?");
                $deleteStmt->bind_param("i", $resetId);
                $deleteStmt->execute();
                $deleteStmt->close();
                
                $message = "Your password has been successfully updated. You can now <a href='login.php'>login</a> with your new password.";
                $showResetForm = false;
            } else {
                $errorMessage = "Failed to update password. Please try again.";
            }
            $updateStmt->close();
        } else {
            $errorMessage = "Invalid or expired reset token. Please request a new password reset link.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Booksy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(to right, #3b82f6, #9333ea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .reset-form {
            padding: 0 2rem 2rem;
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

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .error-message {
            background-color: #fee2e2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .success-message {
            background-color: #dcfce7;
            color: #22c55e;
            border: 1px solid #bbf7d0;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: #60a5fa;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Booksy</h1>
            <p>Reset Your Password</p>
        </div>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="message error-message">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="message success-message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($showResetForm): ?>
            <form class="reset-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?token=" . $token); ?>" id="resetForm">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                
                <div class="form-group">
                    <div class="password-wrapper">
                        <input type="password" name="newPassword" id="newPassword" class="form-control password-field" placeholder="New Password" required>
                        <button type="button" class="password-toggle" data-target="newPassword">Show</button>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-length">At least 8 characters</div>
                        <div class="requirement" id="req-uppercase">At least 1 uppercase letter</div>
                        <div class="requirement" id="req-number">At least 1 number</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="password-wrapper">
                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control password-field" placeholder="Confirm Password" required>
                        <button type="button" class="password-toggle" data-target="confirmPassword">Show</button>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-match">Passwords match</div>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">Reset Password</button>
            </form>
        <?php else: ?>
            <?php if (empty($message)): ?>
                <div class="message error-message">
                    Invalid or expired reset token. Please request a new password reset link.
                </div>
                <div class="login-link">
                    <a href="login.php">Back to Login</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const toggleButtons = document.querySelectorAll('.password-toggle');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.textContent = 'Hide';
                    } else {
                        passwordInput.type = 'password';
                        this.textContent = 'Show';
                    }
                });
            });

            // Password validation
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const submitBtn = document.getElementById('submitBtn');
            
            // Requirement elements
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqNumber = document.getElementById('req-number');
            const reqMatch = document.getElementById('req-match');
            
            function validatePassword() {
                const password = newPassword.value;
                const confirm = confirmPassword.value;
                
                // Check length
                if (password.length >= 8) {
                    reqLength.classList.add('met');
                } else {
                    reqLength.classList.remove('met');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(password)) {
                    reqUppercase.classList.add('met');
                } else {
                    reqUppercase.classList.remove('met');
                }
                
                // Check number
                if (/[0-9]/.test(password)) {
                    reqNumber.classList.add('met');
                } else {
                    reqNumber.classList.remove('met');
                }
                
                // Check match
                if (password === confirm && password !== '') {
                    reqMatch.classList.add('met');
                } else {
                    reqMatch.classList.remove('met');
                }
                
                // Enable/disable submit button
                if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && password === confirm && confirm !== '') {
                    submitBtn.removeAttribute('disabled');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                }
            }
            
            // Add event listeners
            newPassword.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
            
            // Initialize form validation state
            validatePassword();
        });
    </script>
</body>
</html>