<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - A-Shop</title>
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
        margin: 50px auto;
        padding: 0 20px;
    }
    .page-title{
        text-align: center;
        color: #333;
        margin-bottom: 40px;
    }
    .contact-wrapper{
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        background: white;
        padding: 40px;
        border-radius: 8px;
    }
    .contact-section h2{
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
    }
    .contact-form label{
        display: block;
        margin-bottom: 5px;
        color: #555;
    }
    .contact-form input,
    .contact-form textarea{
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .contact-form textarea{
        resize: vertical;
        min-height: 120px;
    }
    .contact-form button{
        width: 100%;
        padding: 12px;
        border: none;
        background: #6e8efb;
        color: white;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
    }
    .contact-form button:hover{
        background: #5b73ef;
    }
    .contact-info p{
        color: #555;
        margin-bottom: 15px;
        line-height: 1.6;
    }
    .contact-info strong{
        color: #333;
    }
    
    @media (max-width: 768px){
        .contact-wrapper{
            grid-template-columns: 1fr;
        }
    }
</style>

<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1 class="page-title">Contact</h1>
        
        <div class="contact-wrapper">
            <!-- Formulaire à gauche -->
            <div class="contact-section">
                <h2>Contactez-nous</h2>
                
                <form action="" class="contact-form">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
                    
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre email"required>
                    
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Votre message ..." required></textarea>
                    
                    <button type="submit">Envoyer</button>
                </form>
            </div>

            <!-- Coordonnées à droite -->
            <div class="contact-section contact-info">
                <h2>Nos coordonnées</h2>
                
                <p><strong>Email :</strong><br>bonaloue@gmail.com</p>
                
                <p><strong>Téléphone :</strong><br>+226 77 97 79 63</p>
                
                <p><strong>Adresse :</strong><br>Secteur 15, Ouagadougou<br>Burkina Faso</p>
                
                <p><strong>Horaires :</strong><br>Lundi - Samedi : 8h - 18h</p>
            </div>
        </div>
    </div>
</body>
</html>