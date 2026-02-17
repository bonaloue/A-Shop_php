<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

$sql_client = "SELECT id_client FROM client WHERE id_user = ?";
$stmt = $conn->prepare($sql_client);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result_client = $stmt->get_result();
$client = $result_client->fetch_assoc();

$sql = "SELECT c.*, p.statut as statut_paiement, p.mode_paiement, l.statut as statut_livraison, l.adresse_livraison 
        FROM commande c 
        LEFT JOIN paiement p ON c.id_commande = p.id_commande 
        LEFT JOIN livraison l ON c.id_commande = l.id_commande 
        WHERE c.id_client = ? 
        ORDER BY c.date_commande DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client['id_client']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - A-Shop</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}
.container{max-width:1200px;margin:30px auto;padding:0 20px;}
.page-header{background:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.page-title{color:#333;font-size:32px;display:flex;align-items:center;gap:15px;}
.success-message{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.commande-card{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px;}
.commande-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #eee;}
.commande-id{font-size:20px;font-weight:bold;color:#333;display:flex;align-items:center;gap:8px;}
.commande-date{color:#666;margin-top:5px;display:flex;align-items:center;gap:5px;}
.commande-total{display:flex;align-items:center;gap:8px;font-size:24px;font-weight:bold;color:#6e8efb;}
.commande-body{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:20px;}
.info-block{background:#f8f8ff;padding:15px;border-radius:8px;}
.info-label{font-size:12px;color:#666;margin-bottom:5px;text-transform:uppercase;display:flex;align-items:center;gap:5px;}
.info-value{font-size:16px;color:#333;font-weight:bold;}
.status-badge{padding:5px 15px;border-radius:20px;font-size:12px;font-weight:bold;display:inline-flex;align-items:center;gap:5px;}
.status-en_attente{background:#fff3cd;color:#856404;}
.status-confirmee,.status-valide{background:#d4edda;color:#155724;}
.status-en_cours{background:#cfe2ff;color:#084298;}
.status-livree{background:#d1ecf1;color:#0c5460;}
.timeline{margin-top:20px;padding-top:20px;border-top:1px solid #eee;}
.timeline-title{font-weight:bold;color:#333;margin-bottom:15px;display:flex;align-items:center;gap:8px;}
.timeline-steps{display:flex;justify-content:space-between;position:relative;}
.timeline-step{flex:1;text-align:center;position:relative;}
.timeline-step::before{content:'';position:absolute;top:20px;left:0;right:0;height:2px;background:#ddd;z-index:0;}
.timeline-step:first-child::before{left:50%;}
.timeline-step:last-child::before{right:50%;}
.step-icon{width:40px;height:40px;border-radius:50%;background:#ddd;color:white;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;position:relative;z-index:1;font-size:20px;}
.step-active .step-icon{background:#6e8efb;animation:pulse 2s infinite;}
.step-completed .step-icon{background:#44ff44;}
.step-label{font-size:12px;color:#666;margin-top:5px;}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(110,142,251,0.7);}50%{box-shadow:0 0 0 10px rgba(110,142,251,0);}}
.no-commandes{text-align:center;padding:50px;background:white;border-radius:10px;color:#666;}
.adresse-block{margin-top:15px;padding:15px;background:#f8f8f8;border-radius:5px;display:flex;align-items:start;gap:10px;}
.adresse-block ion-icon{font-size:20px;color:#6e8efb;margin-top:2px;}
</style>

<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <ion-icon name="receipt-outline" style="font-size:36px;"></ion-icon> 
                Mes Commandes
            </h1>
        </div>
        
        <?php if(isset($_GET['success'])): ?>
        <div class="success-message">
            <ion-icon name="checkmark-circle-outline" style="font-size:24px;"></ion-icon>
            Commande passée avec succès ! Vous recevrez une confirmation une fois le paiement validé.
        </div>
        <?php endif; ?>
        
        <?php if($result->num_rows > 0): ?>
            <?php while($cmd = $result->fetch_assoc()): ?>
            <div class="commande-card">
                <div class="commande-header">
                    <div>
                        <div class="commande-id">
                            <ion-icon name="bag-handle-outline"></ion-icon>
                            Commande #<?php echo $cmd['id_commande']; ?>
                        </div>
                        <div class="commande-date">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <?php echo date('d/m/Y à H:i', strtotime($cmd['date_commande'])); ?>
                        </div>
                    </div>
                    <div class="commande-total">
                        <ion-icon name="cash-outline"></ion-icon>
                        <?php echo number_format($cmd['total'], 0, ',', ' '); ?> FCFA
                    </div>
                </div>
                
                <div class="commande-body">
                    <div class="info-block">
                        <div class="info-label">
                            <ion-icon name="list-outline"></ion-icon>
                            Statut commande
                        </div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $cmd['statut']; ?>">
                                <ion-icon name="<?php echo $cmd['statut'] == 'confirmee' ? 'checkmark-circle-outline' : 'time-outline'; ?>"></ion-icon>
                                <?php echo ucfirst(str_replace('_', ' ', $cmd['statut'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-block">
                        <div class="info-label">
                            <ion-icon name="card-outline"></ion-icon>
                            Paiement
                        </div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $cmd['statut_paiement']; ?>">
                                <ion-icon name="<?php echo $cmd['statut_paiement'] == 'valide' ? 'checkmark-done-outline' : 'card-outline'; ?>"></ion-icon>
                                <?php echo ucfirst(str_replace('_', ' ', $cmd['statut_paiement'])); ?>
                            </span>
                            <div style="font-size:12px;color:#666;margin-top:5px;display:flex;align-items:center;gap:5px;">
                                <ion-icon name="wallet-outline"></ion-icon>
                                <?php echo ucfirst(str_replace('_', ' ', $cmd['mode_paiement'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-block">
                        <div class="info-label">
                            <ion-icon name="rocket-outline"></ion-icon>
                            Livraison
                        </div>
                        <div class="info-value">
                            <span class="status-badge status-<?php echo $cmd['statut_livraison']; ?>">
                                <ion-icon name="<?php echo $cmd['statut_livraison'] == 'livree' ? 'checkmark-done-outline' : ($cmd['statut_livraison'] == 'en_cours' ? 'navigate-outline' : 'time-outline'); ?>"></ion-icon>
                                <?php echo ucfirst(str_replace('_', ' ', $cmd['statut_livraison'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="timeline">
                    <div class="timeline-title">
                        <ion-icon name="git-network-outline"></ion-icon>
                        Suivi de commande
                    </div>
                    <div class="timeline-steps">
                        <div class="timeline-step <?php echo in_array($cmd['statut'], ['en_attente','confirmee']) ? 'step-completed' : ''; ?>">
                            <div class="step-icon">
                                <ion-icon name="cart-outline"></ion-icon>
                            </div>
                            <div class="step-label">Commandé</div>
                        </div>
                        
                        <div class="timeline-step <?php echo $cmd['statut_paiement'] == 'valide' ? 'step-completed' : ($cmd['statut_paiement'] == 'en_attente' ? 'step-active' : ''); ?>">
                            <div class="step-icon">
                                <ion-icon name="card-outline"></ion-icon>
                            </div>
                            <div class="step-label">Paiement</div>
                        </div>
                        
                        <div class="timeline-step <?php echo $cmd['statut_livraison'] == 'livree' ? 'step-completed' : ($cmd['statut_livraison'] == 'en_cours' ? 'step-active' : ''); ?>">
                            <div class="step-icon">
                                <ion-icon name="rocket-outline"></ion-icon>
                            </div>
                            <div class="step-label">Livraison</div>
                        </div>
                        
                        <div class="timeline-step <?php echo $cmd['statut_livraison'] == 'livree' ? 'step-completed' : ''; ?>">
                            <div class="step-icon">
                                <ion-icon name="checkmark-done-outline"></ion-icon>
                            </div>
                            <div class="step-label">Reçu</div>
                        </div>
                    </div>
                </div>
                
                <?php if(!empty($cmd['adresse_livraison'])): ?>
                <div class="adresse-block">
                    <ion-icon name="location-outline"></ion-icon>
                    <div>
                        <strong>Adresse de livraison:</strong><br>
                        <?php echo $cmd['adresse_livraison']; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-commandes">
                <ion-icon name="cart-outline" style="font-size:60px;margin-bottom:20px;color:#ccc;"></ion-icon>
                <h2>Aucune commande</h2>
                <p style="margin-top:10px;">Vous n'avez pas encore passé de commande.</p>
                <a href="boutique.php" style="display:inline-flex;align-items:center;gap:10px;margin-top:20px;background:#6e8efb;color:white;padding:12px 30px;border-radius:5px;text-decoration:none;">
                    <ion-icon name="storefront-outline"></ion-icon>
                    Découvrir la boutique
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>