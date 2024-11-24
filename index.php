<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afya Bora</title>
    <style> 
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }
        .navbar {
            width: 100%;
            background-color: #003300; /* Dark Green */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar .logo {
            height: 50px;
            width: auto;
        }
        .navbar .nav-items {
            display: flex;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }
        .navbar a:hover {
            background-color: #005500;
        }
        .navbar img {
            height: 20px;
            width: 20px;
            margin-right: 8px;
        }
        .hero {
            background: url('landing.png') no-repeat center center;
            background-size: cover;
            width: 100%;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        .hero h1 {
            font-size: 3em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .about {
            background-color: #f9f9f9;
            padding: 40px;
            width: 80%;
            max-width: 800px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .about h1 {
            margin-bottom: 20px;
            font-size: 2.5em;
            color: #003300; /* Dark Green */
        }
        .about p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
        }
        .registration-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .button {
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background-color: #FF6600; /* Orange */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #CC5200; /* Darker Orange */
        }
        footer {
            background-color: #003300; /* Dark Green */
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="nav-items">
            <a href="index.php">Home</a>
            <a href="registration.php">Register</a>
            <a href="login.php">Login</a>
            <a href="mailto:info@afyabora.com">
                <img src="email_icon.png" alt="Email"> info@afyabora.com
            </a>
        </div>
    </div>
    <div class="hero">
        <h1>Welcome to Afya Bora</h1>
    </div>
    <div class="main">
        <div class="about">
            <h1><i>Your Health, Our Priority</i></h1>
            <h2>About Afya Bora</h2>
            <p>Afya Bora is your trusted healthcare partner, offering a platform to manage your medical records over time. Our system allows you to easily access your medical history, communicate with healthcare providers, book appointments, and renew prescriptions—all from the comfort of your home.</p>
            <p>Join us today and experience a seamless way to manage your health.</p>
            <div class="registration-buttons">
                <a href="registration.php" class="button">Register</a>
            </div>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; 
            <?php echo date('Y'); ?>
        </p>
    </footer>   
</body>
</html>
