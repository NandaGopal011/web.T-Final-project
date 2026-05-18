<?php
// CommissionModel - Handles commission rate and financial report database operations
require_once __DIR__ . '/../config/Database.php';

class CommissionModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Set default commission rate for platform
    public function setDefaultCommissionRate($rate) {
        // Remove previous default
        $this->conn->query("UPDATE commission_rates SET is_default = FALSE WHERE is_default = TRUE");

        // Set new default
        $stmt = $this->conn->prepare("INSERT INTO commission_rates (commission_rate, is_default) VALUES (?, TRUE)
                                     ON DUPLICATE KEY UPDATE commission_rate = VALUES(commission_rate), is_default = TRUE");
        $stmt->bind_param("d", $rate);
        return $stmt->execute();
    }

    // Set commission rate for specific seller
    public function setSellerCommissionRate($seller_id, $rate) {
        $stmt = $this->conn->prepare("INSERT INTO commission_rates (seller_id, commission_rate, is_default) VALUES (?, ?, FALSE)
                                     ON DUPLICATE KEY UPDATE commission_rate = ?");
        $stmt->bind_param("idd", $seller_id, $rate, $rate);
        return $stmt->execute();
    }

    // Get financial report by period
    public function getFinancialReport($period = 'month') {
        if ($period == 'month') {
            $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as period,
                             SUM(commission_amount) as total_commission,
                             SUM(final_price) as total_sales
                      FROM platform_fees
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                      ORDER BY period DESC";
        } else if ($period == 'week') {
            $query = "SELECT DATE_FORMAT(created_at, '%Y-%u') as period,
                             SUM(commission_amount) as total_commission,
                             SUM(final_price) as total_sales
                      FROM platform_fees
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 WEEK)
                      GROUP BY DATE_FORMAT(created_at, '%Y-%u')
                      ORDER BY period DESC";
        } else {
            $query = "SELECT DATE(created_at) as period,
                             SUM(commission_amount) as total_commission,
                             SUM(final_price) as total_sales
                      FROM platform_fees
                      WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                      GROUP BY DATE(created_at)
                      ORDER BY period DESC";
        }

        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get top sellers by revenue
    public function getTopSellersByRevenue($limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT u.id, u.name, u.email, SUM(pf.final_price) as total_revenue, COUNT(pf.id) as total_sales
            FROM platform_fees pf
            JOIN users u ON pf.seller_id = u.id
            GROUP BY u.id
            ORDER BY total_revenue DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get top categories by commission
    public function getTopCategoriesByCommission($limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT c.id, c.name, SUM(pf.commission_amount) as total_commission, COUNT(pf.id) as total_auctions
            FROM platform_fees pf
            JOIN listings l ON pf.listing_id = l.id
            JOIN categories c ON l.category_id = c.id
            GROUP BY c.id
            ORDER BY total_commission DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all commission rates
    public function getCommissionRates() {
        $result = $this->conn->query("SELECT * FROM commission_rates ORDER BY is_default DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get default commission rate
    public function getDefaultCommissionRate() {
        $result = $this->conn->query("SELECT commission_rate FROM commission_rates WHERE is_default = TRUE LIMIT 1");
        $row = $result->fetch_assoc();
        return $row['commission_rate'] ?? 0;
    }
}
?>
