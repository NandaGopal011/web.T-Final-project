<?php
// FeaturedController - Handles featured listings actions
require_once __DIR__ . '/../models/FeaturedModel.php';

class FeaturedController {
    private $featured;

    public function __construct() {
        $this->featured = new FeaturedModel();
    }

    public function addFeaturedListing($listing_id) {
        return $this->featured->addFeaturedListing($listing_id, $_SESSION['user_id']);
    }

    public function removeFeaturedListing($listing_id) {
        return $this->featured->removeFeaturedListing($listing_id);
    }

    public function getFeaturedListings() {
        return $this->featured->getFeaturedListings();
    }
}
?>
