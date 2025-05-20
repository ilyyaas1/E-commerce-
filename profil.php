<?php
session_start();
require 'config/database.php';
require 'includes/header.html';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Utilisateur</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .profil-wrapper {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            min-height: calc(100vh - 100px); /* laisse de l'espace pour le header */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .profile-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #d0d7de;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .profile-container h2 {
            margin: 0;
            font-size: 26px;
            color: #1f2937;
        }

        .info {
            margin-top: 20px;
            text-align: left;
        }

        .info p {
            margin: 8px 0;
            font-size: 16px;
            color: #4b5563;
        }

        .info p strong {
            color: #111827;
            display: inline-block;
            width: 130px;
        }

        .logout-form {
            margin-top: 30px;
        }

        .logout-form input[type="submit"] {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-form input[type="submit"]:hover {
            background-color: #2563eb;
        }

        @media (max-width: 500px) {
            .info p strong {
                width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="profil-wrapper">
        <div class="profile-container">
            <div class="avatar"><?= strtoupper(substr($nom, 0, 1)) ?></div>
            <h2><?= htmlspecialchars($nom) ?></h2>

            <div class="info">
                <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
                <p><strong>Rôle :</strong> <?= htmlspecialchars($role) ?></p>
                <p><strong>Inscrit le :</strong> <?= htmlspecialchars($date_inscription) ?></p>
            </div>

            <div class="logout-form">
                <form action="profil.php" method="post">
                    <input type="submit" name="logout" value="Se déconnecter">
                </form>
            </div>
        </div>
    </div>
    <?php
        require 'includes/footer.html';
        ?>
</body>
</html>
