<?php
session_start();
include "../db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit(); }

if(isset($_POST['maj_statut'])){
    $id_livraison = $_POST['id_livraison'];
    $statut = $_POST['statut'];
    $sql = "UPDATE livraison SET statut=? WHERE id_livraison=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $statut, $id_livraison);
    if($stmt->execute()){ $message = "Statut mis à jour !"; }
}

$sql = "SELECT l.*, c.id_commande, c.total, u.nom, u.telephone FROM livraison l INNER JOIN commande c ON l.id_commande = c.id_commande INNER JOIN client cl ON c.id_client = cl.id_client INNER JOIN utilisateur u ON cl.id_user = u.id_user ORDER BY l.date_creation DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Gestion Livraisons</title>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}.header{background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:white;padding:20px;}.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}.header h1{display:flex;align-items:center;gap:10px;}.btn-back{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;}.btn-back:hover{background:#f8f8f8;}.container{max-width:1400px;margin:30px auto;padding:0 20px;}.table-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}table{width:100%;border-collapse:collapse;}th{background:#ff6b6b;color:white;padding:15px;text-align:left;}td{padding:15px;border-bottom:1px solid #eee;}select{padding:8px;border:1px solid #ccc;border-radius:5px;}.btn-update{background:#6e8efb;color:white;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;}.btn-update:hover{background:#5b73ef;}.status-badge{padding:5px 15px;border-radius:20px;font-size:12px;font-weight:bold;}.status-en_attente{background:#fff3cd;color:#856404;}.status-en_cours{background:#cfe2ff;color:#084298;}.status-livree{background:#d4edda;color:#155724;}.message{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px;}</style>
</head>
<body>
    <div class="header"><div class="header-content"><h1><ion-icon name="rocket-outline"></ion-icon> Gestion des Livraisons</h1><a href="dashboard.php" class="btn-back"><ion-icon name="arrow-back-outline"></ion-icon> Retour</a></div></div>
    <div class="container">
        <?php if(isset($message)): ?><div class="message"><ion-icon name="checkmark-circle-outline" style="font-size:24px;"></ion-icon><?php echo $message; ?></div><?php endif; ?>
        <div class="table-card">
            <table>
                <thead><tr><th>ID</th><th>Commande</th><th>Client</th><th>Tél</th><th>Adresse</th><th>Date création</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if($result->num_rows > 0): while($l = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $l['id_livraison']; ?></td>
                        <td>#<?php echo $l['id_commande']; ?></td>
                        <td><?php echo $l['nom']; ?></td>
                        <td><?php echo $l['telephone']; ?></td>
                        <td><?php echo substr($l['adresse_livraison'],0,50).'...'; ?></td>
                        <td><?php echo date('d/m/Y',strtotime($l['date_creation'])); ?></td>
                        <td><span class="status-badge status-<?php echo $l['statut']; ?>"><?php echo ucfirst($l['statut']); ?></span></td>
                        <td>
                            <form method="post" style="display:flex;gap:10px;">
                                <input type="hidden" name="id_livraison" value="<?php echo $l['id_livraison']; ?>">
                                <select name="statut">
                                    <option value="en_attente" <?php echo $l['statut']=='en_attente'?'selected':''; ?>>En attente</option>
                                    <option value="en_cours" <?php echo $l['statut']=='en_cours'?'selected':''; ?>>En cours</option>
                                    <option value="livree" <?php echo $l['statut']=='livree'?'selected':''; ?>>Livrée</option>
                                </select>
                                <button type="submit" name="maj_statut" class="btn-update"><ion-icon name="refresh-outline"></ion-icon> MAJ</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" style="text-align:center;color:#666;">Aucune livraison</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>