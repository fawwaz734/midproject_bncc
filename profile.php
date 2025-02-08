<?php
session_start();

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "midproject_bncc";

$conection = mysqli_connect($host, $user, $pass, $db);
if(!$conection){
    die("connection failed: " . mysqli_connect_error());
}

$errors = [];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); 
    exit;
}
$firstName = $_SESSION['first_name']; 
$lastName = $_SESSION['last_name'];
$email = $_SESSION['email'];
$bio = $_SESSION['bio'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .profile-info {
            margin-top: 20px;
        }

        .profile-info p {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
        }

        .profile-info strong {
            color: #333;
        }

        .logout-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome, <?php echo $firstName . ' ' . $lastName; ?></h1>

        <div class="profile-info">
            <p><strong>First Name:</strong> <?php echo $firstName; ?></p>
            <p><strong>Last Name:</strong> <?php echo $lastName; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Bio:</strong> <?php echo $bio; ?></p>
        </div>

        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>

</body>
</html>
