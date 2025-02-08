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

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user']['id'];
$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($connection, $_GET['search']);
    $query = "SELECT * FROM users WHERE (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%') AND id != '$admin_id'";
} else {
    $query = "SELECT * FROM users WHERE id != '$admin_id'";
}

$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <nav class="bg-blue-600 p-4 text-white flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <div class="flex gap-4">
            <a href="dashboard.php" class="hover:underline">Dashboard</a>
            <a href="profile.php" class="hover:underline">Profile</a>
            <a href="logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto mt-6">
        <form method="GET" action="dashboard.php" class="mb-4 flex justify-center">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded-l-lg w-1/3" placeholder="Cari user...">
            <button type="submit" class="bg-blue-500 text-white px-4 rounded-r-lg">Cari</button>
        </form>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th class="border p-2">No</th>
                        <th class="border p-2">Foto</th>
                        <th class="border p-2">Nama Lengkap</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($user = mysqli_fetch_assoc($result)) : ?>
                        <tr class="text-center">
                            <td class="border p-2"><?= $no++; ?></td>
                            <td class="border p-2">
                                <img src="<?= !empty($user['photo']) ? $user['photo'] : 'default.png' ?>" width="50" class="rounded-full">
                            </td>
                            <td class="border p-2"><?= $user['first_name'] . " " . $user['last_name']; ?></td>
                            <td class="border p-2"><?= $user['email']; ?></td>
                            <td class="border p-2">
                                <a href="read.php?id=<?= $user['id'] ?>" class="text-blue-500 hover:underline">View</a> |
                                <a href="update.php?id=<?= $user['id'] ?>" class="text-green-500 hover:underline">Edit</a> |
                                <a href="delete.php?id=<?= $user['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Yakin ingin menghapus?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="flex justify-center mt-4">
            <a href="create.php" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-600">Tambah User</a>
        </div>
    </div>

</body>
</html>
