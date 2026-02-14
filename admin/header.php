<?php
// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si admin connecté
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

// Déterminer la page actuelle pour le titre
$page_titles = [
    'dashboard.php' => 'Dashboard',
    'produits.php' => 'Gestion Produits',
    'categories.php' => 'Gestion Catégories',
    'commandes.php' => 'Gestion Commandes',
    'paiements.php' => 'Validation Paiements',
    'livraisons.php' => 'Gestion Livraisons',
    'clients.php' => 'Liste Clients'
];

$page_icons = [
    'dashboard.php' => 'shield-checkmark',
    'produits.php' => 'cube-outline',
    'categories.php' => 'pricetags-outline',
    'commandes.php' => 'cart-outline',
    'paiements.php' => 'card-outline',
    'livraisons.php' => 'rocket-outline',
    'clients.php' => 'people-outline'
];

$current_page = basename($_SERVER['PHP_SELF']);
$page_title = $page_titles[$current_page] ?? 'Administration';
$page_icon = $page_icons[$current_page] ?? 'settings-outline';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - A-Shop Admin</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="admin.css">    
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>
                <ion-icon name="<?php echo $page_icon; ?>"></ion-icon>
                <?php echo $page_title; ?>
            </h1>
            <div class="header-nav">
                <?php if($current_page != 'dashboard.php'): ?>
                    <a href="dashboard.php" class="btn btn-back">
                        <ion-icon name="arrow-back-outline"></ion-icon>
                        Dashboard
                    </a>
                <?php endif; ?>
                <span style="color:white;">Bonjour, <?php echo $_SESSION['nom']; ?></span>
                <a href="../logout.php" class="btn btn-logout">
                    <ion-icon name="log-out-outline"></ion-icon>
                    Déconnexion
                </a>
            </div>
        </div>
    </div>
    <div class="container">