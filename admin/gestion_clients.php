<?php
require "../config/database.php";
require "includes/header.html";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Clients</title>
    <link rel="stylesheet" href="assests/css/tyle.css">
<style>


.admin-container {
    max-width: auto;
    margin: auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-size: 28px;
}

form {
    text-align: center;
    margin-bottom: 25px;
}

form input[type="submit"] {
    background-color: #28a745;
    color: #fff;
    border: none;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #218838;
}

table {
    margin:0;
    padding: 0;
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 15px;
}

table th,
table td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
}

table tr:hover {
    background-color: #f1f1f1;
}

a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

a:hover {
    background-color: #e7f0ff;
}

</style>
</head>

<body>
<div class="admin-container">
    <h1>Gestion des Clients</h1>
    <form method="post">
        <input type="submit" name="afficher" value="Afficher tous les clients">
    </form>
    <br>
    <br>


    <?php
    if (isset($_POST["afficher"])) {
        $conn = connectMabasi();
        $stmt = $conn->prepare("SELECT id, nom, email, mot_de_pass, role, date_inscription FROM utilisateurs WHERE role != 'admin'");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            echo "<table border=1 style='border-collapse:collapse;'><tr><th>ID</th><th>Nom</th><th>Email</th><th>Mot de passe</th><th>Rôle</th><th>Date</th><th>Actions</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nom']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['mot_de_pass']}</td>
                    <td>{$row['role']}</td>
                    <td>{$row['date_inscription']}</td>
                    <td>
                        <a href='modifier_client.php?id={$row['id']}'>Modifier</a> | 
                        <a href='supprimer_client.php?id={$row['id']}'>Supprimer</a>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Aucun client inscrit.</p>";
        }
        $stmt->close();
    }
    ?>
</div>
</body>
</html>
