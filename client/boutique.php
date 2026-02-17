<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}

if(!isset($_SESSION['panier'])){
    $_SESSION['panier'] = array();
}

if(isset($_POST['action'])){
    $id_produit = $_POST['id_produit'];
    
    if($_POST['action'] == 'ajouter'){
        if(isset($_SESSION['panier'][$id_produit])){
            $_SESSION['panier'][$id_produit]++;
        } else {
            $_SESSION['panier'][$id_produit] = 1;
        }
        $message_success = "Produit ajouté au panier !";
    }
    elseif($_POST['action'] == 'retirer'){
        if(isset($_SESSION['panier'][$id_produit])){
            unset($_SESSION['panier'][$id_produit]);
            $message_success = "Produit retiré du panier !";
        }
    }
}

$sql = "SELECT p.*, c.nom as categorie_nom 
        FROM produit p 
        LEFT JOIN categorie c ON p.id_categorie = c.id_categorie 
        ORDER BY p.date_ajout DESC";
$result = $conn->query($sql);

$total_panier = 0;
$nb_articles = 0;
if(count($_SESSION['panier']) > 0){
    $ids = implode(',', array_keys($_SESSION['panier']));
    $sql_panier = "SELECT * FROM produit WHERE id_produit IN ($ids)";
    $result_panier = $conn->query($sql_panier);
    
    while($prod = $result_panier->fetch_assoc()){
        $quantite = $_SESSION['panier'][$prod['id_produit']];
        $total_panier += $prod['prix'] * $quantite;
        $nb_articles += $quantite;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique - A-Shop</title>
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
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 20px;
    }
    .page-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-title{
        color: #333;
        font-size: 36px;
    }
    .panier-widget{
        background: white;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .panier-info{
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .panier-count{
        font-size: 24px;
    }
    .panier-total{
        font-size: 18px;
        color: #6e8efb;
        font-weight: bold;
    }
    .btn-voir-panier{
        background: #6e8efb;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        transition: 0.3s;
    }
    .btn-voir-panier:hover{
        background: #5b73ef;
    }
    .products-grid{
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    .product-card{
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: 0.3s;
        position: relative;
    }
    .product-card:hover{
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .product-image{
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 48px;
        overflow: hidden;
    }
    .product-image img{
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-info{
        padding: 20px;
    }
    .product-category{
        color: #6e8efb;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .product-name{
        color: #333;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .product-description{
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        line-height: 1.4;
    }
    .product-footer{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }
    .product-price{
        color: #6e8efb;
        font-size: 20px;
        font-weight: bold;
    }
    .product-stock{
        font-size: 13px;
        color: #666;
    }
    .product-actions{
        margin-top: 15px;
        display: flex;
        gap: 10px;
    }
    .btn-action{
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-ajouter{
        background: #6e8efb;
        color: white;
    }
    .btn-ajouter:hover{
        background: #5b73ef;
    }
    .btn-retirer{
        background: #ff4444;
        color: white;
    }
    .btn-retirer:hover{
        background: #cc0000;
    }
    .in-cart-badge{
        position: absolute;
        top: 10px;
        right: 10px;
        background: #44ff44;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .no-products{
        text-align: center;
        padding: 50px;
        color: #666;
        font-size: 18px;
        background: white;
        border-radius: 10px;
    }
    .message-success{
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
</style>

<body>
    <?php include '../navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Notre Boutique</h1>
            
            <?php if($nb_articles > 0): ?>
            <div class="panier-widget">
                <div class="panier-info">
                    <div>
                        <div class="panier-count"><?php echo $nb_articles; ?> article<?php echo $nb_articles > 1 ? 's' : ''; ?></div>
                        <div class="panier-total"><?php echo number_format($total_panier, 0, ',', ' '); ?> FCFA</div>
                    </div>
                    <a href="panier.php" class="btn-voir-panier">Voir le panier</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(isset($message_success)): ?>
        <div class="message-success"><?php echo $message_success; ?></div>
        <?php endif; ?>
        
        <?php if($result->num_rows > 0): ?>
            <div class="products-grid">
                <?php while($produit = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <?php if(isset($_SESSION['panier'][$produit['id_produit']])): ?>
                            <div class="in-cart-badge">Dans le panier (<?php echo $_SESSION['panier'][$produit['id_produit']]; ?>)</div>
                        <?php endif; ?>
                        
                        <div class="product-image">
                            <?php if(!empty($produit['image']) && file_exists('../image/' . $produit['image'])): ?>
                                <img src="../image/<?php echo $produit['image']; ?>" alt="<?php echo $produit['nom']; ?>">
                            <?php else: ?>
                                <div style="font-size:60px;color:white;"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category"><?php echo $produit['categorie_nom'] ? $produit['categorie_nom'] : 'Sans catégorie'; ?></div>
                            <div class="product-name"><?php echo $produit['nom']; ?></div>
                            <div class="product-description">
                                <?php 
                                    $description = $produit['description'];
                                    echo strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description;
                                ?>
                            </div>
                            <div class="product-footer">
                                <div>
                                    <div class="product-price"><?php echo number_format($produit['prix'], 0, ',', ' '); ?> FCFA</div>
                                    <div class="product-stock">Stock: <?php echo $produit['stock']; ?></div>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <form action="boutique.php" method="post" style="flex: 1;">
                                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                    <input type="hidden" name="action" value="ajouter">
                                    <button type="submit" class="btn-action btn-ajouter">
                                        <?php echo isset($_SESSION['panier'][$produit['id_produit']]) ? '+ Ajouter encore' : 'Ajouter'; ?>
                                    </button>
                                </form>
                                
                                <?php if(isset($_SESSION['panier'][$produit['id_produit']])): ?>
                                <form action="boutique.php" method="post" style="flex: 1;">
                                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                    <input type="hidden" name="action" value="retirer">
                                    <button type="submit" class="btn-action btn-retirer">Retirer</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                <p>Aucun produit disponible pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>