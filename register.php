<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        :root {
            --primary-color: #0a888f;
            --primary-dark: #074e52;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --border-color: #ddd;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-size: 28px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            transition: var(--transition);
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 136, 143, 0.2);
        }

        /* Role Selection Grid */
        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .role-column {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .role-option {
            padding: 15px;
            background: var(--secondary-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .role-option:hover {
            background: #e9ecef;
        }

        .role-option input[type="radio"] {
            margin-right: 8px;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: mediumblue;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        button:hover {
            background-color: black;
            transform: translateY(-2px);
        }

        p {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }


input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <form action="register_user.php" method="post">
            <div class="form-row">
                <div class="column">
                    <input type="text" name="user_name" placeholder="Full name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <input type="text" name="phone" placeholder="Phone Number" required>
                </div>
            </div>
            <div class="form-row">
                <input type="text" name="created_at" placeholder="Joining Date" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>
            
            <!-- Role Selection Grid -->
            <div class="role-grid">
                <!-- Left Column -->
                <div class="role-column">
                    <label class="role-option"><input type="radio" name="role" value="manager" required> Manager</label>
                    <label class="role-option"><input type="radio" name="role" value="purcheser" required> Purchaser</label>
                </div>
                
                <!-- Right Column -->
                <div class="role-column">
                    <label class="role-option"><input type="radio" name="role" value="storeman" required> Storeman</label>
                    <label class="role-option"><input type="radio" name="role" value="sales" required> Sales</label>
                </div>
            </div>
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>