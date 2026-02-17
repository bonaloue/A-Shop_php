<?php
include "header.php";
include "../db.php";

if(isset($_POST['action']) && $_POST['action'] == 'ajouter'){
    $nom         = $_POST['nom'];
    $description = $_POST['description'];
    $prix        = $_POST['prix'];
    $stock       = $_POST['stock'];
    $id_categorie = $_POST['id_categorie'];
    $image = '';

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)){
            $upload_dir = '../image/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $image = uniqid() . '_' . basename($_FILES['image']['name']);
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)){
                echo '<div class="message message-error"><ion-icon name="alert-circle-outline"></ion-icon> Erreur upload image</div>';
                $image = '';
            }
        } else {
            echo '<div class="message message-error"><ion-icon name="alert-circle-outline"></ion-icon> Format non autorisé (jpg, jpeg, png, gif, webp)</div>';
        }
    }

    $sql = "INSERT INTO produit (nom, description, prix, stock, id_categorie, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $nom, $description, $prix, $stock, $id_categorie, $image);
    if($stmt->execute()){
        echo '<div class="message message-success"><ion-icon name="checkmark-circle-outline"></ion-icon> Produit ajouté avec succès !</div>';
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'modifier'){
    $id          = $_POST['id_produit'];
    $nom         = $_POST['nom'];
    $description = $_POST['description'];
    $prix        = $_POST['prix'];
    $stock       = $_POST['stock'];
    $id_categorie = $_POST['id_categorie'];
    $image_actuelle = $_POST['image_actuelle'];

    // Nouvelle image uploadée ?
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)){
            $upload_dir = '../image/';
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $nouvelle_image = uniqid() . '_' . basename($_FILES['image']['name']);
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $nouvelle_image)){
                // Supprimer l'ancienne image
                if(!empty($image_actuelle) && file_exists('../image/' . $image_actuelle)){
                    unlink('../image/' . $image_actuelle);
                }
                $image_actuelle = $nouvelle_image;
            }
        }
    }

    $sql = "UPDATE produit SET nom=?, description=?, prix=?, stock=?, id_categorie=?, image=? WHERE id_produit=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissi", $nom, $description, $prix, $stock, $id_categorie, $image_actuelle, $id);
    if($stmt->execute()){
        echo '<div class="message message-success"><ion-icon name="checkmark-circle-outline"></ion-icon> Produit modifié avec succès !</div>';
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'supprimer'){
    $id = $_POST['id_produit'];
    $stmt_img = $conn->prepare("SELECT image FROM produit WHERE id_produit = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $prod = $stmt_img->get_result()->fetch_assoc();

    $stmt = $conn->prepare("DELETE FROM produit WHERE id_produit = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        if(!empty($prod['image']) && file_exists('../image/' . $prod['image'])){
            unlink('../image/' . $prod['image']);
        }
        echo '<div class="message message-success"><ion-icon name="checkmark-circle-outline"></ion-icon> Produit supprimé !</div>';
    }
}

$result_produits = $conn->query("SELECT p.*, c.nom as cat_nom FROM produit p LEFT JOIN categorie c ON p.id_categorie = c.id_categorie ORDER BY p.date_ajout DESC");
$result_cat      = $conn->query("SELECT * FROM categorie ORDER BY nom");
$result_cat2     = $conn->query("SELECT * FROM categorie ORDER BY nom");
?>

