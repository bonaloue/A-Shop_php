<?php
session_start();
include "../db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit(); }

if(isset($_POST['valider'])){
    $id_paiement = $_POST['id_paiement'];
    $sql = "UPDATE paiement SET statut='valide' WHERE id_paiement=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paiement);
    if($stmt->execute()){ $message = "Paiement validé !"; }
}

$sql = "SELECT p.*, c.id_commande, c.total, u.nom FROM paiement p INNER JOIN commande c ON p.id_commande = c.id_commande INNER JOIN client cl ON c.id_client = cl.id_client INNER JOIN utilisateur u ON cl.id_user = u.id_user ORDER BY p.date_paiement DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Validation Paiements</title>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}.header{background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:white;padding:20px;}.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}.header h1{display:flex;align-items:center;gap:10px;}.btn-back{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;}.btn-back:hover{background:#f8f8f8;}.container{max-width:1400px;margin:30px auto;padding:0 20px;}.table-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}table{width:100%;border-collapse:collapse;}th{background:#ff6b6b;color:white;padding:15px;text-align:left;}td{padding:15px;border-bottom:1px solid #eee;}.btn-validate{background:#44ff44;color:#155724;padding:8px 15px;border:none;border-radius:5px;cursor:pointer;font-weight:bold;display:inline-flex;align-items:center;gap:5px;}.btn-validate:hover{background:#33dd33;}.status-badge{padding:5px 15px;border-radius:20px;font-size:12px;font-weight:bold;}.status-en_attente{background:#fff3cd;color:#856404;}.status-valide{background:#d4edda;color:#155724;}.message{background:#d4edda;color:#155724;padding:15px;border-radius:5px;margin-bottom:20px;text-align:center;display:flex;align-items:center;justify-content:center;gap:10px;}</style>
</head>
<body>
    <div class="header"><div class="header-content"><h1><ion-icon name="card-outline"></ion-icon> Validation des Paiements</h1><a href="dashboard.php" class="btn-back"><ion-icon name="arrow-back-outline"></ion-icon> Retour</a></div></div>
    <div class="container">
        <?php if(isset($message)): ?><div class="message"><ion-icon name="checkmark-circle-outline" style="font-size:24px;"></ion-icon><?php echo $message; ?></div><?php endif; ?>
        <div class="table-card">
            <table>
                <thead><tr><th>ID Paiement</th><th>Commande</th><th>Client</th><th>Montant</th><th>Mode</th><th>Date</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if($result->num_rows > 0): while($p = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $p['id_paiement']; ?></td>
                        <td>#<?php echo $p['id_commande']; ?></td>
                        <td><?php echo $p['nom']; ?></td>
                        <td style="font-weight:bold;"><?php echo number_format($p['montant'],0,',',' '); ?> FCFA</td>
                        <td><?php echo ucfirst(str_replace('_',' ',$p['mode_paiement'])); ?></td>
                        <td><?php echo date('d/m/Y H:i',strtotime($p['date_paiement'])); ?></td>
                        <td><span class="status-badge status-<?php echo $p['statut']; ?>"><?php echo ucfirst($p['statut']); ?></span></td>
                        <td>
                            <?php if($p['statut'] == 'en_attente'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id_paiement" value="<?php echo $p['id_paiement']; ?>">
                                <button type="submit" name="valider" class="btn-validate"><ion-icon name="checkmark-outline"></ion-icon> Valider</button>
                            </form>
                            <?php else: ?>
                            <span style="color:#155724;display:flex;align-items:center;gap:5px;"><ion-icon name="checkmark-done-outline"></ion-icon> Validé</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" style="text-align:center;color:#666;">Aucun paiement</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>