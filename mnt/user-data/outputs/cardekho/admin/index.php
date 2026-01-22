<?php
session_start();
require_once '../config/helpers.php';

$settings = getSettings();
$totalCars = count(getAllCars());
$totalBanners = count(getBanners());
$totalMenuItems = count(getMenuItems());

// Get recent cars
$db = getDB();
$recentCars = $db->query("SELECT * FROM cars ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CarDekho</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary: #1A1A2E;
            --sidebar-bg: #0F0F1A;
            --card-bg: #FFFFFF;
            --text-dark: #2D2D2D;
            --text-light: #6B7280;
            --bg-light: #F5F7FA;
            --border: #E5E7EB;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sidebar-logo i {
            color: var(--primary);
        }

        .sidebar-logo span {
            color: var(--primary);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-label {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            letter-spacing: 1px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,107,53,0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary);
        }

        .topbar-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255,107,53,0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border);
            color: var(--text-dark);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .stat-icon.cars { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
        .stat-icon.banners { background: linear-gradient(135deg, var(--info), #2563EB); }
        .stat-icon.menu { background: linear-gradient(135deg, var(--success), #059669); }
        .stat-icon.views { background: linear-gradient(135deg, var(--warning), #D97706); }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary);
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Content Cards */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .table tr:hover {
            background: var(--bg-light);
        }

        .table img {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
        }

        .badge {
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16,185,129,0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245,158,11,0.1);
            color: var(--warning);
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn.edit {
            background: rgba(59,130,246,0.1);
            color: var(--info);
        }

        .action-btn.delete {
            background: rgba(239,68,68,0.1);
            color: var(--danger);
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Quick Links */
        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s;
        }

        .quick-link:hover {
            background: var(--primary);
            color: #fff;
            transform: translateX(5px);
        }

        .quick-link i {
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .quick-link:hover i {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-car"></i>
                Car<span>Dekho</span>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="index.php" class="menu-item active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <div class="menu-label">Content Management</div>
            <a href="banners.php" class="menu-item">
                <i class="fas fa-images"></i> Banners
            </a>
            <a href="cars.php" class="menu-item">
                <i class="fas fa-car-side"></i> Cars
            </a>
            <a href="menu.php" class="menu-item">
                <i class="fas fa-bars"></i> Menu Items
            </a>
            
            <div class="menu-label">Settings</div>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Site Settings
            </a>
            <a href="customers.php" class="menu-item">
                <i class="fas fa-users"></i> Customers
            </a>
            
            <div class="menu-label">Actions</div>
            <a href="../index.php" class="menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <h1 class="page-title">Dashboard</h1>
            <div class="topbar-actions">
                <a href="../index.php" target="_blank" class="btn btn-outline">
                    <i class="fas fa-eye"></i> View Site
                </a>
                <a href="cars.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Car
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon cars">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalCars ?></h3>
                    <p>Total Cars</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon banners">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalBanners ?></h3>
                    <p>Active Banners</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon menu">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalMenuItems ?></h3>
                    <p>Menu Items</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon views">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-info">
                    <h3>1.2K</h3>
                    <p>Total Views</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-grid">
            <!-- Recent Cars -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Cars</h3>
                    <a href="cars.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View All</a>
                </div>
                <div class="card-body" style="padding: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCars as $car): ?>
                            <tr>
                                <td><img src="../<?= $car['image'] ?>" alt="<?= $car['name'] ?>" onerror="this.src='https://via.placeholder.com/60x45?text=Car'"></td>
                                <td><?= $car['name'] ?></td>
                                <td><?= $car['price'] ?></td>
                                <td>
                                    <span class="badge <?= $car['car_type'] == 'latest' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $car['car_type'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="cars.php?action=edit&id=<?= $car['id'] ?>" class="action-btn edit"><i class="fas fa-edit"></i></a>
                                    <a href="cars.php?action=delete&id=<?= $car['id'] ?>" class="action-btn delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentCars)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-light);">No cars found. Add your first car!</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-links">
                        <a href="cars.php?action=add" class="quick-link">
                            <i class="fas fa-plus"></i>
                            <span>Add New Car</span>
                        </a>
                        <a href="banners.php?action=add" class="quick-link">
                            <i class="fas fa-image"></i>
                            <span>Add New Banner</span>
                        </a>
                        <a href="menu.php?action=add" class="quick-link">
                            <i class="fas fa-link"></i>
                            <span>Add Menu Item</span>
                        </a>
                        <a href="settings.php" class="quick-link">
                            <i class="fas fa-cog"></i>
                            <span>Site Settings</span>
                        </a>
                        <a href="customers.php" class="quick-link">
                            <i class="fas fa-users"></i>
                            <span>View Customers</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
