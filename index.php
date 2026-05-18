<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/SellerController.php';

$page = $_GET['page'] ?? 'login';

// Public pages (no auth required)
$publicPages = ['login', 'register', 'register_submit', 'login_submit', 'logout'];

// Auth check
if (!in_array($page, $publicPages)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
    if ($_SESSION['role'] !== 'seller') {
        header('Location: index.php?page=login&error=access_denied');
        exit;
    }
}

$auth   = new AuthController();
$seller = new SellerController();

switch ($page) {
    // Auth
    case 'login':           $auth->showLogin();         break;
    case 'login_submit':    $auth->login();              break;
    case 'register':        $auth->showRegister();       break;
    case 'register_submit': $auth->register();           break;
    case 'logout':          $auth->logout();             break;

    // Seller dashboard & profile
    case 'dashboard':       $seller->dashboard();        break;
    case 'profile':         $seller->profile();          break;
    case 'profile_update':  $seller->updateProfile();    break;

    // Listings
    case 'listings':        $seller->myListings();       break;
    case 'create_listing':  $seller->createListing();    break;
    case 'store_listing':   $seller->storeListing();     break;
    case 'edit_listing':    $seller->editListing();      break;
    case 'update_listing':  $seller->updateListing();    break;
    case 'cancel_listing':  $seller->cancelListing();    break;
    case 'relist':          $seller->relistItem();       break;

    // Templates
    case 'templates':       $seller->templates();        break;
    case 'save_template':   $seller->saveTemplate();     break;
    case 'delete_template': $seller->deleteTemplate();   break;
    case 'listing_from_template': $seller->createFromTemplate(); break;

    // Bid activity (AJAX)
    case 'bid_activity':    $seller->bidActivity();      break;

    // Ended auctions
    case 'ended_auctions':  $seller->endedAuctions();    break;

    // Analytics
    case 'analytics':       $seller->analytics();        break;

    // Reviews
    case 'reviews':         $seller->reviews();          break;
    case 'respond_review':  $seller->respondReview();    break;

    default:
        header('Location: index.php?page=dashboard');
        exit;
}
