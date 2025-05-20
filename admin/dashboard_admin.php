<?php
require 'includes/header.html';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <style>

    .dashboard-header {
      margin: 40px;
    }

    .dashboard-header h1 {
      font-size: 2em;
      color: #2c3e50;
      margin-bottom: 5px;
    }

    .dashboard-header p {
      color: #7f8c8d;
      font-size: 1em;
    }

    .admin-links {
      display: flex;
      gap: 40px;
      padding: 40px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .admin-card {
      display: block;
      width: 300px;
      padding: 30px;
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      text-decoration: none;
      color: #333;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .admin-card h2 {
      margin-top: 0;
      font-size: 1.3em;
      color: #2c3e50;
    }

    .admin-card p {
      margin-top: 8px;
      color: #666;
      font-size: 0.95em;
    }

    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>



<div class="dashboard-header">
  <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['nom']); ?> 👋</h1>
  <p>Bienvenue dans votre espace de gestion.</p>
</div>

<div class="admin-links">
  <a href="gestion_produit.php" class="admin-card">
    <h2>Gestion des Produits</h2>
    <p>Ajouter, modifier ou supprimer des produits.</p>
  </a>

  <a href="gestion_clients.php" class="admin-card">
    <h2>Gestion des Clients</h2>
    <p>Voir la liste des clients et gérer leurs informations.</p>
  </a>
</div>

</body>
</html>
