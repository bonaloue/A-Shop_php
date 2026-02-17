<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

if(!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0){
    header("Location: boutique.php");
    exit();
}

$sql_client = "SELECT id_client FROM client WHERE id_user = ?";
$stmt = $conn->prepare($sql_client);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result_client = $stmt->get_result();
$client = $result_client->fetch_assoc();

if(!$client){
    die("Erreur: Client non trouvé");
}

$ids = implode(',', array_keys($_SESSION['panier']));
$sql = "SELECT * FROM produit WHERE id_produit IN ($ids)";
$result = $conn->query($sql);

$total = 0;
while($prod = $result->fetch_assoc()){
    $quantite = $_SESSION['panier'][$prod['id_produit']];
    $total += $prod['prix'] * $quantite;
}

if(isset($_POST['confirmer_commande'])){
    $adresse_livraison = $_POST['adresse_livraison'];
    $mode_paiement = $_POST['mode_paiement'];
    
    $conn->begin_transaction();
    
    try {
        $sql_commande = "INSERT INTO commande (id_client, total, statut) VALUES (?, ?, 'en_attente')";
        $stmt = $conn->prepare($sql_commande);
        $stmt->bind_param("id", $client['id_client'], $total);
        $stmt->execute();
        $id_commande = $conn->insert_id;
        
        $sql_ligne = "INSERT INTO ligne_commande (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)";
        $stmt_ligne = $conn->prepare($sql_ligne);
        
        foreach($_SESSION['panier'] as $id_produit => $quantite){
            $sql_prod = "SELECT prix FROM produit WHERE id_produit = ?";
            $stmt_prod = $conn->prepare($sql_prod);
            $stmt_prod->bind_param("i", $id_produit);
            $stmt_prod->execute();
            $prix = $stmt_prod->get_result()->fetch_assoc()['prix'];
            
            $stmt_ligne->bind_param("iiid", $id_commande, $id_produit, $quantite, $prix);
            $stmt_ligne->execute();
        }
        
        $sql_paiement = "INSERT INTO paiement (id_commande, montant, mode_paiement, statut) VALUES (?, ?, ?, 'en_attente')";
        $stmt_paiement = $conn->prepare($sql_paiement);
        $stmt_paiement->bind_param("ids", $id_commande, $total, $mode_paiement);
        $stmt_paiement->execute();
        
        $sql_livraison = "INSERT INTO livraison (id_commande, adresse_livraison, statut) VALUES (?, ?, 'en_attente')";
        $stmt_livraison = $conn->prepare($sql_livraison);
        $stmt_livraison->bind_param("is", $id_commande, $adresse_livraison);
        $stmt_livraison->execute();
        
        $conn->commit();
        
        unset($_SESSION['panier']);
        
        header("Location: mes-commandes.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Erreur lors de la commande: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser la commande - A-Shop</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}
.container{max-width:800px;margin:30px auto;padding:0 20px;}
.page-header{background:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.page-title{color:#333;font-size:32px;display:flex;align-items:center;gap:15px;}
.form-card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.form-section{margin-bottom:30px;}
.section-title{color:#333;font-size:20px;font-weight:bold;margin-bottom:15px;display:flex;align-items:center;gap:10px;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;color:#333;font-weight:bold;margin-bottom:8px;display:flex;align-items:center;gap:8px;}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:12px;border:1px solid #ddd;border-radius:5px;font-size:16px;}
.form-group textarea{min-height:100px;resize:vertical;}
.payment-options{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;}
.payment-option{border:2px solid #ddd;border-radius:8px;padding:20px;cursor:pointer;text-align:center;transition:0.3s;}
.payment-option:hover{border-color:#6e8efb;background:#f8f8ff;}
.payment-option input[type="radio"]{display:none;}
.payment-option input[type="radio"]:checked + label{color:#6e8efb;font-weight:bold;}
.payment-option:has(input[type="radio"]:checked){border-color:#6e8efb;background:#f8f8ff;}
.payment-option label{cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:10px;font-size:18px;}
.payment-icon{font-size:40px;color:#6e8efb;}
.order-summary{background:#f8f8ff;padding:20px;border-radius:8px;margin-bottom:20px;}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #ddd;}
.summary-row ion-icon{font-size:20px;color:#666;}
.summary-total{font-size:24px;font-weight:bold;color:#6e8efb;padding-top:15px;}
.btn-submit{width:100%;background:#6e8efb;color:white;border:none;padding:15px;border-radius:5px;font-size:18px;font-weight:bold;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;}
.btn-submit:hover{background:#5b73ef;}
.error-message{background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
</style>

<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <ion-icon name="checkmark-circle-outline" style="font-size:36px;"></ion-icon> 
                Finaliser la commande
            </h1>
        </div>
        
        <?php if(isset($error)): ?>
        <div class="error-message">
            <ion-icon name="alert-circle-outline" style="font-size:24px;"></ion-icon>
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="post" class="form-card">
            <div class="form-section">
                <div class="section-title">
                    <ion-icon name="location-outline"></ion-icon> 
                    Adresse de livraison
                </div>
                <div class="form-group">
                    <label>
                        <ion-icon name="home-outline"></ion-icon>
                        Adresse complète *
                    </label>
                    <textarea name="adresse_livraison" required placeholder="Entrez votre adresse de livraison complète (ville, quartier, rue, point de repère...)"></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <ion-icon name="card-outline"></ion-icon> 
                    Mode de paiement
                </div>
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" name="mode_paiement" value="orange_money" id="orange" required>
                        <label for="orange">
                            <ion-icon name="phone-portrait-outline" class="payment-icon"></ion-icon>
                            Orange Money
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" name="mode_paiement" value="moov_money" id="moov">
                        <label for="moov">
                            <ion-icon name="phone-portrait-outline" class="payment-icon"></ion-icon>
                            Moov Money
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" name="mode_paiement" value="paiement_livraison" id="cash">
                        <label for="cash">
                            <ion-icon name="cash-outline" class="payment-icon"></ion-icon>
                            À la livraison
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <ion-icon name="receipt-outline"></ion-icon> 
                    Récapitulatif
                </div>
                <div class="order-summary">
                    <div class="summary-row">
                        <span style="display:flex;align-items:center;gap:8px;">
                            <ion-icon name="cube-outline"></ion-icon>
                            Nombre d'articles
                        </span>
                        <span><?php echo array_sum($_SESSION['panier']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span style="display:flex;align-items:center;gap:8px;">
                            <ion-icon name="calculator-outline"></ion-icon>
                            Sous-total
                        </span>
                        <span><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-total" style="display:flex;align-items:center;gap:8px;">
                            <ion-icon name="cash-outline"></ion-icon>
                            Total à payer
                        </span>
                        <span class="summary-total"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="confirmer_commande" class="btn-submit">
                <ion-icon name="checkmark-done-outline"></ion-icon> 
                Confirmer la commande
            </button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>