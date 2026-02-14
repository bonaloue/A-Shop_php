<?php
session_start();
include "db.php";

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// Initialiser le panier s'il n'existe pas
if(!isset($_SESSION['panier'])){
    $_SESSION['panier'] = array();
}

// Récupérer les détails des produits dans le panier
$produits_panier = array();
$total = 0;

if(count($_SESSION['panier']) > 0){
    $ids = implode(',', array_keys($_SESSION['panier']));
    $sql = "SELECT * FROM produit WHERE id_produit IN ($ids)";
    $result = $conn->query($sql);
    
    while($produit = $result->fetch_assoc()){
        $produit['quantite'] = $_SESSION['panier'][$produit['id_produit']];
        $produit['sous_total'] = $produit['prix'] * $produit['quantite'];
        $total += $produit['sous_total'];
        $produits_panier[] = $produit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - A-Shop</title>
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
        max-width: 1000px;
        margin: 30px auto;
        padding: 0 20px;
    }
    .page-title{
        text-align: center;
        color: #333;
        margin: 30px 0;
        font-size: 36px;
    }
    .panier-container{
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .panier-item{
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    .item-info{
        flex: 1;
    }
    .item-name{
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }
    .item-price{
        color: #666;
        font-size: 14px;
    }
    .item-quantity{
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 0 30px;
    }
    .quantity-btn{
        background: #6e8efb;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
    }
    .quantity-btn:hover{
        background: #5b73ef;
    }
    .quantity-value{
        font-weight: bold;
        font-size: 16px;
    }
    .item-total{
        font-size: 20px;
        font-weight: bold;
        color: #6e8efb;
        min-width: 150px;
        text-align: right;
    }
    .btn-remove{
        background: #ff4444;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-remove:hover{
        background: #cc0000;
    }
    .panier-footer{
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #eee;
    }
    .total-row{
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 30px;
    }
    .total-label{
        color: #333;
    }
    .total-amount{
        color: #6e8efb;
    }
    .btn-commander{
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
    .btn-commander:hover{
        background: #5b73ef;
    }
    .panier-vide{
        text-align: center;
        padding: 50px;
        color: #666;
    }
    .btn-continuer{
        display: inline-block;
        margin-top: 20px;
        padding: 12px 30px;
        background: #6e8efb;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.3s;
    }
    .btn-continuer:hover{
        background: #5b73ef;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">Mon Panier</h1>
        
        <?php if(count($produits_panier) > 0): ?>
            <div class="panier-container">
                <?php foreach($produits_panier as $produit): ?>
                    <div class="panier-item">
                        <div class="item-info">
                            <div class="item-name"><?php echo $produit['nom']; ?></div>
                            <div class="item-price"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> FCFA / unité</div>
                        </div>
                        
                        <div class="item-quantity">
                            <form action="modifier_panier.php" method="post" style="display: inline;">
                                <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                <input type="hidden" name="action" value="diminuer">
                                <button type="submit" class="quantity-btn">-</button>
                            </form>
                            
                            <span class="quantity-value"><?php echo $produit['quantite']; ?></span>
                            
                            <form action="modifier_panier.php" method="post" style="display: inline;">
                                <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                <input type="hidden" name="action" value="augmenter">
                                <button type="submit" class="quantity-btn">+</button>
                            </form>
                        </div>
                        
                        <div class="item-total">
                            <?php echo number_format($produit['sous_total'], 0, ',', ' '); ?> FCFA
                        </div>
                        
                        <form action="modifier_panier.php" method="post" style="display: inline;">
                            <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                            <input type="hidden" name="action" value="supprimer">
                            <button type="submit" class="btn-remove">Retirer</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                
                <div class="panier-footer">
                    <div class="total-row">
                        <span class="total-label">Total :</span>
                        <span class="total-amount"><?php echo number_format($total, 0, ',', ' '); ?> FCFA</span>
                    </div>
                    
                    <form action="passer_commande.php" method="post">
                        <button type="submit" class="btn-commander">Passer la commande</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="panier-container">
                <div class="panier-vide">
                    <h2>🛒 Votre panier est vide</h2>
                    <p>Commencez vos achats dès maintenant !</p>
                    <a href="boutique.php" class="btn-continuer">Continuer mes achats</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>