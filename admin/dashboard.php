<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$sql_stats = "SELECT 
    (SELECT COUNT(*) FROM produit) as nb_produits,
    (SELECT COUNT(*) FROM commande) as nb_commandes,
    (SELECT COUNT(*) FROM utilisateur WHERE role='client') as nb_clients,
    (SELECT COALESCE(SUM(total), 0) FROM commande WHERE statut='confirmee') as ca_total";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

$sql_commandes = "SELECT c.*, u.nom, u.email 
                  FROM commande c
                  INNER JOIN client cl ON c.id_client = cl.id_client
                  INNER JOIN utilisateur u ON cl.id_user = u.id_user
                  ORDER BY c.date_commande DESC LIMIT 5";
$result_commandes = $conn->query($sql_commandes);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - A-Shop</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;}
.header{background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:white;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}
.header h1{font-size:28px;display:flex;align-items:center;gap:10px;}
.user-info{display:flex;align-items:center;gap:15px;}
.btn-logout{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:flex;align-items:center;gap:8px;}
.btn-logout:hover{background:#f8f8f8;}
.container{max-width:1400px;margin:30px auto;padding:0 20px;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-bottom:30px;}
.stat-card{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);transition:0.3s;}
.stat-card:hover{transform:translateY(-5px);box-shadow:0 5px 20px rgba(0,0,0,0.2);}
.stat-header{display:flex;align-items:center;gap:15px;margin-bottom:15px;}
.stat-icon{font-size:40px;color:#ff6b6b;}
.stat-value{font-size:32px;font-weight:bold;color:#333;margin-bottom:5px;}
.stat-label{color:#666;font-size:14px;}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-bottom:30px;}
.menu-card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);text-align:center;transition:0.3s;text-decoration:none;color:inherit;}
.menu-card:hover{transform:translateY(-5px);box-shadow:0 5px 20px rgba(0,0,0,0.2);}
.menu-icon{font-size:50px;margin-bottom:15px;color:#ff6b6b;}
.menu-card h3{color:#333;margin-bottom:10px;}
.menu-card p{color:#666;font-size:14px;}
.recent-orders{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.recent-orders h2{color:#333;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.order-item{padding:15px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;}
.order-item:last-child{border-bottom:none;}
.order-info{flex:1;}
.order-id{font-weight:bold;color:#333;}
.order-client{color:#666;font-size:14px;margin-top:5px;}
.order-status{padding:5px 15px;border-radius:20px;font-size:12px;font-weight:bold;}
.status-en_attente{background:#fff3cd;color:#856404;}
.status-confirmee{background:#d4edda;color:#155724;}
.order-amount{font-weight:bold;color:#ff6b6b;margin-left:20px;}
</style>

<body>
    <div class="header">
        <div class="header-content">
            <h1><ion-icon name="shield-checkmark"></ion-icon> Administration A-Shop</h1>
            <div class="user-info">
                <span>Bonjour, <?php echo $_SESSION['nom']; ?></span>
                <a href="../logout.php" class="btn-logout">
                    <ion-icon name="log-out-outline"></ion-icon>
                    Déconnexion
                </a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <ion-icon name="cube-outline" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-value"><?php echo $stats['nb_produits']; ?></div>
                <div class="stat-label">Produits</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <ion-icon name="cart-outline" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-value"><?php echo $stats['nb_commandes']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <ion-icon name="people-outline" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-value"><?php echo $stats['nb_clients']; ?></div>
                <div class="stat-label">Clients</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <ion-icon name="cash-outline" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-value"><?php echo number_format($stats['ca_total'],0,',',' '); ?> FCFA</div>
                <div class="stat-label">Chiffre d'affaires</div>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="produits.php" class="menu-card">
                <ion-icon name="cube-outline" class="menu-icon"></ion-icon>
                <h3>Gestion Produits</h3>
                <p>Ajouter, modifier, supprimer des produits</p>
            </a>
            
            <a href="categories.php" class="menu-card">
                <ion-icon name="pricetags-outline" class="menu-icon"></ion-icon>
                <h3>Gestion Catégories</h3>
                <p>Gérer les catégories de produits</p>
            </a>
            
            <a href="commandes.php" class="menu-card">
                <ion-icon name="cart-outline" class="menu-icon"></ion-icon>
                <h3>Gestion Commandes</h3>
                <p>Voir et gérer toutes les commandes</p>
            </a>
            
            <a href="paiements.php" class="menu-card">
                <ion-icon name="card-outline" class="menu-icon"></ion-icon>
                <h3>Validation Paiements</h3>
                <p>Valider les paiements en attente</p>
            </a>
            
            <a href="livraisons.php" class="menu-card">
                <ion-icon name="rocket-outline" class="menu-icon"></ion-icon>
                <h3>Gestion Livraisons</h3>
                <p>Gérer et suivre les livraisons</p>
            </a>
            
            <a href="clients.php" class="menu-card">
                <ion-icon name="people-outline" class="menu-icon"></ion-icon>
                <h3>Liste Clients</h3>
                <p>Voir tous les clients inscrits</p>
            </a>
        </div>
        
        <div class="recent-orders">
            <h2><ion-icon name="time-outline"></ion-icon> Dernières commandes</h2>
            <?php if($result_commandes->num_rows > 0): ?>
                <?php while($cmd = $result_commandes->fetch_assoc()): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-id">Commande #<?php echo $cmd['id_commande']; ?></div>
                            <div class="order-client"><?php echo $cmd['nom']; ?> - <?php echo date('d/m/Y H:i',strtotime($cmd['date_commande'])); ?></div>
                        </div>
                        <span class="order-status status-<?php echo $cmd['statut']; ?>">
                            <?php echo ucfirst($cmd['statut']); ?>
                        </span>
                        <div class="order-amount"><?php echo number_format($cmd['total'],0,',',' '); ?> FCFA</div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center;color:#666;padding:20px;">Aucune commande</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>