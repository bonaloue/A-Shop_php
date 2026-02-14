<?php
session_start();
include "../db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit(); }

if(isset($_POST['action'])){
    if($_POST['action'] == 'ajouter'){
        $nom = $_POST['nom'];
        $sql = "INSERT INTO categorie (nom) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nom);
        if($stmt->execute()){ $message = "Catégorie ajoutée !"; }
    }
    elseif($_POST['action'] == 'supprimer'){
        $id = $_POST['id_categorie'];
        $sql = "DELETE FROM categorie WHERE id_categorie = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if($stmt->execute()){ $message = "Catégorie supprimée !"; }
    }
}

$sql_cat = "SELECT c.*, COUNT(p.id_produit) as nb_produits FROM categorie c LEFT JOIN produit p ON c.id_categorie = p.id_categorie GROUP BY c.id_categorie ORDER BY c.nom";
$result_cat = $conn->query($sql_cat);
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Gestion Catégories</title>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}.header{background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:white;padding:20px;}.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}.header h1{display:flex;align-items:center;gap:10px;}.btn-back{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;}.btn-back:hover{background:#f8f8f8;}.container{max-width:1000px;margin:30px auto;padding:0 20px;}.form-card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:30px;}.form-card h2{margin-bottom:20px;display:flex;align-items:center;gap:10px;}input{padding:10px;border:1px solid #ccc;border-radius:5px;width:100%;margin-bottom:15px;}.btn-primary{background:#6e8efb;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:bold;width:100%;display:inline-flex;align-items:center;justify-content:center;gap:8px;}.btn-primary:hover{background:#5b73ef;}.cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;}.cat-card{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}.cat-name{font-size:20px;font-weight:bold;color:#333;margin-bottom:10px;display:flex;align-items:center;gap:10px;}.cat-count{color:#666;margin-bottom:15px;display:flex;align-items:center;gap:5px;}.btn-delete{background:#ff4444;color:white;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;}.btn-delete:hover{background:#cc0000;}.message{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px;}</style>
</head>
<body>
    <div class="header"><div class="header-content"><h1><ion-icon name="pricetags-outline"></ion-icon> Gestion des Catégories</h1><a href="dashboard.php" class="btn-back"><ion-icon name="arrow-back-outline"></ion-icon> Retour</a></div></div>
    <div class="container">
        <?php if(isset($message)): ?><div class="message"><ion-icon name="checkmark-circle-outline" style="font-size:24px;"></ion-icon><?php echo $message; ?></div><?php endif; ?>
        <div class="form-card">
            <h2><ion-icon name="add-circle-outline"></ion-icon> Ajouter une catégorie</h2>
            <form method="post">
                <input type="hidden" name="action" value="ajouter">
                <input type="text" name="nom" placeholder="Nom de la catégorie" required>
                <button type="submit" class="btn-primary"><ion-icon name="save-outline"></ion-icon> Ajouter</button>
            </form>
        </div>
        <div class="cat-grid">
            <?php while($cat = $result_cat->fetch_assoc()): ?>
            <div class="cat-card">
                <div class="cat-name"><ion-icon name="pricetag-outline"></ion-icon> <?php echo $cat['nom']; ?></div>
                <div class="cat-count"><ion-icon name="cube-outline"></ion-icon> <?php echo $cat['nb_produits']; ?> produit(s)</div>
                <form method="post" onsubmit="return confirm('Supprimer cette catégorie ?');">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="id_categorie" value="<?php echo $cat['id_categorie']; ?>">
                    <button type="submit" class="btn-delete"><ion-icon name="trash-outline"></ion-icon> Supprimer</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>