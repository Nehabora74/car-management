<?php
session_start();
require_once '../config/helpers.php';

$db = getDB();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $url = sanitize($_POST['url']);
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $stmt = $db->prepare("UPDATE menu_items SET title=?, url=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([$title, $url, $sort_order, $is_active, $_POST['id']]);
        $message = 'Menu item updated!';
    } else {
        $stmt = $db->prepare("INSERT INTO menu_items (title, url, sort_order, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $url, $sort_order, $is_active]);
        $message = 'Menu item added!';
    }
    $messageType = 'success';
    $action = 'list';
}

if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Menu item deleted!';
    $messageType = 'success';
    $action = 'list';
}

$editItem = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}

$menuItems = $db->query("SELECT * FROM menu_items ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #1A1A2E; --sidebar-bg: #0F0F1A; --card-bg: #FFFFFF; --text-dark: #2D2D2D; --text-light: #6B7280; --bg-light: #F5F7FA; --border: #E5E7EB; --success: #10B981; --danger: #EF4444; --info: #3B82F6; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-light); color: var(--text-dark); display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: var(--sidebar-bg); color: #fff; position: fixed; height: 100vh; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo { display: flex; align-items: center; gap: 0.75rem; font-size: 1.5rem; font-weight: 700; }
        .sidebar-logo i, .sidebar-logo span { color: var(--primary); }
        .sidebar-menu { padding: 1rem 0; }
        .menu-label { padding: 0.75rem 1.5rem; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.4); }
        .menu-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.9rem 1.5rem; color: rgba(255,255,255,0.7); text-decoration: none; border-left: 3px solid transparent; }
        .menu-item:hover, .menu-item.active { background: rgba(255,107,53,0.1); color: var(--primary); border-left-color: var(--primary); }
        .menu-item i { width: 20px; text-align: center; }
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--secondary); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .btn-outline { background: transparent; border: 2px solid var(--border); color: var(--text-dark); }
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .card-title { font-size: 1.1rem; font-weight: 600; }
        .card-body { padding: 1.5rem; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { font-weight: 600; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; }
        .badge { padding: 0.3rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .badge-danger { background: rgba(239,68,68,0.1); color: var(--danger); }
        .action-btn { width: 36px; height: 36px; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .action-btn.edit { background: rgba(59,130,246,0.1); color: var(--info); }
        .action-btn.delete { background: rgba(239,68,68,0.1); color: var(--danger); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-control { width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--border); border-radius: 10px; font-size: 1rem; font-family: inherit; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .checkbox-label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .checkbox-label input { width: 18px; height: 18px; accent-color: var(--primary); }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><div class="sidebar-logo"><i class="fas fa-car"></i> Car<span>Dekho</span></div></div>
        <nav class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="index.php" class="menu-item"><i class="fas fa-home"></i> Dashboard</a>
            <div class="menu-label">Content Management</div>
            <a href="banners.php" class="menu-item"><i class="fas fa-images"></i> Banners</a>
            <a href="cars.php" class="menu-item"><i class="fas fa-car-side"></i> Cars</a>
            <a href="menu.php" class="menu-item active"><i class="fas fa-bars"></i> Menu Items</a>
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
            <h1 class="page-title">Menu Items</h1>
            <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Menu Item</a>
        </div>
        <?php if ($message): ?><div class="alert alert-<?= $messageType ?>"><i class="fas fa-check-circle"></i> <?= $message ?></div><?php endif; ?>
        <div class="card">
            <div class="card-body" style="padding:0;">
                <table class="table">
                    <thead><tr><th>Title</th><th>URL</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($menuItems as $item): ?>
                        <tr>
                            <td><strong><?= $item['title'] ?></strong></td>
                            <td><code><?= $item['url'] ?></code></td>
                            <td><?= $item['sort_order'] ?></td>
                            <td><span class="badge <?= $item['is_active'] ? 'badge-success' : 'badge-danger' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                            <td>
                                <a href="?action=edit&id=<?= $item['id'] ?>" class="action-btn edit"><i class="fas fa-edit"></i></a>
                                <a href="?action=delete&id=<?= $item['id'] ?>" class="action-btn delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="topbar">
            <h1 class="page-title"><?= $editItem ? 'Edit' : 'Add' ?> Menu Item</h1>
            <a href="menu.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="card"><div class="card-body">
            <form method="POST">
                <?php if ($editItem): ?><input type="hidden" name="id" value="<?= $editItem['id'] ?>"><?php endif; ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Menu Title *</label>
                        <input type="text" name="title" class="form-control" required value="<?= $editItem['title'] ?? '' ?>" placeholder="e.g., New Cars">
                    </div>
                    <div class="form-group">
                        <label>URL *</label>
                        <input type="text" name="url" class="form-control" required value="<?= $editItem['url'] ?? '' ?>" placeholder="e.g., #new-cars or /page">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $editItem['sort_order'] ?? 0 ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div></div>
        <?php endif; ?>
    </main>
</body>
</html>
