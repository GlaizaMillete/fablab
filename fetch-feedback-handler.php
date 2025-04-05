<?php
// Fetch feedback data
$sql = "SELECT id, client_name, feedback_pdf, feedback_date FROM feedback";
$result = $conn->query($sql);

?>