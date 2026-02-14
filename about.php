<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - A-Shop</title>
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
        max-width: auto;
        margin: 50px auto;
        padding: 0 20px;
    }
    .about-section{
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .about-section h1{
        color: #6e8efb;
        margin-bottom: 20px;
        font-size: 36px;
    }
    .about-section h2{
        color: #333;
        margin: 30px 0 15px;
        font-size: 24px;
    }
    .about-section p{
        color: #666;
        line-height: 1.8;
        margin-bottom: 15px;
        font-size: 16px;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="about-section">
            <h1>À propos de A-Shop</h1>
            
            <p>A-Shop est votre plateforme de commerce électronique de confiance au Burkina Faso. Nous nous engageons à vous offrir une expérience d'achat en ligne simple, sécurisée et agréable.</p>
            
            <h2>Notre Mission</h2>
            <p>Faciliter l'accès aux produits de qualité pour tous les Burkinabè, où qu'ils se trouvent. Nous croyons que chacun mérite d'avoir accès à des produits authentiques à des prix justes.</p>
            
            <h2>Nos Valeurs</h2>
            <p><strong>Qualité :</strong> Nous sélectionnons soigneusement chaque produit pour garantir la meilleure qualité.</p>
            <p><strong>Confiance :</strong> La transparence et l'honnêteté sont au cœur de nos relations avec nos clients.</p>
            <p><strong>Service :</strong> Votre satisfaction est notre priorité absolue.</p>
            
        </div>
    </div>
</body>
</html>