<?php
session_start();
require_once "config/database.php";
require_once "includes/header.html";

$conn = connectMabasi();


if (!isset($_SESSION['user_id'])) {
    echo "<p style='text-align:center; color:red;'>Veuillez vous connecter pour finaliser votre commande.</p>";
    header('Location : login.php');
    exit;
}

if (!isset($_SESSION['achat_direct']) && empty($_SESSION['panier'])) {
    echo "<p style='text-align:center; color:red;'>Aucun produit à acheter.</p>";
    exit;
}

$id_client = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paiement = $_POST["mode_paiement"];

    if (!in_array($paiement, ['carte', 'paypal', 'livraison'])) {
        $erreur = "Méthode de paiement invalide.";
    } else {
        $produits = isset($_SESSION['achat_direct']) ? [$_SESSION['achat_direct']] : $_SESSION['panier'];

      
        $stmt = $conn->prepare("INSERT INTO commandes (id_client, statut, mode_paiement) VALUES (?, 'confirmée', ?)");
        $stmt->bind_param("is", $id_client, $paiement);
        $stmt->execute();
        $num_commande = $stmt->insert_id;

        
        $stmt_prod = $conn->prepare("INSERT INTO lignedecommande(num_commande, ref_prod, quantite, prix) VALUES (?, ?, ?, ?)");
        foreach ($produits as $p) {
            $stmt_prod->bind_param("isid", $num_commande, $p["reference"], $p["quantite"], $p["prix"]);
            $stmt_prod->execute();
        }

        unset($_SESSION['achat_direct']);
        unset($_SESSION['panier']);

        echo "<p style='text-align:center; color:green;'>Commande enregistrée avec succès !</p>";
        exit;
    }
   
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; }
        .container {
            width: 70%;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        .produit {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .produit span {
            flex: 1;
            text-align: center;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        form {
            margin-top: 40px;
        }
        label {
            display: block;
            margin-top: 15px;   
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn-valider {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-valider:hover {
            background-color: #27ae60;
        }
        .erreur {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">Récapitulatif de la commande</h2>

    <?php
    $produits = isset($_SESSION['achat_direct']) ? [$_SESSION['achat_direct']] : $_SESSION['panier'];
    $total = 0;

    echo "<div class='produit' style='font-weight:bold; border-bottom:2px solid #333;'>
            <span>Produit</span>
            <span>Quantité</span>
            <span>Prix Unitaire</span>
            <span>Total</span>
         </div>";

    foreach ($produits as $p) {
        $sous_total = $p["prix"] * $p["quantite"];
        $total += $sous_total;

        echo "<div class='produit'>
                <span>" . htmlspecialchars($p["nom"]) . "</span>
                <span>" . $p["quantite"] . "</span>
                <span>" . number_format($p["prix"], 2) . " DH</span>
                <span>" . number_format($sous_total, 2) . " DH</span>
             </div>";
    }

    echo "<div class='total'>Total à payer : " . number_format($total, 2) . " DH</div>";
    if (isset($erreur)) echo "<p class='erreur'>$erreur</p>";
    ?>

    <form method="post">
        <label for="mode_paiement">Méthode de paiement :</label>
        <select name="mode_paiement" required>
            <option value="carte">Carte bancaire</option>
            <option value="paypal">PayPal</option>
            <option value="livraison">Paiement à la livraison</option>
        </select>

        <input type="submit" value="Valider le paiement" class="btn-valider">
    </form>
</div>

    <?php require 'includes/footer.html'; ?>

</body>
</html>
