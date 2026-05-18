<?php

// localhost/seller/check_columns.php
require_once 'config/database.php';
$db = getDB();

$tables = ['listings', 'users', 'bids', 'categories'];
foreach ($tables as $t) {
    echo "<h3>$t</h3><pre>";
    $r = $db->query("SHOW COLUMNS FROM $t");
    if ($r) {
        while ($row = $r->fetch_assoc()) {
            echo $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Table not found\n";
    }
    echo "</pre>";
}
