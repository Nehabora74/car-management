<?php
session_start();
require_once 'helpers.php';

$settings = getSettings();
$menuItems = getMenuItems();
$banners = getBanners();
$mostSearchedCars = getCarsByType('most_searched');
$latestCars = getCarsByType('latest');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarDekho - New Cars, Car Prices in India</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--primary:#FF6B35;--primary-dark:#E55A2B;--secondary:#1A1A2E;--text-dark:#2D2D2D;--text-light:#6B7280;--bg-light:#F5F7FA;--white:#FFFFFF;--border:#E5E7EB;--success:#10B981;--electric:#3B82F6}
        body{font-family:'Poppins',sans-serif;background:var(--bg-light);color:var(--text-dark);line-height:1.6}
        a{text-decoration:none;color:inherit}
        .container{max-width:1280px;margin:0 auto;padding:0 1.5rem}

        /* Header */
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

        /* Banner */
        .banner-section{position:relative;overflow:hidden}
        .banner-slide{position:relative;min-height:500px;background:linear-gradient(135deg,rgba(26,26,46,.9),rgba(26,26,46,.5)),url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1920');background-size:cover;background-position:center}
        .banner-content{position:relative;z-index:2;max-width:1280px;margin:0 auto;padding:6rem 1.5rem;color:var(--white)}
        .banner-content h1{font-size:3rem;font-weight:800;margin-bottom:1rem;line-height:1.2}
        .banner-content h1 span{color:var(--primary)}
        .banner-content p{font-size:1.2rem;opacity:.9;margin-bottom:2rem;max-width:500px}
        .banner-nav{position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;gap:.75rem;z-index:10}
        .banner-dot{width:12px;height:12px;border-radius:50%;background:rgba(255,255,255,.5);cursor:pointer;transition:all .3s}
        .banner-dot.active{background:var(--primary);transform:scale(1.2)}

        /* Search Box */
        .search-box{background:var(--white);border-radius:16px;padding:2rem;margin-top:-4rem;position:relative;z-index:20;box-shadow:0 10px 40px rgba(0,0,0,.15)}
        .search-tabs{display:flex;gap:1rem;margin-bottom:1.5rem}
        .search-tab{padding:.75rem 1.5rem;background:var(--bg-light);border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all .3s;font-family:inherit;display:flex;align-items:center;gap:.5rem}
        .search-tab.active{background:var(--primary);color:var(--white)}
        .search-tab.electric.active{background:var(--primary)}
        .search-tab i{font-size:1rem}
        .search-form{display:grid;grid-template-columns:repeat(4,1fr) auto;gap:1rem;align-items:end}
        .form-group label{display:block;font-weight:500;margin-bottom:.5rem;font-size:.9rem;color:var(--text-light)}
        .form-group select{width:100%;padding:.9rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:1rem;font-family:inherit;cursor:pointer}
        .form-group select:focus{outline:none;border-color:var(--primary)}
        .search-btn{padding:.9rem 2rem;height:fit-content}

        /* Tab Content */
        .tab-content{display:none}
        .tab-content.active{display:block}

        /* Section */
        .section{padding:4rem 0}
        .section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem}
        .section-title{font-size:1.8rem;font-weight:700;color:var(--secondary)}
        .section-title span{color:var(--primary)}
        .section-title .electric-text{color:var(--electric)}
        .view-all{color:var(--primary);font-weight:600;display:flex;align-items:center;gap:.5rem;transition:gap .3s}
        .view-all:hover{gap:.75rem}

        /* Cars Grid */
        .cars-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem}
        .car-card{background:var(--white);border-radius:16px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.05);transition:all .3s}
        .car-card:hover{transform:translateY(-8px);box-shadow:0 15px 40px rgba(0,0,0,.12)}
        .car-image{position:relative;height:180px;overflow:hidden}
        .car-image img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
        .car-card:hover .car-image img{transform:scale(1.1)}
        .car-badge{position:absolute;top:1rem;left:1rem;background:var(--primary);color:var(--white);padding:.3rem .75rem;border-radius:20px;font-size:.75rem;font-weight:600}
        .car-badge.new{background:var(--primary)}
        .car-badge.electric{background:var(--electric)}
        .car-wishlist{position:absolute;top:1rem;right:1rem;width:36px;height:36px;background:var(--white);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .3s;box-shadow:0 2px 8px rgba(0,0,0,.1)}
        .car-wishlist:hover{background:var(--primary);color:var(--white)}
        .car-details{padding:1.25rem}
        .car-name{font-size:1.1rem;font-weight:600;color:var(--secondary);margin-bottom:.5rem}
        .car-price{color:var(--primary);font-weight:700;font-size:1rem;margin-bottom:.5rem}
        .car-specs{display:flex;gap:1rem;margin-bottom:1rem;font-size:.8rem;color:var(--text-light)}
        .car-specs span{display:flex;align-items:center;gap:.25rem}
        .car-actions{display:flex;gap:.75rem}
        .car-actions .btn{flex:1;padding:.6rem;font-size:.85rem;justify-content:center}

        /* Electric Section Special */
        .electric-section .section-title span{color:var(--primary)}
        .electric-section .car-badge{background:var(--primary)}
        .electric-section .car-price{color:var(--primary)}
        .electric-section .btn-primary{background:var(--primary)}
        .electric-section .view-all{color:var(--primary)}

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
        @media(max-width:480px){.cars-grid{grid-template-columns:1fr}.search-tabs{flex-wrap:wrap}}
    </style>
