<?php
session_start();
require_once 'helpers.php';

$settings = getSettings();
$menuItems = getMenuItems();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $carTypes = $_POST['carType'] ?? [];
    
    $errors = [];
    if (strlen($name) < 2) $errors[] = 'Name required';
    if (!preg_match('/^[6-9]\d{9}$/', $phone)) $errors[] = 'Valid phone required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (strlen($address) < 10) $errors[] = 'Address required';
    if (empty($carTypes)) $errors[] = 'Select car type';
    
    if (empty($errors)) {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $address]);
        $customerId = $db->lastInsertId();
        
        $stmt = $db->prepare("INSERT INTO car_preferences (customer_id, car_type) VALUES (?, ?)");
        foreach ($carTypes as $type) { $stmt->execute([$customerId, $type]); }
        $db->commit();
        $message = 'Thank you! Your preferences have been submitted.';
        $messageType = 'success';
    } else {
        $message = implode(', ', $errors);
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Car - <?= $settings['site_name'] ?? 'CarDekho' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--primary:#FF6B35;--primary-dark:#E55A2B;--secondary:#1A1A2E;--text-dark:#2D2D2D;--text-light:#6B7280;--bg-light:#F5F7FA;--white:#FFFFFF;--border:#E5E7EB;--success:#10B981;--error:#EF4444}
        body{font-family:'Poppins',sans-serif;background:var(--bg-light);color:var(--text-dark);line-height:1.6}
        a{text-decoration:none;color:inherit}
        .container{max-width:1280px;margin:0 auto;padding:0 1.5rem}
             .header{background:var(--white);box-shadow:0 2px 15px rgba(0,0,0,.08);position:sticky;top:0;z-index:1000}
        .header-top{background:var(--secondary);padding:.5rem 0}
        .header-top-content{display:flex;justify-content:space-between;align-items:center;font-size:.85rem;color:rgba(255,255,255,.8)}
        .header-top a{color:rgba(255,255,255,.8);transition:color .3s}
        .header-top a:hover{color:var(--primary)}
        .header-top-left{display:flex;gap:1.5rem}
        .header-top-left i{margin-right:.5rem;color:var(--primary)}
        .social-links{display:flex;gap:1rem}
        .header-main{padding:1rem 0}
        .header-main-content{display:flex;justify-content:space-between;align-items:center}
        .logo{display:flex;align-items:center;gap:.5rem;font-size:1.8rem;font-weight:800;color:var(--secondary)}
        .logo-icon{width:45px;height:45px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--white)}
        .logo span{color:var(--primary)}
        .nav-menu{display:flex;gap:2rem;list-style:none}
        .nav-menu a{font-weight:500;padding:.5rem 0;position:relative;transition:color .3s}
        .nav-menu a::after{content:'';position:absolute;bottom:0;left:0;width:0;height:2px;background:var(--primary);transition:width .3s}
        .nav-menu a:hover{color:var(--primary)}
        .nav-menu a:hover::after{width:100%}
        .btn{padding:.75rem 1.5rem;border-radius:8px;font-weight:600;font-size:.95rem;cursor:pointer;transition:all .3s;border:none;display:inline-flex;align-items:center;gap:.5rem;text-decoration:none}
        .btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:var(--white)}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 5px 20px rgba(255,107,53,.4)}
        .btn-outline{background:transparent;border:2px solid var(--primary);color:var(--primary)}
        .btn-outline:hover{background:var(--primary);color:var(--white)}

        
         .hero{background:linear-gradient(135deg,var(--secondary),#16213E);padding:4rem 1.5rem;text-align:center}
        .hero h1{color:var(--white);font-size:2.5rem;margin-bottom:.5rem}
        .hero h1 span{color:var(--primary)}
        .hero p{color:rgba(255,255,255,.7);font-size:1.1rem}
        .form-section{padding:3rem 0}
        .form-grid{display:grid;grid-template-columns:1fr 380px;gap:3rem}
        .form-container{background:var(--white);border-radius:20px;padding:2.5rem;box-shadow:0 4px 20px rgba(0,0,0,.08)}
        .form-header h2{font-size:1.5rem;color:var(--secondary);margin-bottom:.5rem}
        .form-header p{color:var(--text-light);margin-bottom:1.5rem}
        .form-group{margin-bottom:1.5rem}
        .form-group label{display:block;font-weight:500;margin-bottom:.5rem}
        .required{color:var(--primary)}
        .form-input{width:100%;padding:.9rem 1.2rem;border:2px solid var(--border);border-radius:12px;font-size:1rem;font-family:inherit;transition:border-color .3s}
        .form-input:focus{outline:none;border-color:var(--primary)}
        textarea.form-input{min-height:100px;resize:vertical}
        .car-types-section h3{font-size:1.1rem;color:var(--secondary);margin-bottom:1rem}
        .car-types-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
        .car-type-card{position:relative}
        .car-type-card input{position:absolute;opacity:0}
        .car-type-label{display:flex;flex-direction:column;align-items:center;padding:1.5rem 1rem;border:2px solid var(--border);border-radius:16px;cursor:pointer;transition:all .3s}
        .car-type-label:hover{border-color:var(--primary)}
        .car-type-card input:checked+.car-type-label{border-color:var(--primary);background:rgba(255,107,53,.05)}
        .car-type-icon{font-size:2.5rem;color:var(--text-light);margin-bottom:.5rem}
        .car-type-card input:checked+.car-type-label .car-type-icon{color:var(--primary)}
        .car-type-name{font-weight:600}
        .submitbtn{padding:1rem 2rem;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:var(--white);border:none;border-radius:12px;font-size:1.1rem;font-weight:600;cursor:pointer;margin-top:1.5rem;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:.5rem}
        .submitbtn:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(255,107,53,.35)}
        .sidebar-card{background:var(--white);border-radius:20px;padding:2rem;box-shadow:0 4px 20px rgba(0,0,0,.08);margin-bottom:1.5rem}
        .sidebar-card h3{font-size:1.2rem;color:var(--secondary);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem}
        .sidebar-card h3 i{color:var(--primary)}
        .features-list{list-style:none}
        .features-list li{display:flex;align-items:flex-start;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--border)}
        .features-list li:last-child{border-bottom:none}
        .features-list i{color:var(--success);margin-top:3px}
        .stats-card{background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:20px;padding:2rem;color:var(--white)}
        .stats-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1.5rem;text-align:center}
        .stat-number{font-size:1.8rem;font-weight:700;display:block}
        .stat-label{font-size:.85rem;opacity:.9}
        .alert{padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}
        .alert-success{background:rgba(16,185,129,.1);color:var(--success)}
        .alert-error{background:rgba(239,68,68,.1);color:var(--error)}
        @media(max-width:992px){.form-grid{grid-template-columns:1fr}}
        @media(max-width:768px){.nav-menu{display:none}.car-types-grid{grid-template-columns:1fr}.hero h1{font-size:1.8rem}.form-container{padding:1.5rem}}
        
       /* Footer */
        .footer{background:var(--secondary);color:rgba(255,255,255,.8);padding:4rem 0 0}
        .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:3rem;margin-bottom:3rem}
        .footer-brand .logo{color:var(--white);margin-bottom:1rem}
        .footer-brand p{font-size:.95rem;line-height:1.8;margin-bottom:1.5rem}
        .footer-social{display:flex;gap:1rem}
        .footer-social a{width:40px;height:40px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all .3s}
        .footer-social a:hover{background:var(--primary);transform:translateY(-3px)}
        .footer-title{color:var(--white);font-size:1.1rem;font-weight:600;margin-bottom:1.5rem}
        .footer-links{list-style:none}
        .footer-links li{margin-bottom:.75rem}
        .footer-links a{transition:all .3s;display:flex;align-items:center;gap:.5rem}
        .footer-links a:hover{color:var(--primary);padding-left:5px}
        .footer-contact li{display:flex;gap:1rem;margin-bottom:1rem}
        .footer-contact i{color:var(--primary);width:20px}
        .footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding:1.5rem 0;text-align:center;font-size:.9rem}

        /* Responsive */
        @media(max-width:1024px){.cars-grid{grid-template-columns:repeat(3,1fr)}.footer-grid{grid-template-columns:repeat(2,1fr)}.search-form{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:768px){.header-top{display:none}.nav-menu{display:none}.cars-grid{grid-template-columns:repeat(2,1fr)}.search-form{grid-template-columns:1fr}.banner-content h1{font-size:2rem}.footer-grid{grid-template-columns:1fr;text-align:center}.footer-social{justify-content:center}}
    </style>
</head>
<body>
   <header class="header">
        <div class="header-top">
            <div class="container header-top-content">
                <div class="header-top-left">
                    <a href="tel:7302765192"><i class="fas fa-phone"></i>7302765192</a>
                    <a href="mailto:neha@cardekho.com"><i class="fas fa-envelope"></i>neha@cardekho.com</a>
                </div>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="container header-main-content">
                <a href="#" class="logo">
                    <div class="logo-icon"><i class="fas fa-car"></i></div>
                    
                </a>
                <nav>
                    <ul class="nav-menu">
                        <li><a href="#">New Cars</a></li>
                        <li><a href="#">Electric Cars</a></li>
                        <li><a href="#">Compare</a></li>
                        <li><a href="#">News</a></li>
                        <li><a href="#">Reviews</a></li>
                    </ul>
                </nav>
                <a href="#" class="btn btn-primary"><i class="fas fa-car"></i> Find Car</a>
            </div>
        </div>
    </header>
    <section class="hero"><div class="container"><h1>Find Your <span>Dream Car</span></h1><p>Tell us your preferences and we'll help you find the perfect vehicle</p></div></section>
    <section class="form-section"><div class="container"><div class="form-grid">
        <div class="form-container">
            <div class="form-header"><h2>Share Your Preferences</h2><p>Fill in your details and select your preferred car types</p></div>
            <?php if($message):?><div class="alert alert-<?=$messageType?>"><i class="fas fa-<?=$messageType==='success'?'check-circle':'exclamation-circle'?>"></i><?=$message?></div><?php endif;?>
            <form method="POST">
                <div class="form-group"><label>Full Name <span class="required">*</span></label><input type="text" name="name" class="form-input" placeholder="Enter your full name" required></div>
                <div class="form-group"><label>Phone Number <span class="required">*</span></label><input type="tel" name="phone" class="form-input" placeholder="10-digit phone number" required></div>
                <div class="form-group"><label>Email Address <span class="required">*</span></label><input type="email" name="email" class="form-input" placeholder="Enter your email" required></div>
                <div class="form-group"><label>Address <span class="required">*</span></label><textarea name="address" class="form-input" placeholder="Enter complete address" required></textarea></div>
                <div class="car-types-section">
                    <h3>Select Car Type(s) <span class="required">*</span></h3>
                    <div class="car-types-grid">
                        <div class="car-type-card"><input type="checkbox" id="hatchback" name="carType[]" value="Hatchback"><label for="hatchback" class="car-type-label"><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Maruti/Swift/9226/1755777061785/front-left-side-47.jpg?imwidth=420&amp;impolicy=resize" style="width:35px;"><span class="car-type-name">Hatchback</span></label></div>
                        <div class="car-type-card"><input type="checkbox" id="sedan" name="carType[]" value="Sedan"><label for="sedan" class="car-type-label"><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Hyundai/Aura/10125/1762429751468/front-left-side-47.jpg?imwidth=420&amp;impolicy=resize" style="width:35px;"><span class="car-type-name">Sedan</span></label></div>
                        <div class="car-type-card"><input type="checkbox" id="suv" name="carType[]" value="SUV"><label for="suv" class="car-type-label"><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Tata/Sierra/12271/1765181428462/front-left-side-47.jpg?imwidth=420&amp;impolicy=resize" style="width:35px;"><span class="car-type-name">SUV</span></label></div>
                    </div>
                </div>
                <button type="submit" class="submitbtn"><i class="fas fa-paper-plane"></i> Submit </button>
            </form>
        </div>
        <aside>
            <div class="sidebar-card"><h3><i class="fas fa-check-circle"></i> Why Choose Us?</h3><ul class="features-list"><li><i class="fas fa-check"></i><span>Wide range of vehicles from trusted dealers</span></li><li><i class="fas fa-check"></i><span>Best price guarantee with transparent pricing</span></li><li><i class="fas fa-check"></i><span>Expert assistance for car buying journey</span></li><li><i class="fas fa-check"></i><span>Easy financing options available</span></li></ul></div>
            <div class="stats-card"><div class="stats-grid"><div><span class="stat-number">50+</span><span class="stat-label">Car Models</span></div><div><span class="stat-number">20K+</span><span class="stat-label">Happy Customers</span></div><div><span class="stat-number">10+</span><span class="stat-label">Dealers</span></div><div><span class="stat-number">24/7</span><span class="stat-label">Support</span></div></div></div>
        </aside>
    </div></div></section>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <div class="logo-icon"><i class="fas fa-car"></i></div>
                        
                    </a>
                    <p>India's #1 auto portal. Find new cars, electric vehicles, compare prices and get the best deals on your dream car.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#">New Cars</a></li>
                        <li><a href="#">Electric Cars</a></li>
                        <li><a href="#">Compare Cars</a></li>
                        <li><a href="#">Car News</a></li>
                        <li><a href="#">Car Reviews</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Popular Brands</h4>
                    <ul class="footer-links">
                        <li><a href="#">Maruti Suzuki</a></li>
                        <li><a href="#">Hyundai</a></li>
                        <li><a href="#">Tata Motors</a></li>
                        <li><a href="#">Mahindra</a></li>
                        <li><a href="#">Kia</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">Contact Us</h4>
                    <ul class="footer-links footer-contact">
                        <li><i class="fas fa-phone"></i><span>7302765192</span></li>
                        <li><i class="fas fa-envelope"></i><span>neha@cardekho.com</span></li>
                        <li><i class="fas fa-map-marker-alt"></i><span>Dwarka Delhi, India</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">© 2026 CarDekho. All Rights Reserved</div>
        </div>
    </footer>
</body>
</html>
