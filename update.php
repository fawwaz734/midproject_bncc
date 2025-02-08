<?php
session_start();

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "midproject_bncc";

$conection = mysqli_connect($host, $user, $pass, $db);
if (!$conection) {
    die("Connection failed: " . mysqli_connect_error());
}

// ambil data dari id
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conection, $_GET['id']);
    $query = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($conection, $query);
    $user = mysqli_fetch_assoc($result);
}

if (!$user) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim(mysqli_real_escape_string($conection, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conection, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conection, $_POST['email']));
    $bio = trim(mysqli_real_escape_string($conection, $_POST['bio']));
    $password = $_POST['password'];
    $photo = $user['photo'];


    if (empty($first_name) || strlen($first_name) > 255) {
        $errors[] = "First name harus diisi dan tidak boleh lebih dari 255 karakter!";
    }

    if (empty($last_name) || strlen($last_name) > 255) {
        $errors[] = "Last name harus diisi dan tidak boleh lebih dari 255 karakter!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }

  
    if ($email !== $user['email']) {
        $cek_email = mysqli_query($conection, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $errors[] = "Email sudah digunakan, gunakan email lain!";
        }
    }

    if (!empty($_FILES['photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = mime_content_type($_FILES["photo"]["tmp_name"]);
        $file_size = $_FILES["photo"]["size"];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "File foto harus berupa JPG, JPEG, atau PNG!";
        }

        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran foto tidak boleh lebih dari 2MB!";
        }

        if (empty($errors)) {
            $target_dir = "profile/";
            $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $photo_name;

            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo = $target_file;
            }
        }
    }

    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Password harus minimal 6 karakter!";
        } else {
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        }
    } else {
        $password_hashed = $user['password'];
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error'); window.history.back();</script>";
        }
        exit();
    }

    $query = "UPDATE users 
              SET first_name = '$first_name', last_name = '$last_name', email = '$email', bio = '$bio', photo = '$photo', password = '$password_hashed'
              WHERE id = '$id'";

    if (mysqli_query($conection, $query)) {
        $_SESSION['message'] = "User berhasil diupdate!";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conection);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-4">Edit User</h2>
        <form action="edit.php?id=<?= $id ?>" method="post" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-gray-600">First Name:</label>
                <input type="text" name="first_name" value="<?= $user['first_name'] ?>" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-gray-600">Last Name:</label>
                <input type="text" name="last_name" value="<?= $user['last_name'] ?>" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-gray-600">Email:</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-gray-600">Password (Kosongkan jika tidak ingin mengubah):</label>
                <input type="password" name="password" class="w-full p-2 border rounded">
            </div>
            <div>
                <label class="block text-gray-600">Bio:</label>
                <input type="text" name="bio" value="<?= $user['bio'] ?>" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-gray-600">Upload Foto Baru:</label>
                <input type="file" name="photo" class="w-full p-2 border rounded">
                <?php if (!empty($user['photo'])) : ?>
                    <img src="<?= $user['photo'] ?>" alt="User Photo" class="mt-2 rounded w-24 h-24 object-cover">
                <?php endif; ?>
            </div>
            <div class="flex justify-between mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Perubahan</button>
                <a href="dashboard.php" class="text-gray-600 hover:text-gray-800">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>

