<?php
require "../config/database.php";
require "includes/header.html";

$message = '';
$id = $_GET['id'] ?? '';

if (!empty($id) && is_numeric($id)) {
    $conn = connectMabasi();

    $check = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ? AND role != 'admin'");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $delete = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
        $message = "✅ Client avec l'ID <strong>$id</strong> supprimé avec succès.";
    } else {
        $message = "❌ Client introuvable ou tentative de suppression d'un administrateur.";
    }
    $check->close();
} else {
    $message = "⚠️ ID invalide ou non spécifié.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression Client</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 60px auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 26px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 18px;
            padding: 15px;
            background-color: #eef1f6;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #222;
            border-left: 5px solid #2e8b57;
        }
        a.button {
            text-decoration: none;
            padding: 12px 20px;
            background-color: #2e8b57;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }
        a.button:hover {
            background-color: #246b44;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Résultat de la suppression</h1>
    <div class="message"><?= $message ?></div>
    <a href="gestion_clients.php" class="button">← Retour à la gestion des clients</a>
</div>
</body>
</html>
