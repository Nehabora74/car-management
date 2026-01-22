<?php
session_start();
require_once 'helpers.php';

$db = getDB();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $button_text = sanitize($_POST['button_text']);
    $button_url = sanitize($_POST['button_url']);
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImage($_FILES['image'], 'banners');
        if ($upload['success']) {
            if (!empty($_POST['existing_image'])) {
                deleteImage($_POST['existing_image']);
            }
            $image = $upload['path'];
        } else {
            $message = $upload['message'];
            $messageType = 'error';
        }
    }
    
    if (empty($message)) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $stmt = $db->prepare("UPDATE banners SET title=?, subtitle=?, image=?, button_text=?, button_url=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $subtitle, $image, $button_text, $button_url, $sort_order, $is_active, $_POST['id']]);
            $message = 'Banner updated successfully!';
        } else {
            $stmt = $db->prepare("INSERT INTO banners (title, subtitle, image, button_text, button_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $subtitle, $image, $button_text, $button_url, $sort_order, $is_active]);
            $message = 'Banner added successfully!';
        }
        $messageType = 'success';
        $action = 'list';
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("SELECT image FROM banners WHERE id = ?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch();
    if ($banner) {
        deleteImage($banner['image']);
        $stmt = $db->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Banner deleted successfully!';
        $messageType = 'success';
    }
    $action = 'list';
}

$editBanner = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM banners WHERE id = ?");
    $stmt->execute([$id]);
    $editBanner = $stmt->fetch();
}

