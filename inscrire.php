<?php
include "db.php";
$nom = $_POST['nom'];
$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];
$telephone = $_POST['telephone'];

$sql_verif = "SELECT * FROM utilisateur WHERE email = ?";
$stmt_verif = $conn->prepare($sql_verif);
$stmt_verif->bind_param("s", $email);
$stmt_verif->execute();
$result = $stmt_verif->get_result();

if($result->num_rows > 0){
    echo "<h2>Erreur : Cet email est déjà utilisé !</h2>";
    echo "<a href='register.php'>Retour</a>";
    exit();
}

$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

$sql_user = "INSERT INTO utilisateur (nom, email, mot_de_passe, telephone, role) VALUES (?, ?, ?, ?, 'client')";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("ssss", $nom, $email, $mot_de_passe_hash, $telephone);

if($stmt_user->execute()){
    $id_user = $conn->insert_id;
    
    $sql_client = "INSERT INTO client (id_user) VALUES (?)";
    $stmt_client = $conn->prepare($sql_client);
    $stmt_client->bind_param("i", $id_user);
    
    if($stmt_client->execute()){
        echo "<h2>Inscription réussie !</h2>";
        echo "<p>Vous pouvez maintenant vous connecter.</p>";
        echo "<a href='index.php'>Se connecter</a>";
    } else {
        echo "<h2>Erreur lors de l'inscription</h2>";
        echo "<p>" . $stmt_client->error . "</p>";
        echo "<a href='register.php'>Retour</a>";
    }
} else {
    echo "<h2>Erreur lors de l'inscription</h2>";
    echo "<p>" . $stmt_user->error . "</p>";
    echo "<a href='register.php'>Retour</a>";
}

$stmt_user->close();
?>