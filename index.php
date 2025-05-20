<?php
require_once "config/database.php";
require_once "includes/header.html";
session_start();

$conn = connectMabasi();

$mot_cle = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$categorie = isset($_GET['categorie']) ? trim($_GET['categorie']) : '';
$order = isset($_GET['order'])? $_GET['order'] : '';



$produits = [];
$cat_query = $conn->query("SELECT DISTINCT categorie FROM produits");
$categories = $cat_query->fetch_all(MYSQLI_ASSOC);


$sql = "SELECT * FROM produits WHERE 1";
$params = [];
$types = "";

if ($mot_cle) {
    $sql .= " AND (nom LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%'))";
    $params[] = $mot_cle;
    $params[] = $mot_cle;
    $types .= "ss";
}

if ($categorie) {
    $sql .= " AND categorie = ?";
    $params[] = $categorie;
    $types .= "s";
}
if(in_array($order,['ASC','DESC'])){
    $sql .=" ORDER BY prix $order ";
}else{
    $sql .=" ORDER BY prix DESC";
}


$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$produits = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <style>
          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; 
        }


        body {
            margin: 0;
            padding:0;  
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f9fc;
        }

        .hero {
            background-image: url(images/image_back.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            padding: 100px 20px 50px;
            color: white;
            text-align: center;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        

        .search-box input,
        .search-box select,
        .search-box button {
            padding: 16px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }

        .search-box input,
        .search-box select {
            width: 200px;
        }

        .search-box button {
            background-color: #ff7e5f;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-box button:hover {
            background-color: #e06045;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px;
        }

        .carte-produit {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .carte-produit img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .carte-produit h3 {
            margin: 10px 0 5px;
        }

        .carte-produit p {
            color: #555;
            margin: 0;
        }

        .carte-produit a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            background: #2196f3;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }
        
    </style>
</head>
<body>

<section class="hero">
    <h1>Bienvenue sur notre boutique</h1>
    <form class="search-box" method="get" action="index.php">
        <input type="text" name="tag" placeholder="Rechercher..." value="<?= htmlspecialchars($mot_cle) ?>">
        <select name="categorie">
            <option value="">Toutes les catégories</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['categorie']) ?>" <?= $categorie === $cat['categorie'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['categorie']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="order">
            <option value="">Trier par prix </option>
            <option value="ASC" <?= $order === "ASC" ? "selected" : "" ?>>Croissant</option>
            <option value="DESC" <?= $order === "DESC" ? "selected" : "" ?>>Descandant</option>
        </select>
        <button type="submit">Rechercher</button>
        
    </form>
</section>

<div class="container">
    <?php foreach($produits as $produit) {?>
        <div class="carte-produit">
            <img src="uploads/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
            <h3><?= htmlspecialchars($produit['nom']) ?></h3>
            <p><?= htmlspecialchars($produit['prix']) ?> DH</p>
            <a href="detail_produit.php?reference=<?= $produit['reference'] ?>">Voir</a>
        </div>
    <?php } ?>
</div>

<?php
     require 'includes/footer.html';
?>



</body>
</html>