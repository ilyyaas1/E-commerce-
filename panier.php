<?php
require "config/database.php";
require "includes/header.html";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$conn = connectMabasi();

if (isset($_POST["supprimer"])) {
    $ref = $_POST['reference'];
    if (isset($_SESSION["panier"][$ref])) {
        unset($_SESSION["panier"][$ref]);
    }
}

$panier = $_SESSION["panier"] ?? [];
$total_general = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 20px;
        }

        .carte-produit {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 260px;
            padding: 15px;
            position: relative;
            transition: 0.3s ease;
            text-align: center;
        }

        .carte-produit:hover {
            transform: translateY(-5px);
        }

        .carte-produit img {
            width: 90%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .carte-produit h2 {
            font-size: 1.1em;
            margin: 10px 0 5px;
        }

        .carte-produit p {
            margin: 6px 0;
            font-size: 0.95em;
            color: #333;
        }

        .carte-produit form {
            margin-top: 10px;
        }

        .carte-produit button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: gray;
            transition: transform 0.2s;
        }

        .carte-produit button:hover {
            transform: scale(1.2);
            color: red;
        }

        .total {
            text-align: center;
            margin: 30px 0;
            font-size: 1.3em;
            font-weight: bold;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

      
    </style>
</head>
<body>

<h1>Mon panier</h1>

<?php if (isset($_SESSION["message_panier"])): ?>
    <p class="message"><?= $_SESSION["message_panier"]; unset($_SESSION["message_panier"]); ?></p>
<?php endif; ?>

<div class="container">
    <?php if (empty($panier)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <?php foreach ($panier as $item): ?>
            <?php
                $total = $item['prix'] * $item['quantite'];
                $total_general += $total;
            ?>
            <div class="carte-produit">
                <a href="detail_produit.php?reference=<?= urlencode($item["reference"]) ?>">
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nom']) ?>">
                    <h2><?= htmlspecialchars($item['nom']) ?></h2>
                    <p><b>Prix :</b> <?= number_format($item['prix'], 2) ?> DH</p>
                    <p><b>Quantité :</b> <?= htmlspecialchars($item['quantite']) ?></p>
                    <p><b>Total :</b> <?= number_format($total, 2) ?> DH</p>
                </a>
                <form action="panier.php" method="post">
                    <input type="hidden" name="reference" value="<?= htmlspecialchars($item['reference']) ?>">
                    <button type="submit" name="supprimer" title="Supprimer du panier">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($panier)): ?>
    <div class="total">Total général : <?= number_format($total_general, 2) ?> DH</div>
<?php endif; ?>





    <?php require 'includes/footer.html'; ?>


</body>
</html>
