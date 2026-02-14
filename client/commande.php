<?php
session_start();
include "db.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id']) || !isset($_GET['commande'])){
    header("Location: index.php");
    exit();
}

$id_commande = $_GET['commande'];

// Récupérer les détails de la commande
$sql = "SELECT c.*, p.mode_paiement, p.statut as statut_paiement, l.adresse_livraison, l.statut as statut_livraison
        FROM commande c
        LEFT JOIN paiement p ON c.id_commande = p.id_commande
        LEFT JOIN livraison l ON c.id_commande = l.id_commande
        WHERE c.id_commande = ?";
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
    <title>Confirmation - A-Shop</title>
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
        max-width: 700px;
        margin: 50px auto;
        padding: 0 20px;
    }
    .confirmation-container{
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .success-icon{
        font-size: 80px;
        margin-bottom: 20px;
    }
    .confirmation-container h1{
        color: #6e8efb;
        margin-bottom: 15px;
        font-size: 32px;
    }
    .confirmation-container p{
        color: #666;
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 30px;
    }
    .order-details{
        background: #f9f9f9;
        padding: 25px;
        border-radius: 8px;
        text-align: left;
        margin: 30px 0;
    }
    .order-details h3{
        color: #333;
        margin-bottom: 15px;
        text-align: center;
    }
    .detail-row{
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    .detail-label{
        color: #666;
        font-weight: bold;
    }
    .detail-value{
        color: #333;
    }
    .status-badge{
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-en-attente{
        background: #fff3cd;
        color: #856404;
    }
    .status-confirmee{
        background: #d4edda;
        color: #155724;
    }
    .btn-container{
        margin-top: 30px;
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    .btn{
        padding: 12px 30px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        transition: 0.3s;
        display: inline-block;
    }
    .btn-primary{
        background: #6e8efb;
        color: white;
    }
    .btn-primary:hover{
        background: #5b73ef;
    }
    .btn-secondary{
        background: #f0f0f0;
        color: #333;
    }
    .btn-secondary:hover{
        background: #e0e0e0;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="confirmation-container">
            <div class="success-icon">✅</div>
            <h1>Commande confirmée !</h1>
            <p>Merci pour votre commande. Vous recevrez une confirmation par email.<br>
            Votre colis sera livré dans les prochains jours.</p>
            
            <div class="order-details">
                <h3>📋 Détails de votre commande</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Numéro de commande :</span>
                    <span class="detail-value">#<?php echo $commande['id_commande']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date :</span>
                    <span class="detail-value"><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Montant total :</span>
                    <span class="detail-value"><?php echo number_format($commande['total'], 0, ',', ' '); ?> FCFA</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Mode de paiement :</span>
                    <span class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $commande['mode_paiement'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Statut commande :</span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo $commande['statut']; ?>">
                            <?php echo ucfirst($commande['statut']); ?>
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Statut paiement :</span>
                    <span class="detail-value">
                        <span class="status-badge status-<?php echo $commande['statut_paiement']; ?>">
                            <?php echo ucfirst($commande['statut_paiement']); ?>
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Adresse de livraison :</span>
                    <span class="detail-value"><?php echo $commande['adresse_livraison']; ?></span>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="mes_commandes.php" class="btn btn-primary">Voir mes commandes</a>
                <a href="boutique.php" class="btn btn-secondary">Continuer mes achats</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>