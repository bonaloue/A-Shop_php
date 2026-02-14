<?php
session_start();
include "db.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id']) || !isset($_SESSION['id_commande'])){
    header("Location: index.php");
    exit();
}

$id_commande = $_SESSION['id_commande'];

// Récupérer les détails de la commande
$sql = "SELECT * FROM commande WHERE id_commande = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_commande);
$stmt->execute();
$result = $stmt->get_result();
$commande = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - A-Shop</title>
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
    .payment-container{
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .payment-info{
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    .payment-info h3{
        color: #333;
        margin-bottom: 15px;
    }
    .info-row{
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .info-label{
        color: #666;
        font-weight: bold;
    }
    .info-value{
        color: #333;
    }
    .total-row{
        font-size: 20px;
        font-weight: bold;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #ddd;
    }
    .total-row .info-value{
        color: #6e8efb;
    }
    .payment-form label{
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }
    .payment-form select,
    .payment-form input{
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        font-size: 16px;
    }
    .payment-form select:focus,
    .payment-form input:focus{
        border-color: #6e8efb;
    }
    .btn-payer{
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
    .btn-payer:hover{
        background: #5b73ef;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">💳 Paiement</h1>
        
        <div class="payment-container">
            <div class="payment-info">
                <h3>Récapitulatif de la commande</h3>
                <div class="info-row">
                    <span class="info-label">Numéro de commande :</span>
                    <span class="info-value">#<?php echo $commande['id_commande']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date :</span>
                    <span class="info-value"><?php echo date('d/m/Y', strtotime($commande['date_commande'])); ?></span>
                </div>
                <div class="info-row total-row">
                    <span class="info-label">Montant total :</span>
                    <span class="info-value"><?php echo number_format($commande['total'], 0, ',', ' '); ?> FCFA</span>
                </div>
            </div>
            
            <form action="traitement_paiement.php" method="post" class="payment-form">
                <label for="mode_paiement">Mode de paiement</label>
                <select name="mode_paiement" required>
                    <option value="">Choisir un mode de paiement</option>
                    <option value="mobile_money">Mobile Money (Orange Money / Moov Money)</option>
                    <option value="carte_bancaire">Carte bancaire</option>
                    <option value="especes">Espèces à la livraison</option>
                </select>
                
                <label for="numero_telephone">Numéro de téléphone</label>
                <input type="text" name="numero_telephone" placeholder="+226 70 12 34 56" required>
                
                <button type="submit" class="btn-payer">Confirmer le paiement</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>