<style>
.image-preview{
    width:100%;max-width:200px;height:150px;border:2px dashed #ccc;
    border-radius:8px;display:flex;align-items:center;justify-content:center;
    margin-top:10px;overflow:hidden;background:#f9f9f9;
}
.image-preview img{max-width:100%;max-height:100%;object-fit:contain;}
.image-preview-placeholder{color:#999;text-align:center;padding:20px;}
.product-image-cell{width:80px;height:60px;}
.product-image-cell img{width:100%;height:100%;object-fit:cover;border-radius:5px;}
.no-image{width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;
    justify-content:center;border-radius:5px;color:#999;font-size:24px;}
.btn-edit{
    background:#f0a500;color:white;padding:8px 12px;border:none;
    border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;
}
.btn-edit:hover{background:#d4920a;}
.action-btns{display:flex;gap:8px;align-items:center;}
.edit-row{display:none;background:#fff8e1;}
.edit-row td{padding:20px;}
.edit-form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:12px;}
.edit-form-group{display:flex;flex-direction:column;gap:5px;}
.edit-form-group label{font-weight:bold;color:#555;font-size:13px;}
.edit-form-group input,
.edit-form-group select,
.edit-form-group textarea{
    padding:9px;border:1px solid #ccc;border-radius:5px;font-size:13px;
}
.edit-form-group textarea{resize:vertical;min-height:70px;font-family:'Segoe UI',sans-serif;}
.edit-image-row{display:flex;align-items:flex-start;gap:20px;margin-bottom:12px;}
.edit-image-actuelle img{width:80px;height:60px;object-fit:cover;border-radius:5px;border:1px solid #ddd;}
.edit-image-actuelle p{font-size:12px;color:#666;margin-top:5px;text-align:center;}
.edit-actions{display:flex;gap:10px;margin-top:10px;}
.btn-save-edit{
    background:#6e8efb;color:white;padding:10px 20px;border:none;
    border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-weight:bold;
}
.btn-save-edit:hover{background:#5b73ef;}
.btn-cancel-edit{
    background:#aaa;color:white;padding:10px 20px;border:none;
    border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;
}
.btn-cancel-edit:hover{background:#888;}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => preview.innerHTML = '<img src="' + e.target.result + '">';
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<div class="image-preview-placeholder"><ion-icon name="image-outline" style="font-size:40px;"></ion-icon><br>Aperçu</div>';
    }
}

function previewEditImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => preview.innerHTML = '<img src="' + e.target.result + '">';
        reader.readAsDataURL(input.files[0]);
    }
}

function ouvrirEdition(id) {
    // Fermer tous les autres formulaires ouverts
    document.querySelectorAll('.edit-row').forEach(row => row.style.display = 'none');
    // Ouvrir celui cliqué
    const row = document.getElementById('edit-row-' + id);
    if(row.style.display === 'table-row'){
        row.style.display = 'none';
    } else {
        row.style.display = 'table-row';
    }
}

function fermerEdition(id) {
    document.getElementById('edit-row-' + id).style.display = 'none';
}
</script>

<div class="form-card">
    <h2><ion-icon name="add-circle-outline"></ion-icon> Ajouter un produit</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="ajouter">
        <div class="form-row">
            <div class="form-group">
                <label>Nom du produit</label>
                <input type="text" name="nom" required>
            </div>
            <div class="form-group">
                <label>Prix (FCFA)</label>
                <input type="number" name="prix" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" required>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="id_categorie" required>
                    <option value="">Choisir...</option>
                    <?php while($cat = $result_cat->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id_categorie']; ?>"><?php echo $cat['nom']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
            <label>Description</label>
            <textarea name="description" required></textarea>
        </div>
        <div class="form-group" style="margin-bottom:15px;">
            <label><ion-icon name="image-outline"></ion-icon> Image du produit (jpg, png, gif, webp)</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
            <div class="image-preview" id="imagePreview">
                <div class="image-preview-placeholder">
                    <ion-icon name="image-outline" style="font-size:40px;"></ion-icon><br>
                    Aperçu de l'image
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <ion-icon name="save-outline"></ion-icon> Ajouter le produit
        </button>
    </form>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Date ajout</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result_produits->num_rows > 0): while($p = $result_produits->fetch_assoc()): ?>

            <tr>
                <td>
                    <div class="product-image-cell">
                        <?php if(!empty($p['image']) && file_exists('../image/' . $p['image'])): ?>
                            <img src="../image/<?php echo $p['image']; ?>" alt="<?php echo $p['nom']; ?>">
                        <?php else: ?>
                            <div class="no-image"><ion-icon name="image-outline"></ion-icon></div>
                        <?php endif; ?>
                    </div>
                </td>
                <td>#<?php echo $p['id_produit']; ?></td>
                <td><?php echo $p['nom']; ?></td>
                <td><?php echo $p['cat_nom'] ?? 'Sans catégorie'; ?></td>
                <td><?php echo number_format($p['prix'],0,',',' '); ?> FCFA</td>
                <td><?php echo $p['stock']; ?></td>
                <td><?php echo date('d/m/Y',strtotime($p['date_ajout'])); ?></td>
                <td>
                    <div class="action-btns">
                        <button class="btn-edit" onclick="ouvrirEdition(<?php echo $p['id_produit']; ?>)">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <form method="post" onsubmit="return confirm('Supprimer ce produit ?');">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                            <button type="submit" class="btn btn-delete">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            <tr class="edit-row" id="edit-row-<?php echo $p['id_produit']; ?>">
                <td colspan="8">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                        <input type="hidden" name="image_actuelle" value="<?php echo $p['image']; ?>">

                        <div class="edit-form-grid">
                            <div class="edit-form-group">
                                <label>Nom du produit</label>
                                <input type="text" name="nom" value="<?php echo htmlspecialchars($p['nom']); ?>" required>
                            </div>
                            <div class="edit-form-group">
                                <label>Prix (FCFA)</label>
                                <input type="number" name="prix" step="0.01" value="<?php echo $p['prix']; ?>" required>
                            </div>
                            <div class="edit-form-group">
                                <label>Stock</label>
                                <input type="number" name="stock" value="<?php echo $p['stock']; ?>" required>
                            </div>
                            <div class="edit-form-group">
                                <label>Catégorie</label>
                                <select name="id_categorie" required>
                                    <option value="">Choisir...</option>
                                    <?php
                                    // Recharger les catégories pour ce produit
                                    $cats = $conn->query("SELECT * FROM categorie ORDER BY nom");
                                    while($cat = $cats->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $cat['id_categorie']; ?>"
                                        <?php echo ($cat['id_categorie'] == $p['id_categorie']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['nom']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="edit-form-group" style="margin-bottom:12px;">
                            <label>Description</label>
                            <textarea name="description" required><?php echo htmlspecialchars($p['description']); ?></textarea>
                        </div>

                        <div class="edit-image-row">
                            <!-- Image actuelle -->
                            <div class="edit-image-actuelle">
                                <div id="editPreview-<?php echo $p['id_produit']; ?>">
                                    <?php if(!empty($p['image']) && file_exists('../image/' . $p['image'])): ?>
                                        <img src="../image/<?php echo $p['image']; ?>" alt="Image actuelle">
                                    <?php else: ?>
                                        <div class="no-image" style="width:80px;height:60px;">
                                            <ion-icon name="image-outline"></ion-icon>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p>Image actuelle</p>
                            </div>
                            <!-- Nouvelle image -->
                            <div class="edit-form-group" style="flex:1;">
                                <label><ion-icon name="image-outline"></ion-icon> Changer l'image (optionnel)</label>
                                <input type="file" name="image" accept="image/*"
                                    onchange="previewEditImage(this, 'editPreview-<?php echo $p['id_produit']; ?>')">
                            </div>
                        </div>

                        <div class="edit-actions">
                            <button type="submit" class="btn-save-edit">
                                <ion-icon name="save-outline"></ion-icon> Enregistrer
                            </button>
                            <button type="button" class="btn-cancel-edit" onclick="fermerEdition(<?php echo $p['id_produit']; ?>)">
                                <ion-icon name="close-outline"></ion-icon> Annuler
                            </button>
                        </div>
                    </form>
                </td>
            </tr>

        <?php endwhile; else: ?>
            <tr><td colspan="8" style="text-align:center;color:#666;">Aucun produit</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $conn->close(); include "footer.php"; ?>