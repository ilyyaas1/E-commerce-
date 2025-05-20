<?php
require "config/database.php";
session_start();
$message = '';
$conn = connectMabasi();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registre"])) {
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $mot_de_pass = $_POST["mot_de_pass"];
    $confirme_mot_de_pass = $_POST["confirme_mot_de_pass"];
    if ($mot_de_pass !== $confirme_mot_de_pass) {
        $message = "les mots de pass sont different";
    } else {

        $check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->num_rows > 0) {
            $message = "email existe deja ";
            $check->close();
        } else {
            $check->close();
            $hashed_password = password_hash($mot_de_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO utilisateurs(nom,email,mot_de_pass) VALUES (?,?,?)");
            $stmt->bind_param("sss", $nom, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION["user_id"] = $stmt->insert_id;
                $_SESSION["nom"] = $nom;
                $_SESSION["role"] = 'client';
                header("Location: login.php");
                exit;
            } else {
                $message = "Erreur lors de l'inscription";
            }
            $stmt->close();
        }
    }
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #f6f6f6, #d9e0f5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .signin-box {
            width: 400px;
            padding: 40px;
            background: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            text-align: center;
        }

        .signin-box h1 {
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .signin-box label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .signin-box input[type="text"],
        .signin-box input[type="email"],
        .signin-box input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: 0.3s;
        }

        .signin-box input[type="text"]:focus,
        .signin-box input[type="email"]:focus,
        .signin-box input[type="password"]:focus {
            border-color: #5c7cfa;
            outline: none;
        }

        .signin-box input[type="submit"] {
            background-color: #5c7cfa;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .signin-box input[type="submit"]:hover {
            background-color: #4263eb;
        }
    </style>
</head>

<body>
    <div class="signin-box">
        <h1>CREATE ACCOUNT</h1>
        <form action="inscription.php" method="post">
            <label for="nom">NOM</label>
            <input type="text" name="nom" placeholder="entrer votre nom" required>
            <label for="email">EMAIL</label>
            <input type="email" name="email" placeholder="entrer votre email" required>
            <label for="mot_de_pass">MOT DE PASS</label>
            <input type="password" name="mot_de_pass" placeholder="entrer votre mot de pass" required>
            <label for="confirme">CONFIRME MOT DE PASS</label>
            <input type="password" name="confirme_mot_de_pass" placeholder="confirmer votre mot de pass" required>
            <input type="submit" name="registre" value="create account">
        </form>
        <p style="color:red;"><?=$message?></p>
        
    </div>
</body>

</html>