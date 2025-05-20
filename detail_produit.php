<?php
require_once "config/database.php";
require_once "includes/header.html";
session_start();

$conn = connectMabasi();

if (!isset($_GET['reference'])) {
    echo "Aucun produit sélectionné.";
    exit;
}

$reference = $_GET['reference'];
$stmt = $conn->prepare("SELECT * FROM produits WHERE reference = ?");
$stmt->bind_param("i", $reference);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produit introuvable.";
    exit;
}

$produit = $result->fetch_assoc();

if (isset($_POST["ajout_panier"])) {
    $quantite = intval($_POST["quantite"]);
    $reference = $_POST["reference"];
    $nom = $_POST["nom"];
    $prix = $_POST["prix"];
    $image = $produit["image"];

    $item = [
        "reference" => $reference,
        "nom" => $nom,
        "prix" => $prix,
        "quantite" => $quantite,
        "image" => $image,
    ];

    $_SESSION["panier"][$reference] = $item;
    $_SESSION["message_panier"] = "Produit ajouté avec succès au panier.";
    header("Location: panier.php");
    exit;
}
if (isset($_POST["acheter_produit"])) {
    $_SESSION['achat_direct'] = [
        "reference" => $_POST["reference"],
        "nom" => $_POST["nom"],
        "prix" => $_POST["prix"],
        "quantite" => intval($_POST["quantite"]),
        "image" => $produit["image"]
    ];
    header("Location: paiement.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Produit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        .detail-container {
            width: 60%;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 2px 4px 15px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
        }
        .detail-container img {
            width: 400px;
            height: 40%;
            border-radius: 5px;
            object-fit: cover;
        }
        .infos {
            flex: 1;
        }
        .btn-ajouter {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }
        .btn-ajouter:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>

<div class="detail-container">
    <img src="uploads/<?= htmlspecialchars($produit['image']) ?>" alt="Produit">
    <div class="infos">
        <h2><?= htmlspecialchars($produit['nom']) ?></h2>
        <p><strong>Description:</strong> <?= htmlspecialchars($produit['description']) ?></p>
        <p><strong>Prix:</strong> <?= $produit['prix'] ?> DH</p>
        <p><strong>Catégorie:</strong> <?= htmlspecialchars($produit['categorie']) ?></p>
        <p><strong>Stock disponible:</strong> <?= $produit['stock'] ?></p>

        <form action="" method="post">
            <input type="hidden" name="reference" value="<?= $produit['reference'] ?>">
            <input type="hidden" name="nom" value="<?= $produit['nom'] ?>">
            <input type="hidden" name="prix" value="<?= $produit['prix'] ?>">
            <label for="quantite">Quantité :</label>
            <input type="number" name="quantite" value="1" min="1" max="<?= $produit['stock'] ?>">
            <br>
            <input type="submit" name="ajout_panier" class="btn-ajouter" value="Ajouter au panier">
            <input type="submit" name="acheter_produit" class="btn-ajouter" value="Acheter Produit">
        </form>
    </div>
</div>
<?php 
        require 'includes/footer.html';
 ?>

</body>
</html>
