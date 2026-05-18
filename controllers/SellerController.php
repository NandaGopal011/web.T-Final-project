<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ListingModel.php';
require_once __DIR__ . '/../models/TemplateModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class SellerController {
    private UserModel    $userModel;
    private ListingModel $listingModel;
    private TemplateModel $templateModel;
    private CategoryModel $categoryModel;

    public function __construct() {
        $this->userModel     = new UserModel();
        $this->listingModel  = new ListingModel();
        $this->templateModel = new TemplateModel();
        $this->categoryModel = new CategoryModel();
    }

    private function sellerId(): int {
        return (int)$_SESSION['user_id'];
    }

    private function requireVerified(): void {
        if (!$_SESSION['verified']) {
            header('Location: index.php?page=dashboard&error=not_verified');
            exit;
        }
    }

    // ─── Dashboard ────────────────────────────────────────────────
    public function dashboard(): void {
        $seller   = $this->userModel->findById($this->sellerId());
        $active   = $this->listingModel->getBySellerAndStatus($this->sellerId(), 'active');
        $pending  = $this->listingModel->getBySellerAndStatus($this->sellerId(), 'pending_review');
        $ended    = $this->listingModel->getBySellerAndStatus($this->sellerId(), 'ended');
        $verReq   = $this->userModel->getVerificationStatus($this->sellerId());
        require __DIR__ . '/../views/seller/dashboard.php';
    }

    // ─── Profile ─────────────────────────────────────────────────
    public function profile(): void {
        $seller  = $this->userModel->findById($this->sellerId());
        $reviews = $this->userModel->getReviews($this->sellerId());
        require __DIR__ . '/../views/seller/profile.php';
    }

    public function updateProfile(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=profile');
            exit;
        }
        $id      = $this->sellerId();
        $seller  = $this->userModel->findById($id);
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $bio     = trim($_POST['bio'] ?? '');

        $profilePic = $seller['profile_pic'];
        if (!empty($_FILES['profile_pic']['name'])) {
            $ext      = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg','jpeg','png','webp'];
            if (in_array($ext, $allowed)) {
                $filename   = 'profile_' . $id . '_' . time() . '.' . $ext;
                $dest       = __DIR__ . '/../public/uploads/' . $filename;
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dest)) {
                    $profilePic = 'public/uploads/' . $filename;
                }
            }
        }

        $this->userModel->updateProfile($id, [
            'name'        => $name,
            'phone'       => $phone,
            'bio'         => $bio,
            'profile_pic' => $profilePic,
        ]);

        // Password change
        if (!empty($_POST['new_password'])) {
            if (!password_verify($_POST['current_password'], $seller['password_hash'])) {
                header('Location: index.php?page=profile&error=wrong_password');
                exit;
            }
            if (strlen($_POST['new_password']) < 6) {
                header('Location: index.php?page=profile&error=short_password');
                exit;
            }
            $this->userModel->updatePassword($id, password_hash($_POST['new_password'], PASSWORD_DEFAULT));
        }

        $_SESSION['name'] = $name;
        header('Location: index.php?page=profile&success=updated');
        exit;
    }

    // ─── Listings ─────────────────────────────────────────────────
    public function myListings(): void {
        $status   = $_GET['status'] ?? 'all';
        $listings = $this->listingModel->getBySellerAndStatus($this->sellerId(), $status);
        require __DIR__ . '/../views/seller/listings.php';
    }

    public function createListing(): void {
        $this->requireVerified();
        $categories = $this->categoryModel->getAll();
        $templates  = $this->templateModel->getBySeller($this->sellerId());
        $prefill    = null;
        require __DIR__ . '/../views/seller/create_listing.php';
    }

    public function storeListing(): void {
        $this->requireVerified();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=create_listing');
            exit;
        }

        $errors = $this->validateListingInput($_POST);
        if ($errors) {
            $_SESSION['listing_errors'] = $errors;
            $_SESSION['listing_old']    = $_POST;
            header('Location: index.php?page=create_listing');
            exit;
        }

        $listingId = $this->listingModel->create([
            'seller_id'     => $this->sellerId(),
            'category_id'   => (int)$_POST['category_id'],
            'title'         => trim($_POST['title']),
            'description'   => trim($_POST['description']),
            'condition'     => $_POST['condition'],
            'starting_price'=> (float)$_POST['starting_price'],
            'reserve_price' => !empty($_POST['reserve_price']) ? (float)$_POST['reserve_price'] : null,
            'end_datetime'  => $_POST['end_datetime'],
        ]);

        // Handle images (up to 5)
        if (!empty($_FILES['images']['name'][0])) {
            $order = 1;
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($order > 5) break;
                if (!$tmp) continue;
                $ext  = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
                $name = 'listing_' . $listingId . '_' . $order . '_' . time() . '.' . $ext;
                $dest = __DIR__ . '/../public/uploads/listings/' . $name;
                if (move_uploaded_file($tmp, $dest)) {
                    $this->listingModel->addImage($listingId, 'public/uploads/listings/' . $name, $order);
                    $order++;
                }
            }
        }

        // Save as template?
        if (!empty($_POST['save_as_template'])) {
            $this->templateModel->create([
                'seller_id'     => $this->sellerId(),
                'title'         => trim($_POST['title']),
                'description'   => trim($_POST['description']),
                'category_id'   => (int)$_POST['category_id'],
                'condition'     => $_POST['condition'],
                'starting_price'=> (float)$_POST['starting_price'],
            ]);
        }

        header('Location: index.php?page=listings&success=created');
        exit;
    }

    public function editListing(): void {
        $this->requireVerified();
        $id      = (int)($_GET['id'] ?? 0);
        $listing = $this->listingModel->getById($id);

        if (!$listing || $listing['seller_id'] != $this->sellerId()) {
            header('Location: index.php?page=listings&error=not_found');
            exit;
        }
        if ($this->listingModel->hasBids($id)) {
            header('Location: index.php?page=listings&error=has_bids');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        require __DIR__ . '/../views/seller/edit_listing.php';
    }

    public function updateListing(): void {
        $this->requireVerified();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=listings');
            exit;
        }

        $id      = (int)($_POST['listing_id'] ?? 0);
        $listing = $this->listingModel->getById($id);

        if (!$listing || $listing['seller_id'] != $this->sellerId()) {
            header('Location: index.php?page=listings&error=not_found');
            exit;
        }
        if ($this->listingModel->hasBids($id)) {
            header('Location: index.php?page=listings&error=has_bids');
            exit;
        }

        $errors = $this->validateListingInput($_POST);
        if ($errors) {
            $_SESSION['listing_errors'] = $errors;
            header('Location: index.php?page=edit_listing&id=' . $id);
            exit;
        }

        $this->listingModel->update($id, [
            'seller_id'     => $this->sellerId(),
            'category_id'   => (int)$_POST['category_id'],
            'title'         => trim($_POST['title']),
            'description'   => trim($_POST['description']),
            'condition'     => $_POST['condition'],
            'starting_price'=> (float)$_POST['starting_price'],
            'reserve_price' => !empty($_POST['reserve_price']) ? (float)$_POST['reserve_price'] : null,
            'end_datetime'  => $_POST['end_datetime'],
        ]);

        header('Location: index.php?page=listings&success=updated');
        exit;
    }

    public function cancelListing(): void {
        $id = (int)($_GET['id'] ?? 0);
        if (!$this->listingModel->cancel($id, $this->sellerId())) {
            header('Location: index.php?page=listings&error=cancel_failed');
            exit;
        }
        header('Location: index.php?page=listings&success=cancelled');
        exit;
    }

    public function relistItem(): void {
        $this->requireVerified();
        $id      = (int)($_GET['id'] ?? 0);
        $listing = $this->listingModel->getById($id);

        if (!$listing || $listing['seller_id'] != $this->sellerId()) {
            header('Location: index.php?page=listings');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $prefill    = $listing;
        require __DIR__ . '/../views/seller/create_listing.php';
    }

    // ─── Templates ────────────────────────────────────────────────
    public function templates(): void {
        $templates = $this->templateModel->getBySeller($this->sellerId());
        require __DIR__ . '/../views/seller/templates.php';
    }

    public function saveTemplate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=templates');
            exit;
        }
        $this->templateModel->create([
            'seller_id'     => $this->sellerId(),
            'title'         => trim($_POST['title'] ?? ''),
            'description'   => trim($_POST['description'] ?? ''),
            'category_id'   => (int)($_POST['category_id'] ?? 0),
            'condition'     => $_POST['condition'] ?? 'good',
            'starting_price'=> (float)($_POST['starting_price'] ?? 0),
        ]);
        header('Location: index.php?page=templates&success=saved');
        exit;
    }

    public function deleteTemplate(): void {
        $id = (int)($_GET['id'] ?? 0);
        $this->templateModel->delete($id, $this->sellerId());
        header('Location: index.php?page=templates&success=deleted');
        exit;
    }

    public function createFromTemplate(): void {
        $this->requireVerified();
        $id      = (int)($_GET['id'] ?? 0);
        $tpl     = $this->templateModel->getById($id, $this->sellerId());
        if (!$tpl) {
            header('Location: index.php?page=create_listing');
            exit;
        }
        $categories = $this->categoryModel->getAll();
        $templates  = $this->templateModel->getBySeller($this->sellerId());
        $prefill    = $tpl;
        require __DIR__ . '/../views/seller/create_listing.php';
    }

    // ─── AJAX: Live Bid Activity ───────────────────────────────────
    public function bidActivity(): void {
        header('Content-Type: application/json');
        $id      = (int)($_GET['id'] ?? 0);
        $listing = $this->listingModel->getById($id);

        if (!$listing || $listing['seller_id'] != $this->sellerId()) {
            echo json_encode(['error' => 'Not found']);
            exit;
        }

        $bids = $this->listingModel->getBidHistory($id);
        echo json_encode([
            'current_bid' => $listing['current_bid'],
            'bid_count'   => $listing['bid_count'],
            'bids'        => $bids,
        ]);
        exit;
    }

    // ─── Ended Auctions ───────────────────────────────────────────
    public function endedAuctions(): void {
        $listings = $this->listingModel->getBySellerAndStatus($this->sellerId(), 'ended');
        $winners  = [];
        foreach ($listings as $l) {
            $winners[$l['id']] = $this->listingModel->getWinnerDetails($l['id']);
        }
        require __DIR__ . '/../views/seller/ended_auctions.php';
    }

    // ─── Analytics ────────────────────────────────────────────────
    public function analytics(): void {
        $data = $this->listingModel->getAnalytics($this->sellerId());
        require __DIR__ . '/../views/seller/analytics.php';
    }

    // ─── Reviews ──────────────────────────────────────────────────
    public function reviews(): void {
        $reviews = $this->userModel->getReviews($this->sellerId());
        require __DIR__ . '/../views/seller/reviews.php';
    }

    public function respondReview(): void {
        // Reviews table doesn't have a response column in the schema
        // We mark it as a future feature; for now redirect back
        header('Location: index.php?page=reviews&info=feature_coming');
        exit;
    }

    // ─── Helpers ─────────────────────────────────────────────────
    private function validateListingInput(array $data): array {
        $errors = [];
        if (empty(trim($data['title'] ?? '')))       $errors[] = 'Title is required.';
        if (empty(trim($data['description'] ?? ''))) $errors[] = 'Description is required.';
        if (empty($data['category_id']))             $errors[] = 'Category is required.';
        if (!in_array($data['condition'] ?? '', ['new','like_new','good','fair'])) {
            $errors[] = 'Valid condition required.';
        }
        $sp = (float)($data['starting_price'] ?? 0);
        if ($sp <= 0) $errors[] = 'Starting price must be greater than 0.';

        $endDt = strtotime($data['end_datetime'] ?? '');
        if (!$endDt || $endDt < time() + 3600) {
            $errors[] = 'End date/time must be at least 1 hour in the future.';
        }
        return $errors;
    }
}
