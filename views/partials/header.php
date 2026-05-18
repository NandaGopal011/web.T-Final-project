<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Seller Panel') ?> — AuctionHub</title>
<link rel="stylesheet" href="public/css/seller.css">
</head>
<body>
<nav class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">🏷️</span>
        <span>AuctionHub</span>
    </div>
    <div class="sidebar-user">
        <div class="avatar"><?= strtoupper(substr($_SESSION['name'], 0, 1)) ?></div>
        <div>
            <div class="sidebar-username"><?= htmlspecialchars($_SESSION['name']) ?></div>
            <div class="sidebar-role"><?= $_SESSION['verified'] ? '✅ Verified Seller' : '⏳ Pending Verification' ?></div>
        </div>
    </div>
    <ul class="nav-links">
        <li><a href="index.php?page=dashboard"   <?= (($_GET['page']??'')=='dashboard')   ? 'class="active"' : '' ?>>📊 Dashboard</a></li>
        <li><a href="index.php?page=listings"    <?= (($_GET['page']??'')=='listings')    ? 'class="active"' : '' ?>>📋 My Listings</a></li>
        <li><a href="index.php?page=create_listing" <?= (($_GET['page']??'')=='create_listing') ? 'class="active"' : '' ?>>➕ New Listing</a></li>
        <li><a href="index.php?page=ended_auctions" <?= (($_GET['page']??'')=='ended_auctions') ? 'class="active"' : '' ?>>🏁 Ended Auctions</a></li>
        <li><a href="index.php?page=templates"   <?= (($_GET['page']??'')=='templates')   ? 'class="active"' : '' ?>>📁 Templates</a></li>
        <li><a href="index.php?page=analytics"   <?= (($_GET['page']??'')=='analytics')   ? 'class="active"' : '' ?>>📈 Analytics</a></li>
        <li><a href="index.php?page=reviews"     <?= (($_GET['page']??'')=='reviews')     ? 'class="active"' : '' ?>>⭐ Reviews</a></li>
        <li><a href="index.php?page=profile"     <?= (($_GET['page']??'')=='profile')     ? 'class="active"' : '' ?>>👤 Profile</a></li>
        <li class="nav-logout"><a href="index.php?page=logout">🚪 Logout</a></li>
    </ul>
</nav>
<main class="main-content">
