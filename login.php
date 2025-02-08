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

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = mysqli_real_escape_string($conection, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $errors[] = "Email dan password harus diisi!";
    }

    if (!preg_match("/^([a-zA-Z0-9._%+-]+)@(gmail\.com|binus\.ac\.id)$/", $email)) {
        $errors[] = "Email harus menggunakan domain @gmail.com atau @binus.ac.id!";
    }

    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conection, $query);

        if($result && mysqli_num_rows($result) > 0){
            $user = mysqli_fetch_assoc($result);
            if(password_verify($password, $user['password'])){
                $_SESSION['user'] = $user;
                
                if($remember) {
                    setcookie("user_email", $email, time() + (86400 * 30), "/");
                }
                
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Password salah!";
            }
        } else {
            $errors[] = "Email tidak terdaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    </style>
</head>

<body class="bg-gradient-to-r from-pink-500 to-yellow-500 flex items-center justify-center h-screen font-sans-serif">
    <div id="login-box" class="bg-transparent p-8 rounded-2xl shadow-2xl shadow-white w-96 transform transition duration-500 hover:scale-105 border-2 border-solid">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Welcome Admin!</h2>
        <p class="text-center text-black-500 mb-4">Silakan masuk untuk melanjutkan</p>
        
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger text-center py-2 rounded-md mb-4 animate-pulse">
                <?php foreach ($errors as $error) { echo "$error<br>"; } ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700 font-medium">📧 Email</label>
                <input type="email" name="email" value="<?= isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '' ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Masukkan email" required>
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 font-medium">🔑 Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="Masukkan password" required>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-gray-700">Remember me</label>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-pink-500 text-white py-2 rounded-lg hover:opacity-90 transition font-semibold text-lg shadow-lg animate-bounce">Login 🚀</button>
        </form>
        
        <script>
            gsap.from("#login-box", { duration: 1, y: -50, opacity: 0, ease: "bounce" });
        </script>
    </div>
</body>
</html>
