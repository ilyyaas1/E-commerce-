<?php
require '../config/database.php';
require 'includes/header.html';
$conn = connectMabasi();


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT nom, email FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nom, $email);

    if (!$stmt->fetch()) {
        echo "Client introuvable.";
        exit;
    }

    $stmt->close();
}


if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nom, $email, $id);

    if ($stmt->execute()) {
        $message = "Client modifié avec succès.";
    } else {
        $message = "Erreur lors de la modification.";
    }

    $stmt->close();
    
    $stmt = $conn->prepare("SELECT nom, email FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nom, $email);
    $stmt->fetch();
    $stmt->close();
    header('Location:gestion_clients.php');

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Client</title>
    <style>
        body {
            font-family: Arial;
            
            background-color: #f5f5f5;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Modifier un Client</h2>
    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
    <form method="post" action="modifier_client.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
        <label for="email">Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        <input type="submit" name="modifier" value="Enregistrer les modifications">
    </form>

</body>
</html>
