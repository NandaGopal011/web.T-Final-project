<?php
// CommissionController - Handles commission and financial report actions
require_once __DIR__ . '/../models/CommissionModel.php';

class CommissionController {
    private $commission;

    public function __construct() {
        $this->commission = new CommissionModel();
    }

    public function setDefaultCommissionRate($rate) {
        return $this->commission->setDefaultCommissionRate($rate);
    }

    public function setSellerCommissionRate($seller_id, $rate) {
        return $this->commission->setSellerCommissionRate($seller_id, $rate);
    }

    public function getFinancialReport($period = 'month') {
        return $this->commission->getFinancialReport($period);
    }

    public function getTopSellersByRevenue($limit = 10) {
        return $this->commission->getTopSellersByRevenue($limit);
    }

    public function getTopCategoriesByCommission($limit = 10) {
        return $this->commission->getTopCategoriesByCommission($limit);
    }
}
?>
