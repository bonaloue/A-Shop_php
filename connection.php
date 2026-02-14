<?php
session_start();
include "db.php";

$nom = $_POST['nom'];
$mot_de_passe = $_POST['mot_de_passe'];

$sql = "SELECT * FROM utilisateur WHERE nom = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nom);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    
    if(password_verify($mot_de_passe, $user['mot_de_passe'])){
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        if($user['role'] == 'admin'){
            header("Location: admin/dashboard.php");
            exit();
        } else {
            header("Location: accueil.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Mot de passe incorrect !";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Aucun compte trouvé avec cet email !";
    header("Location: index.php");
    exit();
}

$stmt->close();
$conn->close();
?>