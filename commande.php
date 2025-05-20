<?php
session_start();
require_once "config/database.php";
require_once "includes/header.html";

$conn = connectMabasi();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["num"])) {
    $num = intval($_POST["num"]);

    if (isset($_POST["statut"])) {
        $nouveau_statut = $_POST["statut"];
        if (in_array($nouveau_statut, ['en attente', 'confirmée', 'annulée'])) {
            $stmt = $conn->prepare("UPDATE commandes SET statut = ? WHERE num = ?");
            $stmt->bind_param("si", $nouveau_statut, $num);
            $stmt->execute();
        }
    }

    if (isset($_POST["supprimer"])) {
        $stmt = $conn->prepare("DELETE FROM commandes WHERE num = ?");
        $stmt->bind_param("i", $num);
        $stmt->execute();
        header("Location: commande.php");
        exit;
    }
}

$sql = "SELECT c.num, c.date_commande, c.statut, c.mode_paiement, u.nom AS utilisateur_nom 
        FROM commandes c 
        JOIN utilisateurs u ON c.id_client = u.id 
        ORDER BY c.date_commande DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes Utilisateurs</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            width: 90%;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .commande {
            display: grid;
            grid-template-columns: 1fr 2fr 1.5fr 1.5fr 2fr 3fr;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .commande.header {
            font-weight: bold;
            color: #555;
            background-color: #f0f0f0;
        }

        form {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }

        select {
            padding: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-update {
            background-color: #10b981;
            color: white;
        }

        .btn-update:hover {
            background-color: #059669;
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background-color: #dc2626;
        }

        @media (max-width: 768px) {
            .commande {
                display: block;
                padding: 15px 0;
            }

            .commande.header {
                display: none;
            }

            .commande > div {
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Liste des Commandes</h2>

    <div class="commande header">
        <div>#Commande</div>
        <div>Utilisateur</div>
        <div>Date</div>
        <div>Mode Paiement</div>
        <div>Statut</div>
        <div>Actions</div>
    </div>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="commande">
            <div><?= $row['num'] ?></div>
            <div><?= htmlspecialchars($row['utilisateur_nom']) ?></div>
            <div><?= $row['date_commande'] ?></div>
            <div><?= $row['mode_paiement'] ?></div>
            <div><?= $row['statut'] ?></div>
            <div>
                <form method="post" action="commande.php">
                    <input type="hidden" name="num" value="<?= $row['num'] ?>">
                    <select name="statut">
                        <option value="en attente" <?= $row['statut'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="confirmée" <?= $row['statut'] === 'confirmée' ? 'selected' : '' ?>>Confirmée</option>
                        <option value="annulée" <?= $row['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                    <button type="submit" class="btn btn-update">Mettre à jour</button>
                </form>
                <form method="post" action="commande.php" style="margin-top: 5px;">
                    <input type="hidden" name="num" value="<?= $row['num'] ?>">
                    <button type="submit" name="supprimer" class="btn btn-delete">Supprimer</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php require 'includes/footer.html'; ?>
</body>
</html>