$banners = $db->query("SELECT * FROM banners ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #1A1A2E; --sidebar-bg: #0F0F1A; --card-bg: #FFFFFF; --text-dark: #2D2D2D; --text-light: #6B7280; --bg-light: #F5F7FA; --border: #E5E7EB; --success: #10B981; --warning: #F59E0B; --danger: #EF4444; --info: #3B82F6; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-light); color: var(--text-dark); display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: var(--sidebar-bg); color: #fff; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo { display: flex; align-items: center; gap: 0.75rem; font-size: 1.5rem; font-weight: 700; }
        .sidebar-logo i { color: var(--primary); }
        .sidebar-logo span { color: var(--primary); }
        .sidebar-menu { padding: 1rem 0; }
        .menu-label { padding: 0.75rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.4); }
        .menu-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.9rem 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .menu-item:hover, .menu-item.active { background: rgba(255,107,53,0.1); color: var(--primary); border-left-color: var(--primary); }
        .menu-item i { width: 20px; text-align: center; }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--secondary); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255,107,53,0.4); }
        .btn-outline { background: transparent; border: 2px solid var(--border); color: var(--text-dark); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .card-title { font-size: 1.1rem; font-weight: 600; color: var(--secondary); }
        .card-body { padding: 1.5rem; }
        .banner-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .banner-card { background: var(--card-bg); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
        .banner-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .banner-image { height: 180px; overflow: hidden; position: relative; }
        .banner-image img { width: 100%; height: 100%; object-fit: cover; }
        .banner-status { position: absolute; top: 10px; right: 10px; }
        .banner-content { padding: 1.25rem; }
        .banner-content h4 { font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--secondary); }
        .banner-content p { color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem; }
        .banner-actions { display: flex; gap: 0.75rem; }
        .badge { padding: 0.3rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .badge-danger { background: rgba(239,68,68,0.1); color: var(--danger); }
        .action-btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.3rem; text-decoration: none; font-size: 0.85rem; }
        .action-btn.edit { background: rgba(59,130,246,0.1); color: var(--info); }
        .action-btn.delete { background: rgba(239,68,68,0.1); color: var(--danger); }
        .action-btn:hover { transform: scale(1.05); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--border); border-radius: 10px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .alert-error { background: rgba(239,68,68,0.1); color: var(--danger); }
        .image-preview { width: 100%; max-width: 400px; height: 200px; border: 2px dashed var(--border); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-top: 0.5rem; overflow: hidden; }
        .image-preview img { width: 100%; height: 100%; object-fit: cover; }
        .checkbox-label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .checkbox-label input { width: 18px; height: 18px; accent-color: var(--primary); }
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); }
        .empty-state i { font-size: 4rem; margin-bottom: 1rem; color: var(--border); }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .form-row { grid-template-columns: 1fr; } .banner-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo"><i class="fas fa-car"></i> Car<span>Dekho</span></div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="index.php" class="menu-item"><i class="fas fa-home"></i> Dashboard</a>
            <div class="menu-label">Content Management</div>
            <a href="banners.php" class="menu-item active"><i class="fas fa-images"></i> Banners</a>
            <a href="cars.php" class="menu-item"><i class="fas fa-car-side"></i> Cars</a>
            <a href="menu.php" class="menu-item"><i class="fas fa-bars"></i> Menu Items</a>
            <div class="menu-label">Settings</div>
            <a href="settings.php" class="menu-item"><i class="fas fa-cog"></i> Site Settings</a>
            <a href="customers.php" class="menu-item"><i class="fas fa-users"></i> Customers</a>
            <div class="menu-label">Actions</div>
            <a href="../index.php" class="menu-item" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
    </aside>

    <main class="main-content">
        <?php if ($action === 'list'): ?>
        <div class="topbar">
            <h1 class="page-title">Manage Banners</h1>
            <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Banner</a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>">
            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $message ?>
        </div>
        <?php endif; ?>

        <?php if (empty($banners)): ?>
        <div class="card">
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>No Banners Found</h3>
                <p>Add your first banner to display on the homepage</p>
                <a href="?action=add" class="btn btn-primary" style="margin-top: 1rem;"><i class="fas fa-plus"></i> Add Banner</a>
            </div>
        </div>
        <?php else: ?>
        <div class="banner-grid">
            <?php foreach ($banners as $banner): ?>
            <div class="banner-card">
                <div class="banner-image">
                    <img src="../<?= $banner['image'] ?>" alt="<?= $banner['title'] ?>" onerror="this.src='https://via.placeholder.com/400x200?text=Banner'">
                    <div class="banner-status">
                        <span class="badge <?= $banner['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                            <?= $banner['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
                <div class="banner-content">
                    <h4><?= $banner['title'] ?: 'Untitled Banner' ?></h4>
                    <p><?= $banner['subtitle'] ?: 'No subtitle' ?></p>
                    <div class="banner-actions">
                        <a href="?action=edit&id=<?= $banner['id'] ?>" class="action-btn edit"><i class="fas fa-edit"></i> Edit</a>
                        <a href="?action=delete&id=<?= $banner['id'] ?>" class="action-btn delete" onclick="return confirm('Delete this banner?')"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="topbar">
            <h1 class="page-title"><?= $action === 'edit' ? 'Edit Banner' : 'Add New Banner' ?></h1>
            <a href="banners.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editBanner): ?>
                    <input type="hidden" name="id" value="<?= $editBanner['id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= $editBanner['image'] ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Banner Title</label>
                            <input type="text" name="title" class="form-control" value="<?= $editBanner['title'] ?? '' ?>" placeholder="e.g., Find Your Dream Car">
                        </div>
                        <div class="form-group">
                            <label>Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" value="<?= $editBanner['subtitle'] ?? '' ?>" placeholder="e.g., Best deals on new cars">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="<?= $editBanner['button_text'] ?? '' ?>" placeholder="e.g., Explore Now">
                        </div>
                        <div class="form-group">
                            <label>Button URL</label>
                            <input type="text" name="button_url" class="form-control" value="<?= $editBanner['button_url'] ?? '' ?>" placeholder="e.g., #new-cars">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $editBanner['sort_order'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" value="1" <?= ($editBanner['is_active'] ?? 1) ? 'checked' : '' ?>>
                                Active (Show on website)
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Banner Image <?= $editBanner ? '' : '*' ?> (Recommended: 1920x600px)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" <?= $editBanner ? '' : 'required' ?> onchange="previewImage(this)">
                        <div class="image-preview" id="imagePreview">
                            <?php if ($editBanner && $editBanner['image']): ?>
                            <img src="../<?= $editBanner['image'] ?>" alt="Current">
                            <?php else: ?>
                            <span style="color:var(--text-light);"><i class="fas fa-image" style="font-size:2rem;"></i><br>Preview</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display:flex;gap:1rem;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $editBanner ? 'Update' : 'Add' ?> Banner</button>
                        <a href="banners.php" class="btn btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '">'; }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
