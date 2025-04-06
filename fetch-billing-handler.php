<?php
// Fetch billing records sorted by billing_date in ascending order
$result = $conn->query("SELECT * FROM billing ORDER BY billing_date ASC");
$billingRows = [];
while ($row = $result->fetch_assoc()) {
    $billingRows[] = $row;
}
?>