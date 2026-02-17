<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - A-Shop</title>
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
    .hero{
        background: linear-gradient(135deg, #eff1f6ff, #138fd2ff);
        color: white;
        text-align: center;
        padding: 100px 20px;
    }
    .hero h1{
        font-size: 48px;
        margin-bottom: 20px;
    }
    .hero p{
        font-size: 20px;
        margin-bottom: 30px;
    }
    .btn-shop{
        background: white;
        color: #6e8efb;
        padding: 15px 40px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 18px;
        display: inline-block;
        transition: 0.3s;
    }
    .btn-shop:hover{
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .container{
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
    }
    .features{
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }
    .feature-card{
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .feature-card:hover{
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .feature-card h3{
        color: #6e8efb;
        margin: 20px 0 10px;
        font-size: 22px;
    }
    .feature-card p{
        color: #666;
        line-height: 1.6;
    }
    .icon{
        font-size: 48px;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="hero">
        <h1>Bienvenue sur A-Shop</h1>
        <p>Votre boutique en ligne de confiance au Burkina Faso</p>
        <a href="client/boutique.php" class="btn-shop">Découvrir nos produits</a>
    </div>
    
    <div class="container">
        <div class="features">
            <div class="feature-card">
                <div class="icon"><ion-icon name="cart-outline"></ion-icon></div>
                <h3>Livraison rapide</h3>
                <p>Recevez vos commandes rapidement partout au Burkina Faso</p>
            </div>
            
            <div class="feature-card">
                <div class="icon"><ion-icon name="card-outline"></ion-icon></div>
                <h3>Paiement sécurisé</h3>
                <p>Payez en toute sécurité avec Mobile Money</p>
            </div>
            
            <div class="feature-card">
                <div class="icon"><ion-icon name="gift-outline"></ion-icon></div>
                <h3>Produits de qualité</h3>
                <p>Des produits authentiques et de haute qualité</p>
            </div>
            
            <div class="feature-card">
                <div class="icon"><ion-icon name="call-outline"></ion-icon></div>
                <h3>Service client</h3>
                <p>Une équipe à votre écoute 7j/7</p>
            </div>
        </div>
    </div>
</body>
</html>