</head>
<body>
    <!-- Header -->
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

    <!-- Banner -->
    <section class="banner-section">
        <div class="banner-slide">
            <div class="banner-content">
                <h1>Find Your <span>Right Car</span></h1>
                <p>Explore latest cars, electric vehicles and get the best deals on your dream car</p>
                <a href="#" class="btn btn-primary"><i class="fas fa-search"></i> Start Searching</a>
            </div>
        </div>
        <div class="banner-nav">
            <div class="banner-dot active"></div>
            <div class="banner-dot"></div>
            <div class="banner-dot"></div>
        </div>

        <!-- Search Box -->
        <div class="container">
            <div class="search-box">
                <div class="search-tabs">
                    <button class="search-tab active" data-tab="new-cars">
                        <i class="fas fa-car"></i> New Cars
                    </button>
                    <button class="search-tab electric" data-tab="electric-cars">
                        <i class="fas fa-bolt"></i> Electric Cars
                    </button>
                </div>
                
                <!-- New Cars Search Form -->
                <div id="new-cars" class="tab-content active">
                    <form class="search-form">
                        <div class="form-group">
                            <label>Select Brand</label>
                            <select>
                                <option>All Brands</option>
                                <option>Maruti Suzuki</option>
                                <option>Hyundai</option>
                                <option>Tata</option>
                                <option>Mahindra</option>
                                <option>Kia</option>
                                <option>Toyota</option>
                                <option>Honda</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Model</label>
                            <select><option>All Models</option></select>
                        </div>
                        <div class="form-group">
                            <label>Budget</label>
                            <select>
                                <option>Select Budget</option>
                                <option>Under ₹5 Lakh</option>
                                <option>₹5 - 10 Lakh</option>
                                <option>₹10 - 15 Lakh</option>
                                <option>₹15 - 25 Lakh</option>
                                <option>Above ₹25 Lakh</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fuel Type</label>
                            <select>
                                <option>All Fuel Types</option>
                                <option>Petrol</option>
                                <option>Diesel</option>
                                <option>CNG</option>
                                <option>Hybrid</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"><i class="fas fa-search"></i> Search</button>
                    </form>
                </div>

                <!-- Electric Cars Search Form -->
                <div id="electric-cars" class="tab-content">
                    <form class="search-form">
                        <div class="form-group">
                            <label>Select Brand</label>
                            <select>
                                <option>All EV Brands</option>
                                <option>Tata</option>
                                <option>Mahindra</option>
                                <option>MG</option>
                                <option>Hyundai</option>
                                <option>Kia</option>
                                <option>BYD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Range (km)</label>
                            <select>
                                <option>All Ranges</option>
                                <option>Under 200 km</option>
                                <option>200 - 400 km</option>
                                <option>400 - 500 km</option>
                                <option>Above 500 km</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Budget</label>
                            <select>
                                <option>Select Budget</option>
                                <option>Under ₹10 Lakh</option>
                                <option>₹10 - 20 Lakh</option>
                                <option>₹20 - 30 Lakh</option>
                                <option>Above ₹30 Lakh</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Body Type</label>
                            <select>
                                <option>All Types</option>
                                <option>Hatchback</option>
                                <option>SUV</option>
                                <option>Sedan</option>
                                <option>Coupe</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary search-btn" style="background:var(--primary)"><i class="fas fa-bolt"></i> Search EVs</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Cars Section (New Cars) -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Latest <span>Cars</span></h2>
                <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="cars-grid">
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/MG/Hector/13125/1768813549348/front-left-side-47.jpg?tr=w-664" alt="MG Hector">
                        <span class="car-badge new">New Launch</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">MG Hector 2025</h3>
                        <p class="car-price">₹11.99 - 18.99 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-gas-pump"></i> Petrol/Diesel</span>
                            <span><i class="fas fa-cog"></i> MT/CVT</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Tata/Sierra/12271/1765181428462/front-left-side-47.jpg?tr=w-664" alt="Tata Sierra">
                        <span class="car-badge new">New Launch</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Tata Sierra</h3>
                        <p class="car-price">₹11.49 - 21.29 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-gas-pump"></i> Petrol/Diesel</span>
                            <span><i class="fas fa-cog"></i> MT/AT</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/174323/seltos-exterior-right-front-three-quarter.jpeg?isig=0&q=80" alt="Kia Seltos 2026">
                        <span class="car-badge new">Facelift</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Kia Seltos 2026</h3>
                        <p class="car-price">₹10.79 - 19.81 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-gas-pump"></i> Petrol/Diesel</span>
                            <span><i class="fas fa-cog"></i> MT/AT/iMT</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Kia/Seltos-2026/12770/1766479168733/front-left-side-47.jpg?tr=w-664" alt="Maruti Dzire">
                        <span class="car-badge new">New Gen</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Maruti Dzire 2024</h3>
                        <p class="car-price">₹6.26 - 9.31 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-gas-pump"></i> Petrol/CNG</span>
                            <span><i class="fas fa-cog"></i> MT/AMT</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Electric Cars Section -->
    <section class="section electric-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-bolt" style="color:var(--primary);margin-right:.5rem;"></i>Electric <span>Cars</span></h2>
                <a href="#" class="view-all">View All EVs <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="cars-grid">
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Mahindra/BE-6/9263/1762423834412/front-left-side-47.jpg?tr=w-664" alt="Mahindra BE 6">
                        <span class="car-badge electric"><i class="fas fa-bolt"></i> Electric</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Mahindra BE 6</h3>
                        <p class="car-price">₹18.90 - 27.65 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-battery-full"></i> 79 kWh</span>
                            <span><i class="fas fa-road"></i> 556 km</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Mahindra/XEV-9e/9262/1755776058045/front-left-side-47.jpg?tr=w-664" alt="Mahindra XEV 9e">
                        <span class="car-badge electric"><i class="fas fa-bolt"></i> Electric</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Mahindra XEV 9e</h3>
                        <p class="car-price">₹21.90 - 31.25 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-battery-full"></i> 79 kWh</span>
                            <span><i class="fas fa-road"></i> 656 km</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/MG/Windsor-EV/11848/1755845275936/front-left-side-47.jpg?tr=w-664" alt="MG Windsor EV">
                        <span class="car-badge electric"><i class="fas fa-bolt"></i> Electric</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">MG Windsor EV</h3>
                        <p class="car-price">₹12.65 - 18.39 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-battery-full"></i> 38 kWh</span>
                            <span><i class="fas fa-road"></i> 331 km</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Tata/Nexon-EV/11024/1755845297648/front-left-side-47.jpg?tr=w-664" alt="Tata Nexon EV">
                        <span class="car-badge electric"><i class="fas fa-bolt"></i> Electric</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Tata Nexon EV</h3>
                        <p class="car-price">₹14.49 - 19.29 Lakh*</p>
                        <div class="car-specs">
                            <span><i class="fas fa-battery-full"></i> 40.5 kWh</span>
                            <span><i class="fas fa-road"></i> 465 km</span>
                        </div>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Most Searched Cars -->
    <section class="section" style="background:var(--white)">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Most <span>Searched</span> Cars</h2>
                <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="cars-grid">
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/130591/fronx-exterior-right-front-three-quarter-109.jpeg?isig=0&q=80" alt="Fronx">
                        <span class="car-badge">Popular</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Maruti FRONX</h3>
                        <p class="car-price">₹6.85 - 11.98 Lakh*</p>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/141867/nexon-exterior-right-front-three-quarter-71.jpeg?isig=0&q=80" alt="Nexon">
                        <span class="car-badge">Popular</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Tata Nexon</h3>
                        <p class="car-price">₹7.32 - 14.15 Lakh*</p>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/106815/creta-exterior-right-front-three-quarter-5.jpeg?isig=0&q=80" alt="Creta">
                        <span class="car-badge">Popular</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Hyundai Creta</h3>
                        <p class="car-price">₹10.73 - 20.20 Lakh*</p>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
                <div class="car-card">
                    <div class="car-image">
                        <img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/40087/thar-exterior-right-front-three-quarter-35.jpeg?isig=0&q=80" alt="Thar">
                        <span class="car-badge">Popular</span>
                        <button class="car-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="car-details">
                        <h3 class="car-name">Mahindra Thar</h3>
                        <p class="car-price">₹9.99 - 16.99 Lakh*</p>
                        <div class="car-actions">
                            <a href="#" class="btn btn-outline">View Details</a>
                            <a href="#" class="btn btn-primary">Get Offers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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


    <script>
  

        // Search tabs
        document.querySelectorAll('.search-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>
</html>
