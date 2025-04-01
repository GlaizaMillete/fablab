<?php
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records</title>
</head>
<body>
<h2>Filter Billing Records</h2>
    <form method="GET">
        <label>From Month:</label>
        <select name="from_month">
            <option value="">All</option>
            <?php for ($m = 1; $m <= 12; $m++) {
                $monthName = date("F", mktime(0, 0, 0, $m, 1));
                echo "<option value='$m'>$monthName</option>";
            } ?>
        </select>
        <label>To Month:</label>
        <select name="to_month">
            <option value="">All</option>
            <?php for ($m = 1; $m <= 12; $m++) {
                $monthName = date("F", mktime(0, 0, 0, $m, 1));
                echo "<option value='$m'>$monthName</option>";
            } ?>
        </select>
        <label>Year:</label>
        <select name="year">
            <option value="">All</option>
            <?php for ($y = date('Y'); $y >= 2016; $y--) {
                echo "<option value='$y'>$y</option>";
            } ?>
        </select>
        <label>Client Profile:</label>
        <select name="client_profile">
            <option value="">All</option>
            <option value="STUDENT">STUDENT</option>
            <option value="MSME">MSME</option>
            <option value="OTHERS">OTHERS</option>
        </select>
        <label>Equipment Used:</label>
        <select name="equipment">
            <option value="">All</option>
            <option value="3D Printer">3D Printer</option>
            <option value="3D Scanner"> 3D Scanner</option>
            <option value="Laser Cutting Machine"> Laser Cutting Machine</option>
            <option value="Print and Cut Machine"> Print and Cut Machine</option>
            <option value="CNC MachineB"> CNC Machine(Big)</option>
            <option value="CNC MachineS"> CNC Machine(Small)</option>
            <option value="Vinly Cutter"> Vinly Cutter</option>
            <option value="Embriodert Machine1"> Embriodert Machine(One Head)</option>
            <option value="Embriodert Machine4"> Embriodert Machine(Four Heads)</option>
            <option value="Faltbed Cutter"> Faltbed Cutter</option>
            <option value="Vacuum Forming"> Vacuum Forming</option>
            <option value="Water Jet Machine"> Water Jet Machine</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    <h2>Search Client Name</h2>
    <form method="GET">
        <input type="text" name="search_name" placeholder="Enter client name">
        <button type="submit">Search</button>
    </form>
<h2>Billing Records</h2>
    <table border="1">
        <tr>
            <th>Client Profile</th>
            <th>Client Name</th>
            <th>Equipment Used</th>
            <th>Total Invoice</th>
            <th>Date</th>
            <th>PDF</th>
        </tr>
        <?php
        $whereClauses = [];
        if (!empty($_GET['from_month']) && !empty($_GET['to_month'])) {
            $from_month = $_GET['from_month'];
            $to_month = $_GET['to_month'];
            $whereClauses[] = "MONTH(billing_date) BETWEEN $from_month AND $to_month";
        }
        if (!empty($_GET['year'])) {
            $year = $_GET['year'];
            $whereClauses[] = "YEAR(billing_date) = $year";
        }
        if (!empty($_GET['client_profile'])) {
            $client_profile = $_GET['client_profile'];
            if ($client_profile == 'OTHERS') {
                $whereClauses[] = "client_profile NOT IN ('STUDENT', 'MSME')";
            } else {
                $whereClauses[] = "client_profile = '$client_profile'";
            }
        }
        if (!empty($_GET['equipment'])) {
            $equipment = $_GET['equipment'];
            $whereClauses[] = "FIND_IN_SET('$equipment', equipment) > 0";
        }
        if (!empty($_GET['search_name'])) {
            $search_name = $conn->real_escape_string($_GET['search_name']);
            $whereClauses[] = "client_name LIKE '%$search_name%'";
        }
        
        $whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

        $result = $conn->query("SELECT * FROM billing $whereClause ORDER BY id DESC");
        $ovaTotal = 0;
        $totalsByProfile = ['STUDENT' => 0, 'MSME' => 0, 'OTHERS' => 0];
        while ($row = $result->fetch_assoc()) {
            $formattedDate = date("F d, Y", strtotime($row['billing_date']));
            echo "<tr><td>{$row['client_profile']}</td><td>{$row['client_name']}</td><td>{$row['equipment']}</td><td>&#8369;" . number_format($row['total_invoice'], 2) . "</td><td>$formattedDate</td></tr>";
            $ovaTotal += $row['total_invoice'];
            if (isset($totalsByProfile[$row['client_profile']])) {
                $totalsByProfile[$row['client_profile']] += $row['total_invoice'];
            } else {
                $totalsByProfile['OTHERS'] += $row['total_invoice'];
            }
            if (!empty($row['billing_pdf'])) {
                echo "<td><a href='/Billing/uploads/{$row['billing_pdf']}' target='_blank'>View PDF</a></td></tr>";
            } else {
                echo "<td>No PDF</td></tr>";
            }
        }
        ?>
    </table>
    <h3>Overall Total: &#8369;<?php echo number_format($ovaTotal, 2); ?></h3>
    <h3>Total per Client Profile:</h3>
    <ul>
        <li>STUDENT: &#8369;<?php echo number_format($totalsByProfile['STUDENT'], 2); ?></li>
        <li>MSME: &#8369;<?php echo number_format($totalsByProfile['MSME'], 2); ?></li>
        <li>OTHERS: &#8369;<?php echo number_format($totalsByProfile['OTHERS'], 2); ?></li>
    </ul>
</body>
</html>