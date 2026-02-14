<?php
include "header.php";
include "../db.php";

if(isset($_POST['action'])){
    if($_POST['action'] == 'ajouter'){
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $stock = $_POST['stock'];
        $id_categorie = $_POST['id_categorie'];
        $image = '';
        
        
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if(in_array($file_extension, $allowed_extensions)){
                $upload_dir = '../uploads/produits/';
                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777, true);
                }
                
                $image = uniqid() . '_' . basename($_FILES['image']['name']);
                $upload_path = $upload_dir . $image;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)){
                } else {
                    echo '<div class="message message-error"><ion-icon name="alert-circle-outline"></ion-icon>Erreur lors de l\'upload de l\'image</div>';
                    $image = '';
                }
            } else {
                echo '<div class="message message-error"><ion-icon name="alert-circle-outline"></ion-icon>Format d\'image non autorisé (jpg, jpeg, png, gif, webp uniquement)</div>';
            }
        }
        
        $sql = "INSERT INTO produit (nom, description, prix, stock, id_categorie, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiss", $nom, $description, $prix, $stock, $id_categorie, $image);
        if($stmt->execute()){ 
            echo '<div class="message message-success"><ion-icon name="checkmark-circle-outline"></ion-icon>Produit ajouté avec succès !</div>'; 
        }
    }
    elseif($_POST['action'] == 'supprimer'){
        $id = $_POST['id_produit'];
        
        $sql_img = "SELECT image FROM produit WHERE id_produit = ?";
        $stmt_img = $conn->prepare($sql_img);
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        $prod = $result_img->fetch_assoc();
        

        $sql = "DELETE FROM produit WHERE id_produit = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if($stmt->execute()){ 
            if(!empty($prod['image']) && file_exists('../uploads/produits/' . $prod['image'])){
                unlink('../uploads/produits/' . $prod['image']);
            }
            echo '<div class="message message-success"><ion-icon name="checkmark-circle-outline"></ion-icon>Produit supprimé !</div>'; 
        }
    }
}

$result_produits = $conn->query("SELECT p.*, c.nom as cat_nom FROM produit p LEFT JOIN categorie c ON p.id_categorie = c.id_categorie ORDER BY p.date_ajout DESC");
$result_cat = $conn->query("SELECT * FROM categorie ORDER BY nom");
?>

<style>
.image-preview{
    width: 100%;
    max-width: 200px;
    height: 150px;
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
    overflow: hidden;
    background: #f9f9f9;
}
.image-preview img{
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.image-preview-placeholder{
    color: #999;
    text-align: center;
    padding: 20px;
}
.product-image-cell{
    width: 80px;
    height: 60px;
}
.product-image-cell img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 5px;
}
.no-image{
    width: 100%;
    height: 100%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    color: #999;
    font-size: 24px;
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu">';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<div class="image-preview-placeholder" id="imagePlaceholder"><ion-icon name="image-outline" style="font-size:40px;"></ion-icon><br>Aperçu de l\'image</div>';
    }
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
                <div class="image-preview-placeholder" id="imagePlaceholder">
                    <ion-icon name="image-outline" style="font-size:40px;"></ion-icon><br>
                    Aperçu de l'image
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><ion-icon name="save-outline"></ion-icon> Ajouter le produit</button>
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result_produits->num_rows > 0): while($p = $result_produits->fetch_assoc()): ?>
            <tr>
                <td>
                    <div class="product-image-cell">
                        <?php if(!empty($p['image']) && file_exists('../uploads/produits/' . $p['image'])): ?>
                            <img src="../uploads/produits/<?php echo $p['image']; ?>" alt="<?php echo $p['nom']; ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <ion-icon name="image-outline"></ion-icon>
                            </div>
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
                    <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce produit ?');">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                        <button type="submit" class="btn btn-delete"><ion-icon name="trash-outline"></ion-icon></button>
                    </form>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="8" style="text-align:center;color:#666;">Aucun produit</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $conn->close(); include "admin_footer.php"; ?>