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

$ids = implode(',', array_keys($_SESSION['panier']));
$sql = "SELECT * FROM produit WHERE id_produit IN ($ids)";
$result = $conn->query($sql);

$total = 0;
$produits = array();
while($prod = $result->fetch_assoc()){
    $quantite = $_SESSION['panier'][$prod['id_produit']];
    $prod['quantite'] = $quantite;
    $prod['sous_total'] = $prod['prix'] * $quantite;
    $total += $prod['sous_total'];
    $produits[] = $prod;
}

if(isset($_POST['modifier_quantite'])){
    $id_produit = $_POST['id_produit'];
    $nouvelle_quantite = max(1, $_POST['quantite']);
    $_SESSION['panier'][$id_produit] = $nouvelle_quantite;
    header("Location: panier.php");
    exit();
}

if(isset($_POST['supprimer'])){
    $id_produit = $_POST['id_produit'];
    unset($_SESSION['panier'][$id_produit]);
    header("Location: panier.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - A-Shop</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}
.container{max-width:1200px;margin:30px auto;padding:0 20px;}
.page-header{background:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.page-title{color:#333;font-size:32px;display:flex;align-items:center;gap:15px;}
.cart-grid{display:grid;grid-template-columns:2fr 1fr;gap:30px;}
.cart-items{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.cart-item{display:grid;grid-template-columns:100px 1fr auto;gap:20px;padding:20px;border-bottom:1px solid #eee;}
.cart-item:last-child{border-bottom:none;}
.item-image{width:100px;height:100px;background:#f0f0f0;border-radius:8px;overflow:hidden;}
.item-image img{width:100%;height:100%;object-fit:cover;}
.no-image{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:40px;color:#999;}
.item-info{flex:1;}
.item-name{font-size:18px;font-weight:bold;color:#333;margin-bottom:5px;display:flex;align-items:center;gap:8px;}
.item-price{color:#6e8efb;font-size:16px;font-weight:bold;margin-bottom:10px;display:flex;align-items:center;gap:5px;}
.quantity-control{display:flex;align-items:center;gap:10px;margin-top:10px;}
.quantity-control label{display:flex;align-items:center;gap:5px;font-size:14px;color:#666;}
.quantity-control input{width:60px;padding:8px;border:1px solid #ddd;border-radius:5px;text-align:center;}
.btn-update{background:#6e8efb;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;display:flex;align-items:center;gap:5px;font-size:14px;}
.btn-update:hover{background:#5b73ef;}
.item-actions{display:flex;flex-direction:column;gap:10px;align-items:flex-end;}
.item-subtotal{font-size:20px;font-weight:bold;color:#333;display:flex;align-items:center;gap:5px;}
.btn-remove{background:#ff4444;color:white;border:none;padding:8px 15px;border-radius:5px;cursor:pointer;display:flex;align-items:center;gap:5px;font-size:14px;}
.btn-remove:hover{background:#cc0000;}
.cart-summary{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);height:fit-content;}
.summary-title{font-size:20px;font-weight:bold;color:#333;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
.summary-row{display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-bottom:1px solid #eee;}
.summary-row ion-icon{font-size:20px;color:#666;}
.summary-total{font-size:24px;font-weight:bold;color:#6e8efb;padding-top:15px;}
.btn-checkout{width:100%;background:#6e8efb;color:white;border:none;padding:15px;border-radius:5px;font-size:16px;font-weight:bold;cursor:pointer;margin-top:20px;display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none;}
.btn-checkout:hover{background:#5b73ef;}
.btn-continue{width:100%;background:white;color:#6e8efb;border:2px solid #6e8efb;padding:15px;border-radius:5px;font-size:16px;font-weight:bold;cursor:pointer;margin-top:10px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:10px;}
.btn-continue:hover{background:#f8f8ff;}
@media (max-width: 768px){
    .cart-grid{grid-template-columns:1fr;}
    .cart-item{grid-template-columns:80px 1fr;}
    .item-actions{grid-column:2;margin-top:10px;}
}
</style>

<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <ion-icon name="cart" style="font-size:36px;"></ion-icon> 
                Mon Panier
            </h1>
        </div>
        
        <div class="cart-grid">
            <div class="cart-items">
                <?php foreach($produits as $prod): ?>
                <div class="cart-item">
                    <div class="item-image">
                        <?php if(!empty($prod['image']) && file_exists('../image/' . $prod['image'])): ?>
                            <img src="../image/<?php echo $prod['image']; ?>" alt="<?php echo $prod['nom']; ?>">
                        <?php else: ?>
                            <div class="no-image"><ion-icon name="cube-outline"></ion-icon></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item-info">
                        <div class="item-name">
                            <ion-icon name="pricetag-outline"></ion-icon>
                            <?php echo $prod['nom']; ?>
                        </div>
                        <div class="item-price">
                            <ion-icon name="cash-outline"></ion-icon>
                            <?php echo number_format($prod['prix'], 0, ',', ' '); ?> FCFA
                        </div>
                        
                        <form method="post" class="quantity-control">
                            <input type="hidden" name="id_produit" value="<?php echo $prod['id_produit']; ?>">
                            <label>
                                <ion-icon name="layers-outline"></ion-icon>
                                Quantité:
                            </label>
                            <input type="number" name="quantite" value="<?php echo $prod['quantite']; ?>" min="1" max="<?php echo $prod['stock']; ?>">
                            <button type="submit" name="modifier_quantite" class="btn-update">
                                <ion-icon name="refresh-outline"></ion-icon> Modifier
                            </button>
                        </form>
                    </div>
                    
                    <div class="item-actions">
                        <div class="item-subtotal">
                            <ion-icon name="calculator-outline"></ion-icon>
                            <?php echo number_format($prod['sous_total'], 0, ',', ' '); ?> FCFA
                        </div>
                        <form method="post">
                            <input type="hidden" name="id_produit" value="<?php echo $prod['id_produit']; ?>">
                            <button type="submit" name="supprimer" class="btn-remove">
                                <ion-icon name="trash-outline"></ion-icon> Retirer
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-title">
                    <ion-icon name="receipt-outline"></ion-icon>
                    Récapitulatif
                </div>
                
                <div class="summary-row">
                    <span style="display:flex;align-items:center;gap:8px;">
                        <ion-icon name="cube-outline"></ion-icon>
                        Articles (<?php echo array_sum($_SESSION['panier']); ?>)
                    </span>
                    <span><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                </div>
                
                <div class="summary-row">
                    <span style="display:flex;align-items:center;gap:8px;">
                        <ion-icon name="rocket-outline"></ion-icon>
                        Livraison
                    </span>
                    <span>Calculée après</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-total" style="display:flex;align-items:center;gap:8px;">
                        <ion-icon name="cash-outline"></ion-icon>
                        Total
                    </span>
                    <span class="summary-total"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                </div>
                
                <a href="commander.php" class="btn-checkout">
                    <ion-icon name="card-outline"></ion-icon> Passer la commande
                </a>
                
                <a href="boutique.php" class="btn-continue">
                    <ion-icon name="arrow-back-outline"></ion-icon> Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>