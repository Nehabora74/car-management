<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CarDekho</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--primary:#FF6B35;--primary-dark:#E55A2B;--secondary:#1A1A2E;--sidebar-bg:#0F0F1A;--card-bg:#FFFFFF;--text-dark:#2D2D2D;--text-light:#6B7280;--bg-light:#F5F7FA;--border:#E5E7EB;--success:#10B981;--warning:#F59E0B;--danger:#EF4444;--info:#3B82F6}
        body{font-family:'Poppins',sans-serif;background:var(--bg-light);color:var(--text-dark);display:flex;min-height:100vh}
        
        /* Sidebar */
        .sidebar{width:260px;background:var(--sidebar-bg);color:#fff;position:fixed;height:100vh;overflow-y:auto;transition:all .3s}
        .sidebar-header{padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.1)}
        .sidebar-logo{display:flex;align-items:center;gap:.75rem;font-size:1.4rem;font-weight:700}
        .sidebar-logo i,.sidebar-logo span{color:var(--primary)}
        .sidebar-menu{padding:1rem 0}
        .menu-label{padding:.75rem 1.5rem;font-size:.7rem;text-transform:uppercase;color:rgba(255,255,255,.4);letter-spacing:1px}
        .menu-item{display:flex;align-items:center;gap:.75rem;padding:.85rem 1.5rem;color:rgba(255,255,255,.7);text-decoration:none;transition:all .3s;border-left:3px solid transparent;font-size:.9rem;cursor:pointer}
        .menu-item:hover,.menu-item.active{background:rgba(255,107,53,.1);color:var(--primary);border-left-color:var(--primary)}
        .menu-item i{width:18px;text-align:center}
        
        /* Main Content */
        .main-content{flex:1;margin-left:260px;padding:2rem}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
        .page-title{font-size:1.6rem;font-weight:700;color:var(--secondary)}
        .btn{padding:.65rem 1.25rem;border-radius:8px;font-weight:600;font-size:.85rem;cursor:pointer;transition:all .3s;border:none;display:inline-flex;align-items:center;gap:.5rem;text-decoration:none}
        .btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 5px 20px rgba(255,107,53,.4)}
        .btn-outline{background:transparent;border:2px solid var(--border);color:var(--text-dark)}
        .btn-outline:hover{border-color:var(--primary);color:var(--primary)}
        .btn-success{background:var(--success);color:#fff}
        .btn-danger{background:var(--danger);color:#fff}
        .btn-sm{padding:.5rem 1rem;font-size:.8rem}
        
        /* Stats */
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:2rem}
        .stat-card{background:var(--card-bg);border-radius:16px;padding:1.5rem;display:flex;align-items:center;gap:1rem;box-shadow:0 4px 15px rgba(0,0,0,.05);transition:all .3s}
        .stat-card:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(0,0,0,.1)}
        .stat-icon{width:55px;height:55px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff}
        .stat-icon.cars{background:linear-gradient(135deg,var(--primary),var(--primary-dark))}
        .stat-icon.banners{background:linear-gradient(135deg,var(--info),#2563EB)}
        .stat-icon.menu{background:linear-gradient(135deg,var(--success),#059669)}
        .stat-icon.customers{background:linear-gradient(135deg,var(--warning),#D97706)}
        .stat-info h3{font-size:1.6rem;font-weight:700;color:var(--secondary)}
        .stat-info p{color:var(--text-light);font-size:.85rem}
        
        /* Card */
        .card{background:var(--card-bg);border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,.05);overflow:hidden;margin-bottom:1.5rem}
        .card-header{padding:1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
        .card-title{font-size:1rem;font-weight:600;color:var(--secondary)}
        .card-body{padding:1.5rem}
        
        /* Table */
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{padding:.85rem;text-align:left;border-bottom:1px solid var(--border);font-size:.85rem}
        .table th{font-weight:600;color:var(--text-light);text-transform:uppercase;font-size:.75rem;background:var(--bg-light)}
        .table tr:hover{background:var(--bg-light)}
        .table img{width:60px;height:45px;object-fit:cover;border-radius:8px}
        .badge{padding:.25rem .6rem;border-radius:15px;font-size:.7rem;font-weight:600}
        .badge-success{background:rgba(16,185,129,.1);color:var(--success)}
        .badge-warning{background:rgba(245,158,11,.1);color:var(--warning)}
        .badge-info{background:rgba(59,130,246,.1);color:var(--info)}
        .badge-danger{background:rgba(239,68,68,.1);color:var(--danger)}
        .action-btn{width:32px;height:32px;border:none;border-radius:6px;cursor:pointer;transition:all .3s;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:.8rem;margin-right:.25rem}
        .action-btn.edit{background:rgba(59,130,246,.1);color:var(--info)}
        .action-btn.delete{background:rgba(239,68,68,.1);color:var(--danger)}
        .action-btn:hover{transform:scale(1.1)}
        
        /* Forms */
        .form-group{margin-bottom:1.25rem}
        .form-group label{display:block;font-weight:500;margin-bottom:.5rem;font-size:.9rem}
        .form-control{width:100%;padding:.75rem 1rem;border:2px solid var(--border);border-radius:10px;font-size:.95rem;font-family:inherit;transition:all .3s}
        .form-control:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(255,107,53,.1)}
        textarea.form-control{min-height:100px;resize:vertical}
        select.form-control{cursor:pointer}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
        .form-check{display:flex;align-items:center;gap:.5rem}
        .form-check input{width:18px;height:18px;accent-color:var(--primary)}
        
        /* Image Preview */
        .image-preview{width:150px;height:100px;border:2px dashed var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-top:.5rem}
        .image-preview img{width:100%;height:100%;object-fit:cover}
        .image-preview i{font-size:2rem;color:var(--border)}
        
        /* Tabs */
        .tabs{display:flex;gap:.5rem;margin-bottom:1.5rem;border-bottom:2px solid var(--border);padding-bottom:-2px}
        .tab{padding:.75rem 1.5rem;background:transparent;border:none;font-weight:600;cursor:pointer;position:relative;color:var(--text-light);font-family:inherit;font-size:.9rem}
        .tab.active{color:var(--primary)}
        .tab.active::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:2px;background:var(--primary)}
        .tab-content{display:none}
        .tab-content.active{display:block}
        
        /* Quick Links */
        .quick-links{display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem}
        .quick-link{display:flex;align-items:center;gap:.75rem;padding:1rem;background:var(--bg-light);border-radius:10px;text-decoration:none;color:var(--text-dark);transition:all .3s;font-size:.9rem;cursor:pointer}
        .quick-link:hover{background:var(--primary);color:#fff;transform:translateX(5px)}
        .quick-link i{width:40px;height:40px;background:var(--card-bg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:.9rem}
        .quick-link:hover i{background:rgba(255,255,255,.2);color:#fff}
        
        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:1000}
        .modal-overlay.show{display:flex}
        .modal{background:var(--card-bg);border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;animation:slideIn .3s ease}
        @keyframes slideIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
        .modal-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
        .modal-header h3{font-size:1.1rem;color:var(--secondary)}
        .modal-close{width:35px;height:35px;border:none;background:var(--bg-light);border-radius:8px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center}
        .modal-close:hover{background:var(--danger);color:#fff}
        .modal-body{padding:1.5rem}
        .modal-footer{padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:.75rem}
        
        /* Alert */
        .alert{padding:1rem 1.5rem;border-radius:10px;margin-bottom:1rem;display:flex;align-items:center;gap:.75rem}
        .alert-success{background:rgba(16,185,129,.1);color:var(--success)}
        .alert-danger{background:rgba(239,68,68,.1);color:var(--danger)}
        
        /* Content Sections */
        .content-section{display:none}
        .content-section.active{display:block}
        
        /* Responsive */
        @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}.quick-links{grid-template-columns:1fr}}
        @media(max-width:768px){.sidebar{transform:translateX(-100%);position:fixed;z-index:100}.sidebar.open{transform:translateX(0)}.main-content{margin-left:0}.stats-grid{grid-template-columns:1fr}.form-row{grid-template-columns:1fr}.mobile-toggle{display:block!important}}
        .mobile-toggle{display:none;position:fixed;bottom:1.5rem;right:1.5rem;width:50px;height:50px;background:var(--primary);color:#fff;border:none;border-radius:50%;font-size:1.2rem;cursor:pointer;z-index:99;box-shadow:0 4px 15px rgba(255,107,53,.4)}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
           <div class="sidebar-logo"><i class="fas fa-car"></i> </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a class="menu-item active" data-section="dashboard"><i class="fas fa-home"></i> Dashboard</a>
            <div class="menu-label">Content Management</div>
            <a class="menu-item" data-section="banners"><i class="fas fa-images"></i> Banners</a>
            <a class="menu-item" data-section="cars"><i class="fas fa-car-side"></i> Cars</a>
            <a class="menu-item" data-section="menu"><i class="fas fa-bars"></i> Menu Items</a>
            <div class="menu-label">Settings</div>
            <a class="menu-item" data-section="settings"><i class="fas fa-cog"></i> Site Settings</a>
            <a class="menu-item" data-section="customers"><i class="fas fa-users"></i> Customers</a>
            <div class="menu-label">Actions</div>
            <a class="menu-item" href="index.html" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a>
        </nav>
    </aside>
    
    <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        
        <!-- Dashboard Section -->
        <div class="content-section active" id="dashboard">
            <div class="topbar">
                <h1 class="page-title">Dashboard</h1>
                <div style="display:flex;gap:.75rem">
                    <a href="index.html" target="_blank" class="btn btn-outline"><i class="fas fa-eye"></i> View Site</a>
                    <button class="btn btn-primary" onclick="showSection('cars');openModal('carModal')"><i class="fas fa-plus"></i> Add Car</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><div class="stat-icon cars"><i class="fas fa-car"></i></div><div class="stat-info"><h3>12</h3><p>Total Cars</p></div></div>
                <div class="stat-card"><div class="stat-icon banners"><i class="fas fa-images"></i></div><div class="stat-info"><h3>3</h3><p>Banners</p></div></div>
                <div class="stat-card"><div class="stat-icon menu"><i class="fas fa-bars"></i></div><div class="stat-info"><h3>5</h3><p>Menu Items</p></div></div>
                <div class="stat-card"><div class="stat-icon customers"><i class="fas fa-users"></i></div><div class="stat-info"><h3>28</h3><p>Customers</p></div></div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Cars</h3>
                        <button class="btn btn-outline btn-sm" onclick="showSection('cars')">View All</button>
                    </div>
                    <div class="card-body" style="padding:0">
                        <table class="table">
                            <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Type</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/MG/Hector/13125/1768813549348/front-left-side-47.jpg?tr=w-664"></td>
                                    <td>MG Hector 2025</td>
                                    <td>₹11.99 - 18.99 Lakh</td>
                                    <td><span class="badge badge-success">Latest</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Mahindra/BE-6/9263/1762423834412/front-left-side-47.jpg?tr=w-664"></td>
                                    <td>Mahindra BE 6</td>
                                    <td>₹18.90 - 27.65 Lakh</td>
                                    <td><span class="badge badge-info">Electric</span></td>
                                </tr>
                                <tr>
                                    <td><img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/130591/fronx-exterior-right-front-three-quarter-109.jpeg?isig=0&q=80"></td>
                                    <td>Maruti FRONX</td>
                                    <td>₹6.85 - 11.98 Lakh</td>
                                    <td><span class="badge badge-warning">Most Searched</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                    <div class="card-body">
                        <div class="quick-links">
                            <a class="quick-link" onclick="showSection('cars');openModal('carModal')"><i class="fas fa-plus"></i><span>Add Car</span></a>
                            <a class="quick-link" onclick="showSection('banners');openModal('bannerModal')"><i class="fas fa-image"></i><span>Add Banner</span></a>
                            <a class="quick-link" onclick="showSection('menu');openModal('menuModal')"><i class="fas fa-link"></i><span>Add Menu</span></a>
                            <a class="quick-link" onclick="showSection('settings')"><i class="fas fa-cog"></i><span>Settings</span></a>
                            <a class="quick-link" onclick="showSection('customers')"><i class="fas fa-users"></i><span>Customers</span></a>
                            <a class="quick-link" href="index.html" target="_blank"><i class="fas fa-eye"></i><span>View Site</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cars Section -->
        <div class="content-section" id="cars">
            <div class="topbar">
                <h1 class="page-title">Manage Cars</h1>
                <button class="btn btn-primary" onclick="openModal('carModal')"><i class="fas fa-plus"></i> Add New Car</button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="tabs" style="border:none;margin:0;padding:0">
                        <button class="tab active" onclick="filterCars('all')">All Cars</button>
                        <button class="tab" onclick="filterCars('latest')">Latest</button>
                        <button class="tab" onclick="filterCars('electric')">Electric</button>
                        <button class="tab" onclick="filterCars('most_searched')">Most Searched</button>
                    </div>
                </div>
                <div class="card-body" style="padding:0">
                    <table class="table" id="carsTable">
                        <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr data-type="latest">
                                <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/MG/Hector/13125/1768813549348/front-left-side-47.jpg?tr=w-664"></td>
                                <td>MG Hector 2025</td>
                                <td>₹11.99 - 18.99 Lakh</td>
                                <td><span class="badge badge-success">Latest</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr data-type="latest">
                                <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Tata/Sierra/12271/1765181428462/front-left-side-47.jpg?tr=w-664"></td>
                                <td>Tata Sierra</td>
                                <td>₹11.49 - 21.29 Lakh</td>
                                <td><span class="badge badge-success">Latest</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr data-type="electric">
                                <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Mahindra/BE-6/9263/1762423834412/front-left-side-47.jpg?tr=w-664"></td>
                                <td>Mahindra BE 6</td>
                                <td>₹18.90 - 27.65 Lakh</td>
                                <td><span class="badge badge-info">Electric</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr data-type="electric">
                                <td><img src="https://stimg.cardekho.com/images/carexteriorimages/630x420/Mahindra/XEV-9e/9262/1755776058045/front-left-side-47.jpg?tr=w-664"></td>
                                <td>Mahindra XEV 9e</td>
                                <td>₹21.90 - 31.25 Lakh</td>
                                <td><span class="badge badge-info">Electric</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr data-type="most_searched">
                                <td><img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/130591/fronx-exterior-right-front-three-quarter-109.jpeg?isig=0&q=80"></td>
                                <td>Maruti FRONX</td>
                                <td>₹6.85 - 11.98 Lakh</td>
                                <td><span class="badge badge-warning">Most Searched</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr data-type="most_searched">
                                <td><img src="https://imgd.aeplcdn.com/664x374/n/cw/ec/106815/creta-exterior-right-front-three-quarter-5.jpeg?isig=0&q=80"></td>
                                <td>Hyundai Creta</td>
                                <td>₹10.73 - 20.20 Lakh</td>
                                <td><span class="badge badge-warning">Most Searched</span></td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="action-btn edit" onclick="openModal('carModal')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Banners Section -->
        <div class="content-section" id="banners">
            <div class="topbar">
                <h1 class="page-title">Manage Banners</h1>
                <button class="btn btn-primary" onclick="openModal('bannerModal')"><i class="fas fa-plus"></i> Add Banner</button>
            </div>
            
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.5rem">
                <div class="card">
                    <div style="height:200px;overflow:hidden">
                        <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800" style="width:100%;height:100%;object-fit:cover">
                    </div>
                    <div class="card-body">
                        <h3 style="margin-bottom:.5rem">Find Your Right Car</h3>
                        <p style="color:var(--text-light);font-size:.9rem;margin-bottom:1rem">Explore latest cars and get the best deals</p>
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span class="badge badge-success">Active</span>
                            <div>
                                <button class="action-btn edit" onclick="openModal('bannerModal')"><i class="fas fa-edit"></i></button>
                                <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div style="height:200px;overflow:hidden">
                        <img src="https://images.unsplash.com/photo-1593941707882-a5bba14938c7?w=800" style="width:100%;height:100%;object-fit:cover">
                    </div>
                    <div class="card-body">
                        <h3 style="margin-bottom:.5rem">Go Electric Today</h3>
                        <p style="color:var(--text-light);font-size:.9rem;margin-bottom:1rem">Discover the future of mobility</p>
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span class="badge badge-success">Active</span>
                            <div>
                                <button class="action-btn edit" onclick="openModal('bannerModal')"><i class="fas fa-edit"></i></button>
                                <button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="content-section" id="menu">
            <div class="topbar">
                <h1 class="page-title">Manage Menu Items</h1>
                <button class="btn btn-primary" onclick="openModal('menuModal')"><i class="fas fa-plus"></i> Add Menu Item</button>
            </div>
            
            <div class="card">
                <div class="card-body" style="padding:0">
                    <table class="table">
                        <thead><tr><th>Title</th><th>URL</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr><td>New Cars</td><td>#new-cars</td><td>1</td><td><span class="badge badge-success">Active</span></td><td><button class="action-btn edit" onclick="openModal('menuModal')"><i class="fas fa-edit"></i></button><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td></tr>
                            <tr><td>Electric Cars</td><td>#electric-cars</td><td>2</td><td><span class="badge badge-success">Active</span></td><td><button class="action-btn edit" onclick="openModal('menuModal')"><i class="fas fa-edit"></i></button><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td></tr>
                            <tr><td>Compare</td><td>#compare</td><td>3</td><td><span class="badge badge-success">Active</span></td><td><button class="action-btn edit" onclick="openModal('menuModal')"><i class="fas fa-edit"></i></button><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td></tr>
                            <tr><td>News</td><td>#news</td><td>4</td><td><span class="badge badge-success">Active</span></td><td><button class="action-btn edit" onclick="openModal('menuModal')"><i class="fas fa-edit"></i></button><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td></tr>
                            <tr><td>Reviews</td><td>#reviews</td><td>5</td><td><span class="badge badge-success">Active</span></td><td><button class="action-btn edit" onclick="openModal('menuModal')"><i class="fas fa-edit"></i></button><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Settings Section -->
        <div class="content-section" id="settings">
            <div class="topbar">
                <h1 class="page-title">Site Settings</h1>
                <button class="btn btn-success" onclick="showAlert('Settings saved successfully!')"><i class="fas fa-save"></i> Save Changes</button>
            </div>
            
            <div class="alert alert-success" id="settingsAlert" style="display:none">
                <i class="fas fa-check-circle"></i> Settings saved successfully!
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-globe" style="color:var(--primary);margin-right:.5rem"></i> General Settings</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <label>Site Logo</label>
                            <input type="file" class="form-control">
                            <div class="image-preview"><i class="fas fa-image"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-address-book" style="color:var(--info);margin-right:.5rem"></i> Contact Information</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fas fa-phone" style="color:var(--primary)"></i> Phone</label>
                            <input type="text" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope" style="color:var(--primary)"></i> Email</label>
                            <input type="email" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt" style="color:var(--primary)"></i> Address</label>
                            <textarea class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-share-alt" style="color:var(--success);margin-right:.5rem"></i> Social Media</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fab fa-facebook" style="color:#1877F2"></i> Facebook</label>
                            <input type="url" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-twitter" style="color:#1DA1F2"></i> Twitter</label>
                            <input type="url" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-instagram" style="color:#E4405F"></i> Instagram</label>
                            <input type="url" class="form-control" >
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-youtube" style="color:#FF0000"></i> YouTube</label>
                            <input type="url" class="form-control" >
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-file-alt" style="color:var(--warning);margin-right:.5rem"></i> Footer Settings</h3></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Footer Text</label>
                            <textarea class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Section -->
        <div class="content-section" id="customers">
            <div class="topbar">
                <h1 class="page-title">Customer Inquiries</h1>
                <span class="badge badge-info" style="padding:.5rem 1rem;font-size:.9rem">28 Total Submissions</span>
            </div>
            
            <div class="card">
                <div class="card-body" style="padding:0">
                    <table class="table">
                        <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Car Types</th><th>Date</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><strong>Rahul Sharma</strong><br><small style="color:var(--text-light)">Delhi, India</small></td>
                                <td>9876543210</td>
                                <td>rahul@email.com</td>
                                <td><span class="badge badge-info">SUV</span> <span class="badge badge-warning">Sedan</span></td>
                                <td>21 Jan 2026</td>
                                <td><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Priya Singh</strong><br><small style="color:var(--text-light)">Mumbai, India</small></td>
                                <td>9123456780</td>
                                <td>priya@email.com</td>
                                <td><span class="badge badge-success">Hatchback</span></td>
                                <td>20 Jan 2026</td>
                                <td><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Amit Patel</strong><br><small style="color:var(--text-light)">Ahmedabad, India</small></td>
                                <td>9988776655</td>
                                <td>amit@email.com</td>
                                <td><span class="badge badge-info">SUV</span></td>
                                <td>19 Jan 2026</td>
                                <td><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Neha Gupta</strong><br><small style="color:var(--text-light)">Bangalore, India</small></td>
                                <td>9876501234</td>
                                <td>neha@email.com</td>
                                <td><span class="badge badge-success">Hatchback</span> <span class="badge badge-warning">Sedan</span> <span class="badge badge-info">SUV</span></td>
                                <td>18 Jan 2026</td>
                                <td><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Vikram Yadav</strong><br><small style="color:var(--text-light)">Jaipur, India</small></td>
                                <td>9012345678</td>
                                <td>vikram@email.com</td>
                                <td><span class="badge badge-warning">Sedan</span></td>
                                <td>17 Jan 2026</td>
                                <td><button class="action-btn delete" onclick="confirmDelete()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Car Modal -->
    <div class="modal-overlay" id="carModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add New Car</h3>
                <button class="modal-close" onclick="closeModal('carModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Car Name *</label>
                        <input type="text" class="form-control" placeholder="e.g. Tata Nexon">
                    </div>
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="text" class="form-control" placeholder="e.g. ₹7.99 - 14.20 Lakh">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Car Type *</label>
                        <select class="form-control">
                            <option value="latest">Latest</option>
                            <option value="electric">Electric</option>
                            <option value="most_searched">Most Searched</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" value="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Fuel Type</label>
                        <input type="text" class="form-control" placeholder="e.g. Petrol/Diesel">
                    </div>
                    <div class="form-group">
                        <label>Transmission</label>
                        <input type="text" class="form-control" placeholder="e.g. MT/AT">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Battery (for Electric)</label>
                        <input type="text" class="form-control" placeholder="e.g. 40.5 kWh">
                    </div>
                    <div class="form-group">
                        <label>Range (for Electric)</label>
                        <input type="text" class="form-control" placeholder="e.g. 465 km">
                    </div>
                </div>
                <div class="form-group">
                    <label>Car Image *</label>
                    <input type="file" class="form-control" accept="image/*">
                    <div class="image-preview"><i class="fas fa-car"></i></div>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="carActive" checked>
                    <label for="carActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('carModal')">Cancel</button>
                <button class="btn btn-primary" onclick="closeModal('carModal');showAlert('Car added successfully!')">Save Car</button>
            </div>
        </div>
    </div>

    <!-- Banner Modal -->
    <div class="modal-overlay" id="bannerModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add New Banner</h3>
                <button class="modal-close" onclick="closeModal('bannerModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Banner Title *</label>
                    <input type="text" class="form-control" placeholder="e.g. Find Your Dream Car">
                </div>
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" class="form-control" placeholder="e.g. Explore latest cars...">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Button Text</label>
                        <input type="text" class="form-control" placeholder="e.g. Start Searching">
                    </div>
                    <div class="form-group">
                        <label>Button URL</label>
                        <input type="text" class="form-control" placeholder="e.g. #search">
                    </div>
                </div>
                <div class="form-group">
                    <label>Banner Image * (Recommended: 1920x600px)</label>
                    <input type="file" class="form-control" accept="image/*">
                    <div class="image-preview" style="width:100%;height:150px"><i class="fas fa-image"></i></div>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="bannerActive" checked>
                    <label for="bannerActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('bannerModal')">Cancel</button>
                <button class="btn btn-primary" onclick="closeModal('bannerModal');showAlert('Banner added successfully!')">Save Banner</button>
            </div>
        </div>
    </div>

    <!-- Menu Modal -->
    <div class="modal-overlay" id="menuModal">
        <div class="modal">
            <div class="modal-header">
                <h3>Add Menu Item</h3>
                <button class="modal-close" onclick="closeModal('menuModal')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" class="form-control" placeholder="e.g. New Cars">
                </div>
                <div class="form-group">
                    <label>URL *</label>
                    <input type="text" class="form-control" placeholder="e.g. #new-cars">
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" class="form-control" value="1">
                </div>
                <div class="form-check">
                    <input type="checkbox" id="menuActive" checked>
                    <label for="menuActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('menuModal')">Cancel</button>
                <button class="btn btn-primary" onclick="closeModal('menuModal');showAlert('Menu item added successfully!')">Save Menu</button>
            </div>
        </div>
    </div>

    <script>
        // Section Navigation
        document.querySelectorAll('.menu-item[data-section]').forEach(item => {
            item.addEventListener('click', function() {
                const section = this.dataset.section;
                showSection(section);
            });
        });

        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
            document.querySelector(`[data-section="${sectionId}"]`)?.classList.add('active');
            document.getElementById('sidebar').classList.remove('open');
        }

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });

        // Filter Cars
        function filterCars(type) {
            document.querySelectorAll('.tabs .tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            document.querySelectorAll('#carsTable tbody tr').forEach(row => {
                if (type === 'all' || row.dataset.type === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Confirm Delete
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this item?')) {
                showAlert('Item deleted successfully!');
            }
        }

        // Show Alert
        function showAlert(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
            alertDiv.style.position = 'fixed';
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.style.animation = 'slideIn .3s ease';
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>
</body>
</html>
