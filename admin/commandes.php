<?php
session_start();
include "../db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit(); }

$sql = "SELECT c.*, u.nom, u.email, u.telephone FROM commande c INNER JOIN client cl ON c.id_client = cl.id_client INNER JOIN utilisateur u ON cl.id_user = u.id_user ORDER BY c.date_commande DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Gestion Commandes</title>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:'Segoe UI',sans-serif;background:#f5f5f5;}.header{background:linear-gradient(135deg,#ff6b6b,#ee5a6f);color:white;padding:20px;}.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;}.header h1{display:flex;align-items:center;gap:10px;}.btn-back{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;}.btn-back:hover{background:#f8f8f8;}.container{max-width:1400px;margin:30px auto;padding:0 20px;}.table-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}table{width:100%;border-collapse:collapse;}th{background:#ff6b6b;color:white;padding:15px;text-align:left;}td{padding:15px;border-bottom:1px solid #eee;}.status-badge{padding:5px 15px;border-radius:20px;font-size:12px;font-weight:bold;}.status-en_attente{background:#fff3cd;color:#856404;}.status-confirmee{background:#d4edda;color:#155724;}</style>
</head>
<body>
    <div class="header"><div class="header-content"><h1><ion-icon name="cart-outline"></ion-icon> Gestion des Commandes</h1><a href="dashboard.php" class="btn-back"><ion-icon name="arrow-back-outline"></ion-icon> Retour</a></div></div>
    <div class="container">
        <div class="table-card">
            <table>
                <thead><tr><th>ID</th><th>Client</th><th>Email</th><th>Téléphone</th><th>Date</th><th>Total</th><th>Statut</th></tr></thead>
                <tbody>
                    <?php if($result->num_rows > 0): while($c = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $c['id_commande']; ?></td>
                        <td><?php echo $c['nom']; ?></td>
                        <td><?php echo $c['email']; ?></td>
                        <td><?php echo $c['telephone']; ?></td>
                        <td><?php echo date('d/m/Y H:i',strtotime($c['date_commande'])); ?></td>
                        <td style="font-weight:bold;color:#ff6b6b;"><?php echo number_format($c['total'],0,',',' '); ?> FCFA</td>
                        <td><span class="status-badge status-<?php echo $c['statut']; ?>"><?php echo ucfirst($c['statut']); ?></span></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" style="text-align:center;color:#666;">Aucune commande</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>