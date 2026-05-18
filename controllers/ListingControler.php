<?php
// ListingController - Handles listing management actions
require_once __DIR__ . '/../models/ListingModel.php';

class ListingController {
    private $listing;

    public function __construct() {
        $this->listing = new ListingModel();
    }

    public function getAllListings($status = null, $category_id = null, $limit = 50, $offset = 0) {
        return $this->listing->getAllListings($status, $category_id, $limit, $offset);
    }

    public function cancelListing($listing_id, $reason) {
        return $this->listing->cancelListing($listing_id, $reason);
    }
}
?>
