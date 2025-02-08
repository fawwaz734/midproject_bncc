<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "midproject_bncc";

$connection = mysqli_connect($host, $user, $pass, $db);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim(mysqli_real_escape_string($connection, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($connection, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($connection, $_POST['email']));
    $bio = trim(mysqli_real_escape_string($connection, $_POST['bio']));
    $photo = "";

    if (empty($first_name) || strlen($first_name) > 255) {
        $errors[] = "First name tidak boleh kosong dan maksimal 255 karakter!";
    }
    if (empty($last_name) || strlen($last_name) > 255) {
        $errors[] = "Last name tidak boleh kosong dan maksimal 255 karakter!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    } elseif (!preg_match("/@(gmail\.com|binus\.ac\.id)$/", $email)) {
        $errors[] = "Hanya email @gmail.com atau @binus.ac.id yang diperbolehkan!";
    } else {
        $cek_email = mysqli_query($connection, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $errors[] = "Email sudah digunakan, gunakan email lain!";
        }
    }

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "profile/";
        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo_name;

        if (file_exists($target_file)) {
            $errors[] = "File dengan nama yang sama sudah ada, coba unggah dengan nama berbeda!";
        } elseif (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file;
        } else {
            $errors[] = "Gagal mengupload foto!";
        }
    } else {
        $errors[] = "Foto tidak boleh kosong!";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error'); window.history.back();</script>";
        }
        exit;
    }

    function generatePassword($length = 10) {
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
    }
    $random_password = generatePassword();
    $password_hashed = md5($random_password); 

    $query = "INSERT INTO users (first_name, last_name, email, password, bio, photo) 
              VALUES ('$first_name', '$last_name', '$email', '$password_hashed', '$bio', '$photo')";

    if (mysqli_query($connection, $query)) {
        echo "<script>alert('User berhasil ditambahkan! Password: $random_password'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="card p-4 shadow">
        <h2 class="text-center">Tambah User</h2>
        <form action="tambah.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio (Opsional)</label>
                <textarea name="bio" id="bio" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Upload Foto</label>
                <input type="file" name="photo" id="photo" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Tambah</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </form>
    </div>
</body>
</html>

