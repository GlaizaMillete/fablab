<?php
// Fetch billing records
$result = $conn->query("SELECT * FROM billing ORDER BY id DESC");
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}
?>