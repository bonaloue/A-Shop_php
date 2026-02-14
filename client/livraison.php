<?php
session_start();
include "db.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id']) || !isset($_SESSION['id_commande'])){
    header("Location: index.php");
    exit();
}

$id_commande = $_SESSION['id_commande'];
$id_user = $_SESSION['user_id'];

// Récupérer les infos client
$sql_client = "SELECT c.*, u.telephone FROM client c 
               INNER JOIN utilisateur u ON c.id_user = u.id_user 
               WHERE c.id_user = ?";
$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param("i", $id_user);
$stmt_client->execute();
$result_client = $stmt_client->get_result();
$client = $result_client->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraison - A-Shop</title>
</head>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body{
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f5f5;
    }
    .container{
        max-width: 600px;
        margin: 50px auto;
        padding: 0 20px;
    }
    .page-title{
        text-align: center;
        color: #333;
        margin: 30px 0;
        font-size: 36px;
    }
    .delivery-container{
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .delivery-form label{
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }
    .delivery-form input,
    .delivery-form textarea{
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        font-size: 16px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .delivery-form input:focus,
    .delivery-form textarea:focus{
        border-color: #6e8efb;
    }
    .delivery-form textarea{
        resize: vertical;
        min-height: 100px;
    }
    .btn-valider{
        width: 100%;
        padding: 15px;
        background: #6e8efb;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-valider:hover{
        background: #5b73ef;
    }
    .info-box{
        background: #e8f4ff;
        border-left: 4px solid #6e8efb;
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 5px;
    }
    .info-box p{
        color: #555;
        line-height: 1.6;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">🚚 Livraison</h1>
        
        <div class="delivery-container">
            <div class="info-box">
                <p>📦 Commande #<?php echo $id_commande; ?> validée avec succès !</p>
                <p>Veuillez renseigner votre adresse de livraison.</p>
            </div>
            
            <form action="traitement_livraison.php" method="post" class="delivery-form">
                <label for="adresse_livraison">Adresse complète de livraison</label>
                <textarea name="adresse_livraison" placeholder="Secteur, rue, numéro..." required><?php echo $client['adresse'] ? $client['adresse'] : ''; ?></textarea>
                
                <label for="ville">Ville</label>
                <input type="text" name="ville" placeholder="Ouagadougou" value="<?php echo $client['ville'] ? $client['ville'] : ''; ?>" required>
                
                <label for="telephone">Téléphone de contact</label>
                <input type="text" name="telephone" placeholder="+226 70 12 34 56" value="<?php echo $client['telephone']; ?>" required>
                
                <button type="submit" class="btn-valider">Valider la livraison</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>