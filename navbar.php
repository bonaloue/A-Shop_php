<?php
// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['nom'] : '';
$user_role = $is_logged_in ? $_SESSION['role'] : '';
?>

<style>
    .navbar{
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        padding: 15px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .navbar-container{
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }
    .navbar-logo{
        color: white;
        font-size: 24px;
        font-weight: bold;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .navbar-logo img{
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .navbar-menu{
        display: flex;
        gap: 30px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .navbar-menu a{
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
        padding: 8px 15px;
        border-radius: 5px;
    }
    .navbar-menu a:hover{
        background: rgba(255,255,255,0.2);
    }
    .navbar-user{
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .user-info{
        color: white;
        font-size: 14px;
    }
    .btn-logout{
        background: #ff4444;
        color: white;
        padding: 8px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }
    .btn-logout:hover{
        background: #cc0000;
    }
    .btn-login{
        background: white;
        color: #6e8efb;
        padding: 8px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        transition: 0.3s;
    }
    .btn-login:hover{
        background: #f0f0f0;
    }
</style>

<nav class="navbar">
    <div class="navbar-container">
        <a href="accueil.php" class="navbar-logo">
            <img src="image/logo.png" alt="A-Shop">
            <span>A-Shop</span>
        </a>
        <?php
            $base = '/Projet_A-Shop'; // ou '' si vous êtes à la racine
        ?>
        <ul class="navbar-menu">
            <li><a href="<?= $base ?>/accueil.php">Accueil</a></li>
            <li><a href="<?= $base ?>/client/boutique.php">Boutique</a></li>
            <li><a href="<?= $base ?>/about.php">À propos</a></li>
            <li><a href="<?= $base ?>/contact.php">Contact</a></li>
        </ul>
        
        <div class="navbar-user">
            <?php if($is_logged_in): ?>
                <div class="user-info">
                    Bonjour, <strong><?php echo $user_name; ?></strong>
                    <?php if($user_role == 'admin'): ?>
                        <span style="background: #ff6b6b; padding: 3px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">ADMIN</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="btn-logout">Déconnexion</a>
            <?php else: ?>
                <a href="index.php" class="btn-login">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>