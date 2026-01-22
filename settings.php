<?php
session_start();
require_once '../config/helpers.php';

$db = getDB();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name']),
        'phone' => sanitize($_POST['phone']),
        'email' => sanitize($_POST['email']),
        'address' => sanitize($_POST['address']),
        'facebook' => sanitize($_POST['facebook']),
        'twitter' => sanitize($_POST['twitter']),
        'instagram' => sanitize($_POST['instagram']),
        'youtube' => sanitize($_POST['youtube']),
        'footer_text' => sanitize($_POST['footer_text'])
    ];

    // Handle logo upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === 0) {
        $upload = uploadImage($_FILES['site_logo'], 'logo');
        if ($upload['success']) {
            $settings['site_logo'] = $upload['path'];
        }
    }

    foreach ($settings as $key => $value) {
        updateSetting($key, $value);
    }

    $message = 'Settings updated successfully!';
    $messageType = 'success';
}

$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #FF6B35; --primary-dark: #E55A2B; --secondary: #1A1A2E; --sidebar-bg: #0F0F1A; --card-bg: #FFFFFF; --text-dark: #2D2D2D; --text-light: #6B7280; --bg-light: #F5F7FA; --border: #E5E7EB; --success: #10B981; --danger: #EF4444; --info: #3B82F6; }
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
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--secondary); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(255,107,53,0.4); }
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .card-title { font-size: 1.1rem; font-weight: 600; color: var(--secondary); display: flex; align-items: center; gap: 0.5rem; }
        .card-title i { color: var(--primary); }
        .card-body { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark); }
        .form-group small { color: var(--text-light); font-size: 0.85rem; }
        .form-control { width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--border); border-radius: 10px; font-size: 1rem; font-family: inherit; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: rgba(16,185,129,0.1); color: var(--success); }
        .input-icon { position: relative; }
        .input-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-light); }
        .input-icon input { padding-left: 2.75rem; }
        .logo-preview { width: 120px; height: 120px; border: 2px dashed var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-top: 0.5rem; overflow: hidden; background: var(--bg-light); }
        .logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .form-row { grid-template-columns: 1fr; } }
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
            <a href="banners.php" class="menu-item"><i class="fas fa-images"></i> Banners</a>
            <a href="cars.php" class="menu-item"><i class="fas fa-car-side"></i> Cars</a>
            <a href="menu.php" class="menu-item"><i class="fas fa-bars"></i> Menu Items</a>
            <div class="menu-label">Settings</div>
            <a href="settings.php" class="menu-item active"><i class="fas fa-cog"></i> Site Settings</a>
            <a href="customers.php" class="menu-item"><i class="fas fa-users"></i> Customers</a>
            <div class="menu-label">Actions</div>
            <a href="../index.php" class="menu-item" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <h1 class="page-title">Site Settings</h1>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>">
            <i class="fas fa-check-circle"></i> <?= $message ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- General Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cog"></i> General Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="<?= $settings['site_name'] ?? 'CarDekho' ?>">
                        </div>
                        <div class="form-group">
                            <label>Site Logo</label>
                            <input type="file" name="site_logo" class="form-control" accept="image/*" onchange="previewLogo(this)">
                            <div class="logo-preview" id="logoPreview">
                                <?php if (!empty($settings['site_logo'])): ?>
                                <img src="../<?= $settings['site_logo'] ?>" alt="Logo">
                                <?php else: ?>
                                <span style="color:var(--text-light);"><i class="fas fa-image"></i></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-phone"></i> Contact Information</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="text" name="phone" class="form-control" value="<?= $settings['phone'] ?? '' ?>" placeholder="1800-XXX-XXXX">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" class="form-control" value="<?= $settings['email'] ?? '' ?>" placeholder="contact@example.com">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" name="address" class="form-control" value="<?= $settings['address'] ?? '' ?>" placeholder="Your business address">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-share-alt"></i> Social Media Links</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Facebook URL</label>
                            <div class="input-icon">
                                <i class="fab fa-facebook-f"></i>
                                <input type="url" name="facebook" class="form-control" value="<?= $settings['facebook'] ?? '' ?>" placeholder="https://facebook.com/yourpage">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Twitter URL</label>
                            <div class="input-icon">
                                <i class="fab fa-twitter"></i>
                                <input type="url" name="twitter" class="form-control" value="<?= $settings['twitter'] ?? '' ?>" placeholder="https://twitter.com/yourhandle">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Instagram URL</label>
                            <div class="input-icon">
                                <i class="fab fa-instagram"></i>
                                <input type="url" name="instagram" class="form-control" value="<?= $settings['instagram'] ?? '' ?>" placeholder="https://instagram.com/yourhandle">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>YouTube URL</label>
                            <div class="input-icon">
                                <i class="fab fa-youtube"></i>
                                <input type="url" name="youtube" class="form-control" value="<?= $settings['youtube'] ?? '' ?>" placeholder="https://youtube.com/yourchannel">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-copyright"></i> Footer Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Footer Copyright Text</label>
                        <input type="text" name="footer_text" class="form-control" value="<?= $settings['footer_text'] ?? '' ?>" placeholder="© 2026 Your Company. All Rights Reserved.">
                        <small>This text appears at the bottom of the website</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>
    </main>

    <script>
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '">'; }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
