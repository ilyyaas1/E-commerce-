<?php
session_start();
require 'config/database.php';



$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $conn = connectMabasi();
    $stmt = $conn->prepare("SELECT id, nom, mot_de_pass,role FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nom, $mot_de_passe_hash, $role);
        $stmt->fetch();

       
        if (password_verify($password, $mot_de_passe_hash)) {
            
            $_SESSION['user_id'] = $id;
            $_SESSION['nom'] = $nom;
            $_SESSION['role'] = $role;
            if($role == 'client'){
                header("Location: index.php"); 
                exit;
            }else{
                header("Location:admin/dashboard_admin.php");
                exit;
            }
            
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Aucun compte avec cet email.";
    }
    $stmt->close();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
     
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4; 
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-box {
    width: 300px;
    padding: 40px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px; 
    text-align: center;
}

h1 {
    margin-bottom: 20px;
    color: #333;
    font-size: 24px;
}

label {
    font-size: 14px;
    margin-bottom: 8px;
    color: #555;
    display: block;
    text-align: left;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #007bff;
    outline: none;
}

input[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff; 
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}



label[for="remember"] {
    font-size: 12px;
    color: #555;
}

a {
    display: inline-block;
    margin-top: 15px;
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
}

a:hover {
    text-decoration: underline;
}

p {
    font-size: 14px;
    color: red;
    margin-top: 10px;
}


    </style>
</head>
<body>
    <div class="login-box">
        <h1>LOGIN</h1>
        <form action="login.php" method="post">
            <label for="email">EMAIL</label>
            <input type="email" name="email" placeholder="enter ur email"><br>
            <label for="password">PASSWORD</label>
            <input type="password" name="password" placeholder="enter your password"><br>
            <input type="submit" value="login" name="login"><br>
            <input type="checkbox" name="remember">
            <label for="remember">remember me</label>
        </form>
        <p style="color:red;"><?php echo $message; ?></p>
        <a href="inscription.php">create an account</a>
    </div>

    
</body>
</html>