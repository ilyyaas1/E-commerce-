<?php
require "../config/database.php";
require "includes/header.html";

$message = '';
$image_ok = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference = $_POST["reference"] ?? '';
    $nom = $_POST["nom"] ?? '';
    $description = $_POST["description"] ?? '';
    $prix = $_POST["prix"] ?? '';
    $categorie = $_POST["categorie"] ?? '';
    $stock = $_POST["stock"] ?? 0;

    $image = $_FILES["image"]["name"] ?? '';
    $tmp = $_FILES["image"]["tmp_name"] ?? '';
    $folder = "../uploads/" . $image;

    if (!empty($image)) {
        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpeg", "jpg", "png", "gif");

        if (!in_array($extension, $allowed_extensions)) {
            $message = 'Image invalide. Seules les extensions jpeg, png, jpg, gif sont autorisées.';
            $image_ok = false;
        }
    }

    $conn = connectMabasi();

    // Ajouter un produit
    if (isset($_POST["add_produit"]) && $image_ok) {
        $check = $conn->prepare("SELECT stock FROM produits WHERE reference = ?");
        $check->bind_param("i", $reference);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->bind_result($stock_existe);
            $check->fetch();
            $new_stock = $stock + $stock_existe;

            $update = $conn->prepare("UPDATE produits SET stock = ? WHERE reference = ?");
            $update->bind_param("ii", $new_stock, $reference);
            $update->execute();
            move_uploaded_file($tmp, $folder);
            $message = "Stock mis à jour pour le produit existant.";
            $update->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO produits(reference, nom, description, prix, categorie, image, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdssi", $reference, $nom, $description, $prix, $categorie, $image, $stock);
            $stmt->execute();
            move_uploaded_file($tmp, $folder);
            $message = 'Produit ajouté avec succès.';
            $stmt->close();
        }
    }

    // Modifier un produit
    if (isset($_POST["update_produit"]) && $image_ok) {
        $check = $conn->prepare("SELECT * FROM produits WHERE reference = ?");
        $check->bind_param("i", $reference);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            if (!empty($image)) {
                $stmt = $conn->prepare("UPDATE produits SET nom=?, description=?, prix=?, categorie=?, image=?, stock=? WHERE reference=?");
                $stmt->bind_param("ssdssii", $nom, $description, $prix, $categorie, $image, $stock, $reference);
                move_uploaded_file($tmp, $folder);
            } else {
                $stmt = $conn->prepare("UPDATE produits SET nom=?, description=?, prix=?, categorie=?, stock=? WHERE reference=?");
                $stmt->bind_param("ssdssi", $nom, $description, $prix, $categorie, $stock, $reference);
            }
            $stmt->execute();
            $message = 'Produit modifié avec succès.';
        } else {
            $message = "Aucun produit trouvé avec la référence $reference.";
        }
        $check->close();
    }

    // Supprimer un produit
    if (isset($_POST["delete_produit"])) {
        $check = $conn->prepare("SELECT * FROM produits WHERE reference = ?");
        $check->bind_param("i", $reference);
        $check->execute();
        $result = $check->get_result();

        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM produits WHERE reference = ?");
            $stmt->bind_param("i", $reference);
            $stmt->execute();
            $stmt->close();
            $message = "Produit supprimé avec succès.";
        } else {
            $message = "Le produit avec la référence $reference n'existe pas.";
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Produits</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="admin-container">
    <h1>Gestion des Produits</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="reference" placeholder="Référence du produit" required>
        <input type="text" name="nom" placeholder="Nom du produit">
        <input type="text" name="description" placeholder="Description">
        <input type="number" name="prix" placeholder="Prix" min="0" step="0.01">
        <input type="text" name="categorie" placeholder="Catégorie">
        <input type="file" name="image">
        <input type="number" name="stock" placeholder="Stock" min="0">
        <div class="btn-group">
            <input type="submit" name="add_produit" value="Ajouter">
            <input type="submit" name="update_produit" value="Modifier">
            <input type="submit" name="delete_produit" value="Supprimer">
        </div>
        <p class="message"><?= $message ?></p>
    </form>
</div>
</body>
</html>
