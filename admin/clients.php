<?php
session_start();
include "../db.php";
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit(); }

$sql = "SELECT u.*, c.adresse, c.ville, c.pays FROM utilisateur u LEFT JOIN client c ON u.id_user = c.id_user WHERE u.role='client' ORDER BY u.date_creation DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Liste Clients</title>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#f5f5f5;
}
.header{
    background:linear-gradient(135deg,#ff6b6b,#ee5a6f);
    color:white;
    padding:20px;
}
.header-content{
    max-width:1400px;
    margin:0 auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
}.header h1{display:flex;align-items:center;gap:10px;}.btn-back{background:white;color:#ff6b6b;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;display:inline-flex;align-items:center;gap:8px;}.btn-back:hover{background:#f8f8f8;}.container{max-width:1400px;margin:30px auto;padding:0 20px;}.table-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}table{width:100%;border-collapse:collapse;}th{background:#ff6b6b;color:white;padding:15px;text-align:left;}td{padding:15px;border-bottom:1px solid #eee;}
</style>
</head>
<body>
    <div class="header"><div class="header-content"><h1><ion-icon name="people-outline"></ion-icon> Liste des Clients</h1><a href="dashboard.php" class="btn-back"><ion-icon name="arrow-back-outline"></ion-icon> Retour</a></div></div>
    <div class="container">
        <div class="table-card">
            <table>
                <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Date inscription</th></tr></thead>
                <tbody>
                    <?php if($result->num_rows > 0): while($c = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $c['id_user']; ?></td>
                        <td><?php echo $c['nom']; ?></td>
                        <td><?php echo $c['email']; ?></td>
                        <td><?php echo $c['telephone']; ?></td>
                        <td><?php echo date('d/m/Y',strtotime($c['date_creation'])); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" style="text-align:center;color:#666;">Aucun client</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>