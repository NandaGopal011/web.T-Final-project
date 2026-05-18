<?php
session_start();
include 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$listings = $conn->query(
    "SELECT * FROM listings WHERE status='active'"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buyer Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            background: #f8fafc;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar{
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 18px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand{
            color: white !important;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .navbar-brand span {
            color: #38bdf8;
        }

        .nav-link{
            color: rgba(255, 255, 255, 0.8) !important;
            margin-left: 20px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .nav-link:hover, .nav-link.active{
            color: #38bdf8 !important;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -22px;
            left: 0;
            right: 0;
            height: 3px;
            background: #38bdf8;
            border-radius: 3px 3px 0 0;
        }
        
        .btn-logout-nav {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 6px 16px !important;
            border-radius: 8px;
            color: #ef4444 !important;
        }
        
        .btn-logout-nav:hover {
            background: #ef4444 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .page-content {
            display: none;
            flex-grow: 1;
            animation: fadeIn 0.4s ease-in-out forwards;
        }

        .page-content.active-page {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero{
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: white;
            padding: 120px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at center, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero h1{
            font-size: 52px;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .hero p{
            color: #94a3b8;
            font-size: 20px;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto 30px auto;
        }

        .hero-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            color: white;
        }

        .stats-grid {
            margin-top: 50px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        .stat-card h3 {
            font-size: 28px;
            font-weight: 700;
            color: #38bdf8;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 14px;
            color: #94a3b8;
            margin: 0;
        }

        .auction-section, .watchlist-section, .mybids-section, .details-section, .premium-history-section{
            padding: 80px 0;
        }

        .section-title-wrapper {
            margin-bottom: 50px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }

        .section-title {
            font-size: 34px;
            font-weight: 700;
            color: #0f172a;
            position: relative;
            display: inline-block;
            letter-spacing: -0.5px;
        }
        
        .section-title span {
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -21px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #38bdf8);
            border-radius: 10px;
        }

        .auction-card{
            background: white;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .auction-card:hover{
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.08);
        }

        .auction-image-container {
            position: relative;
            overflow: hidden;
            background: #f1f5f9;
        }

        .auction-image{
            height: 250px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .auction-card:hover .auction-image {
            transform: scale(1.06);
        }
        
        .badge-live {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #ef4444;
            color: white;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 2;
        }
        
        .badge-live span {
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1.2s infinite;
        }
        
        @keyframes blink {
            0% { opacity: 0.3; }
            50% { opacity: 1; }
            100% { opacity: 0.3; }
        }

        .card-body{
            padding: 30px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .auction-title{
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            line-height: 1.4;
            transition: color 0.3s ease;
        }

        .auction-card:hover .auction-title {
            color: #2563eb;
        }
        
        .auction-desc {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .price-container {
            background: #f8fafc;
            padding: 16px 20px;
            border-radius: 18px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(15, 23, 42, 0.03);
        }
        
        .price-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price{
            font-size: 26px;
            color: #1d4ed8;
            font-weight: 700;
        }

        .form-control-bid {
            height: 52px;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 0 18px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .form-control-bid:focus {
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        .btn-bid{
            width: 100%;
            border: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 15px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-bid:hover{
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        .btn-details-view {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            background: transparent;
            color: #475569;
            padding: 12px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
            transition: all 0.3s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-details-view:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .no-auctions-box {
            background: #ffffff;
            border-radius: 32px;
            padding: 80px 40px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(15, 23, 42, 0.04);
            max-width: 680px;
            margin: 40px auto 0 auto;
            position: relative;
            overflow: hidden;
        }

        .no-auctions-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
        }

        .icon-pulse-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1));
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px auto;
            position: relative;
        }

        .no-auctions-box i {
            font-size: 44px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            z-index: 2;
        }

        .no-auctions-box h4 {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .no-auctions-box p {
            font-size: 16px;
            color: #64748b;
            max-width: 480px;
            margin: 0 auto 30px auto;
            line-height: 1.6;
        }

        .btn-refresh {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-refresh:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.3);
            color: #ffffff;
        }

        .details-wrapper {
            background: white;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow: hidden;
            padding: 40px;
        }

        .details-img-container img {
            width: 100%;
            border-radius: 18px;
            object-fit: cover;
            height: 400px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        }

        .details-info-container {
            padding-left: 20px;
        }

        .details-info-container h2 {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .details-info-container .meta-info {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .details-info-container .meta-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
        }

        .premium-table-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .premium-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 18px;
            border-bottom: 2px solid #edf2f7;
        }

        .premium-table td {
            padding: 18px;
            vertical-align: middle;
            color: #334155;
            font-size: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .history-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-winning { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .status-outbid { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .status-watched { background: rgba(56, 189, 248, 0.1); color: #0284c7; }

        .footer{
            background: #0f172a;
            color: #94a3b8;
            text-align: center;
            padding: 35px;
            margin-top: auto;
            font-size: 14px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
    </style>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" onclick="switchPage('buyer/dashboard.php')">
            🔨 Auction<span>Hub</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" id="nav-dashboard" onclick="switchPage('buyer/dashboard.php')">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="nav-auctions" onclick="switchPage('buyer/auctions.php')">Auctions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="nav-mybids" onclick="switchPage('buyer/my_bids.php')">My Bids</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="nav-watchlist" onclick="switchPage('buyer/watchlist.php')">Watchlist</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-logout-nav" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="page-dashboard" class="page-content active-page">
    <section class="hero">
        <div class="container">
            <h1>Welcome to Online Auction System</h1>
            <p>Bid Smart • Win Big • Buy Amazing Products</p>
            <a onclick="switchPage('buyer/auctions.php')" class="hero-btn">
                <i class="bi bi-gavel"></i> Explore Live Auctions
            </a>
            
            <div class="row g-4 stats-grid justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <h3>10K+</h3>
                        <p>Active Bidders</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <h3>৳2M+</h3>
                        <p>Total Volume</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <h3>99.8%</h3>
                        <p>Secure Escrow</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="premium-history-section container">
        <div class="section-title-wrapper">
            <h2 class="section-title">Platform <span>Activity & Features</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="auction-card">
                    <div class="auction-image-container">
                        <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500" class="auction-image" alt="Feature 1">
                    </div>
                    <div class="card-body">
                        <h3 class="auction-title">Verified Luxury Assets</h3>
                        <p class="auction-desc">Every luxury item hosted under our ecosystem is verified meticulously by corporate industry professionals ensuring ultimate legitimacy.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auction-card">
                    <div class="auction-image-container">
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1d704d3?w=500" class="auction-image" alt="Feature 2">
                    </div>
                    <div class="card-body">
                        <h3 class="auction-title">Real-Time Secure Payments</h3>
                        <p class="auction-desc">Integrated tier-1 secure bank standard payment methods protect your transaction volume seamlessly with automated instantaneous release.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auction-card">
                    <div class="auction-image-container">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500" class="auction-image" alt="Feature 3">
                    </div>
                    <div class="card-body">
                        <h3 class="auction-title">Instant Outbid Analytics</h3>
                        <p class="auction-desc">Receive real-time system alerts the exact second another user passes your bid threshold dynamically without looking over.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="page-auctions" class="page-content">
    <section class="auction-section">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Active Live <span>Auctions</span></h2>
            </div>
            
            <div class="row g-4">
            <?php if($listings->num_rows > 0) { 
                while($row = $listings->fetch_assoc()) { ?>
                <div class="col-lg-4 col-md-6">
                    <div class="auction-card">
                        <div class="auction-image-container">
                            <span class="badge-live"><span></span>Live</span>
                            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30" class="auction-image" alt="Product Image">
                        </div>

                        <div class="card-body">
                            <h3 class="auction-title">
                                <?php echo $row['title']; ?>
                            </h3>

                            <p class="auction-desc">
                                <?php echo $row['description']; ?>
                            </p>

                            <div class="price-container">
                                <span class="price-label">Current Bid</span>
                                <div class="price">
                                    ৳ <?php echo $row['current_bid']; ?>
                                </div>
                            </div>

                            <form action="api/place_bid.php" method="POST">
                                <input type="hidden" name="listing_id" value="<?php echo $row['id']; ?>">

                                <div class="mb-3">
                                    <input type="number" step="0.01" name="amount" class="form-control form-control-bid" placeholder="Enter Your Bid Amount" required>
                                </div>

                                <button type="submit" class="btn-bid">
                                    <i class="bi bi-hammer"></i> Place Bid
                                </button>
                            </form>

                            <button onclick="viewProductDetails('<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['description']); ?>', '<?php echo $row['current_bid']; ?>')" class="btn-details-view">
                                <i class="bi bi-eye"></i> View Full Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php } 
            } else { ?>
                <div class="col-12 text-center">
                    <div class="no-auctions-box">
                        <div class="icon-pulse-wrapper">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <h4>No Live Auctions Available</h4>
                        <p>Right now, there are no live bidding sessions running. Our team is curated with high-end luxury assets shortly. Check back soon or refresh below.</p>
                        <a onclick="location.reload()" class="btn-refresh">
                            <i class="bi bi-arrow-clockwise"></i> Refresh Feed
                        </a>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </section>
</div>

<div id="page-details" class="page-content">
    <section class="details-section">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">Auction <span>Details</span></h2>
            </div>
            
            <div class="details-wrapper">
                <div class="row g-5">
                    <div class="col-md-6 details-img-container">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30" alt="Product Image">
                    </div>
                    <div class="col-md-6">
                        <div class="details-info-container">
                            <h2 id="detail-title">Premium Asset</h2>
                            <div class="meta-info">
                                <span class="meta-badge"><i class="bi bi-shield-check"></i> Verified Asset</span>
                                <span class="meta-badge"><i class="bi bi-clock"></i> Active Session</span>
                            </div>
                            <p id="detail-desc" class="text-muted lh-lg mb-4"></p>
                            
                            <div class="price-container mb-4">
                                <span class="price-label">Current Standing Bid</span>
                                <div class="price" id="detail-price">৳ 0.00</div>
                            </div>

                            <a onclick="switchPage('buyer/auctions.php')" class="btn-refresh bg-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Live Feed
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="page-mybids" class="page-content">
    <section class="mybids-section">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">My Bidding <span>History</span></h2>
            </div>
            
            <div class="premium-table-card">
                <div class="table-responsive">
                    <table class="table premium-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Title</th>
                                <th>My Bid</th>
                                <th>Highest Bid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=100" class="history-img" alt="Luxury Watch"></td>
                                <td>Rolex Submariner Date Luxury Asset</td>
                                <td>৳ 850,000</td>
                                <td>৳ 850,000</td>
                                <td><span class="status-badge status-winning"><i class="bi bi-check-circle"></i> Highest Bidder</span></td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100" class="history-img" alt="Headphones"></td>
                                <td>Premium Wireless Studio Audio Gear</td>
                                <td>৳ 32,000</td>
                                <td>৳ 35,500</td>
                                <td><span class="status-badge status-outbid"><i class="bi bi-x-circle"></i> Outbid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="page-watchlist" class="page-content">
    <section class="watchlist-section">
        <div class="container">
            <div class="section-title-wrapper">
                <h2 class="section-title">My Premium <span>Watchlist</span></h2>
            </div>
            
            <div class="premium-table-card">
                <div class="table-responsive">
                    <table class="table premium-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Title</th>
                                <th>Opening Price</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=100" class="history-img" alt="Tech"></td>
                                <td>Ultra Custom Liquid Cooled Gaming Station</td>
                                <td>৳ 180,000</td>
                                <td><span class="status-badge status-watched">Active Live</span></td>
                                <td><button onclick="switchPage('buyer/auctions.php')" class="btn-bid py-2 px-3 fs-6 w-auto d-inline-flex"><i class="bi bi-hammer"></i> Bid Now</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="footer">
    © 2026 Online Auction System | Designed with Excellence. All Rights Reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function switchPage(filePath) {
        document.querySelectorAll('.page-content').forEach(page => {
            page.classList.remove('active-page');
        });
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        let targetId = 'dashboard';
        let navId = 'dashboard';

        if(filePath === 'buyer/dashboard.php') { targetId = 'dashboard'; navId = 'dashboard'; }
        else if(filePath === 'buyer/auctions.php') { targetId = 'auctions'; navId = 'auctions'; }
        else if(filePath === 'buyer/auction_details.php') { targetId = 'details'; navId = 'auctions'; }
        else if(filePath === 'buyer/my_bids.php') { targetId = 'mybids'; navId = 'mybids'; }
        else if(filePath === 'buyer/watchlist.php') { targetId = 'watchlist'; navId = 'watchlist'; }

        document.getElementById('page-' + targetId).classList.add('active-page');
        let activeNavLink = document.getElementById('nav-' + navId);
        if(activeNavLink) activeNavLink.classList.add('active');
        
        window.scrollTo(0, 0);
        
        const menuToggle = document.getElementById('menu');
        const bsCollapse = bootstrap.Collapse.getInstance(menuToggle);
        if (bsCollapse) {
            bsCollapse.hide();
        }
    }

    function viewProductDetails(title, description, price) {
        document.getElementById('detail-title').innerText = title;
        document.getElementById('detail-desc').innerText = description;
        document.getElementById('detail-price').innerText = '৳ ' + price;
        switchPage('buyer/auction_details.php');
    }
</script>

</body>
</html>