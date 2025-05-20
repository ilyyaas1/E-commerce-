<?php
session_start();
require '../config/database.php';
require 'includes/header.html';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../connexion.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = connectMabasi();
$stmt = $conn->prepare("SELECT nom, email, role, date_inscription FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nom, $email, $role, $date_inscription);
$stmt->fetch();
$stmt->close();
if(isset($_POST["logout"])){
    session_destroy();
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        .profile-box {
            width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
        }
        .logout {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
        .logout a {
            text-decoration: none;
            color: #fff;
            background-color: #e60000;
            padding: 10px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>Bienvenue, <?= htmlspecialchars($nom) ?> !</h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($role) ?></p>
        <p><strong>Inscrit le :</strong> <?= htmlspecialchars($date_inscription) ?></p>

        <div class="logout">
            <form action="profil_admin.php" method="post">
            <input type="submit" name="logout" value="logout" >
            </form> 
        </div>
    </div>
</body>
</html>
