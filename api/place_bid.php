<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])) {
    die("Login required");
}

$listing_id = $_POST['listing_id'];
$amount = $_POST['amount'];
$buyer_id = $_SESSION['user_id'];

$get = $conn->prepare(
    "SELECT current_bid FROM listings WHERE id=?"
);

$get->bind_param("i", $listing_id);
$get->execute();

$data = $get->get_result()->fetch_assoc();

if($amount > $data['current_bid']) {

    $stmt = $conn->prepare(
        "INSERT INTO bids(listing_id,buyer_id,amount)
         VALUES(?,?,?)"
    );

    $stmt->bind_param(
        "iid",
        $listing_id,
        $buyer_id,
        $amount
    );

    $stmt->execute();

    $update = $conn->prepare(
        "UPDATE listings SET current_bid=? WHERE id=?"
    );

    $update->bind_param("di", $amount, $listing_id);
    $update->execute();

    echo "Bid Placed Successfully";
}
else {
    echo "Bid must be higher than current bid";
}
?>
