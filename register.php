<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de contact</title>
</head>
<style>
    body{
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6e8efb,#a777e3);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin : 0;
    }
    .contact-form{
        background : #fff;
        padding: 30px 40px;
        border-radius:15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 450px;
    }
    .contact-form h2{
        text-align: center;
        margin-bottom: 25px;
        color:#333
    }
    .contact-form label{
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color:#555;
    }
    .contact-form input,
    .contact-form textarea{
        width: 100%;
        padding: 12px 15px;
        margin-bottom : 20px;
        border-radius:1px solid #ccc;
        outline:none;
        font-size:16px;
        transition:0.3s
    } 
    .contact-form input:focus,
    .contact-form textarea:focus{
        border-color: #6e8efb;
        box-shadow: 0 0 8px rgba(110,142,251,0.4);
    }
    .contact-form textarea{
        resize : vertical;
        min-height: 120px;
    }
    .contact-form button{
        width: 100%;
        padding: 12px;
        border:none;
        border-radius:8px;
        background: #6e8efb;
        color:#fff;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
    }
    .contact-form button:hover{
        background: #5b73ef;
    }
    .contact-form img{
        display: block;
        margin: 0 auto 20px;
        width: 150px;
        height: 150px;
    }
    .contact-form p{
        text-align: center;
        margin-top: 15px;
        color: #555;
    }
    .contact-form a{
        color: #6e8efb;
        text-decoration: none;
        font-weight: bold;
    }
    .contact-form a:hover{
        text-decoration: underline;
    }
</style>

<body>
    <form action="inscrire.php" class="contact-form" method="post">
        <img src="image/logo.png" alt="Logo de mon site">
        <h2>Formulaire de connexion</h2>
        <label for="">Votre nom</label>
        <input type="text" name="nom" placeholder="Votre nom" required>
        
        <label for="">Votre email</label>
        <input type="email" name="email" placeholder="Votre email" required>
        
        <label for="">Votre mot de passe</label>
        <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>

        <label for="">Votre numero de téléphone</label>
        <input type="text" name="telephone" placeholder="Votre numero de téléphone" required>

        <button type="submit">S'inscrire</button>
        <p>Vous aviez déjà un compte <a href="index.php">Se connecter</a></p>
    </form>
</body>
</html>