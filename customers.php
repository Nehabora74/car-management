<?php
session_start();
require_once '../config/helpers.php';

$db = getDB();

// Delete customer
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: customers.php?deleted=1");
    exit;
}

// Get all customers with their car preferences
$customers = $db->query("
    SELECT c.*, GROUP_CONCAT(cp.car_type SEPARATOR ', ') as car_types
    FROM customers c
    LEFT JOIN car_preferences cp ON c.id = cp.customer_id
    GROUP BY c.id
    ORDER BY c.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Admin</title>
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
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--secondary); }
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.1rem; font-weight: 600; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .table th { font-weight: 600; color: var(--text-light); font-size: 0.85rem; text-transform: uppercase; }
        .table tr:hover { background: var(--bg-light); }
        .badge { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; margin-right: 0.25rem; }
        .badge-hatchback { background: #DBEAFE; color: #1D4ED8; }
        .badge-sedan { background: #D1FAE5; color: #047857; }
        .badge-suv { background: #FEF3C7; color: #B45309; }
        .action-btn { width: 36px; height: 36px; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .action-btn.delete { background: rgba(239,68,68,0.1); color: var(--danger); }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-light); }
        .empty-state i { font-size: 4rem; margin-bottom: 1rem; color: var(--border); }
        .customer-info { display: flex; flex-direction: column; gap: 0.25rem; }
        .customer-info small { color: var(--text-light); font-size: 0.85rem; }
        .stat-badge { background: var(--primary); color: #fff; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .table { font-size: 0.85rem; } }
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
            <a href="menu.php" class="menu-item"><i class="fas fa-bars"></i> Menu Items</a>
            <div class="menu-label">Settings</div>
            <a href="settings.php" class="menu-item"><i class="fas fa-cog"></i> Site Settings</a>
            <a href="customers.php" class="menu-item active"><i class="fas fa-users"></i> Customers</a>
            <div class="menu-label">Actions</div>
            <a href="../index.php" class="menu-item" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <h1 class="page-title">Customer Inquiries</h1>
            <span class="stat-badge"><?= count($customers) ?> Total</span>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> Customer deleted successfully!</div>
        <?php endif; ?>

        <div class="card">
            <?php if (empty($customers)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No Inquiries Yet</h3>
                <p>Customer inquiries from the car preference form will appear here</p>
            </div>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Interested In</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>
                                <div class="customer-info">
                                    <strong><?= htmlspecialchars($customer['name']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <span><i class="fas fa-phone" style="color:var(--success);margin-right:5px;"></i><?= htmlspecialchars($customer['phone']) ?></span>
                                    <small><i class="fas fa-envelope" style="margin-right:5px;"></i><?= htmlspecialchars($customer['email']) ?></small>
                                </div>
                            </td>
                            <td><small><?= htmlspecialchars(substr($customer['address'], 0, 50)) ?>...</small></td>
                            <td>
                                <?php 
                                $types = explode(', ', $customer['car_types'] ?? '');
                                foreach ($types as $type):
                                    if ($type):
                                        $class = strtolower($type);
                                ?>
                                <span class="badge badge-<?= $class ?>"><?= $type ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </td>
                            <td><small><?= date('d M Y', strtotime($customer['created_at'])) ?></small></td>
                            <td>
                                <a href="?delete=<?= $customer['id'] ?>" class="action-btn delete" onclick="return confirm('Delete this customer?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
