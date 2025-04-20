<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'config.php'; // Include the database connection

// Check if the user is logged in as staff
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // Redirect to the staff login page if not logged in
    header("Location: staff-login.php");
    exit();
}

// Initialize variables
$whereClauses = [];
if (!empty($_GET['from_month']) && !empty($_GET['to_month'])) {
    $from_month = intval($_GET['from_month']);
    $to_month = intval($_GET['to_month']);
    $whereClauses[] = "MONTH(billing_date) BETWEEN $from_month AND $to_month";
}
if (!empty($_GET['year'])) {
    $year = intval($_GET['year']);
    $whereClauses[] = "YEAR(billing_date) = $year";
}
if (!empty($_GET['client_profile'])) {
    $client_profile = $conn->real_escape_string($_GET['client_profile']);
    if ($client_profile == 'OTHERS') {
        $whereClauses[] = "client_profile NOT IN ('STUDENT', 'MSME')";
    } else {
        $whereClauses[] = "client_profile = '$client_profile'";
    }
}
if (!empty($_GET['equipment'])) {
    $equipment = $conn->real_escape_string($_GET['equipment']);
    $whereClauses[] = "FIND_IN_SET('$equipment', equipment) > 0";
}
if (!empty($_GET['search_name'])) {
    $search_name = $conn->real_escape_string($_GET['search_name']);
    $whereClauses[] = "client_name LIKE '%$search_name%'";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Fetch billing records
$result = $conn->query("SELECT * FROM billing $whereClause ORDER BY id DESC");
$ovaTotal = 0;
$totalsByProfile = ['STUDENT' => 0, 'MSME' => 0, 'OTHERS' => 0];
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $ovaTotal += $row['total_invoice'];
    if (isset($totalsByProfile[$row['client_profile']])) {
        $totalsByProfile[$row['client_profile']] += $row['total_invoice'];
    } else {
        $totalsByProfile['OTHERS'] += $row['total_invoice'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment and Release Records</title>
    <link rel="icon" href="fablab.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <div class="container">
        <h1>Payment and Release</h1>
        <!-- The Modal -->
        <div id="billingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Billing Form</h2>
                <form id="billingForm" enctype="multipart/form-data" action="add-billing-handler.php" method="POST">
                    <div>
                        <label>Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>
                    <div>
                        <label>Billing Date:</label>
                        <input type="date" name="billing_date" required>
                    </div>
                    <div>
                        <label>Client Profile:</label><br>
                        <input type="radio" name="client_profile" value="STUDENT" required>STUDENT<br>
                        <input type="radio" name="client_profile" value="MSME" required>MSME<br>
                        <input type="radio" name="client_profile" value="OTHERS" required>OTHERS (Specify):
                        <input type="text" name="client_profile_other">
                    </div>
                    <div>
                        <label>Equipment Used:</label><br>
                        <input type="checkbox" name="equipment[]" value="3D Printer"> 3D Printer<br>
                        <input type="checkbox" name="equipment[]" value="3D Scanner"> 3D Scanner<br>
                        <input type="checkbox" name="equipment[]" value="Laser Cutting Machine"> Laser Cutting Machine<br>
                        <input type="checkbox" name="equipment[]" value="Print and Cut Machine"> Print and Cut Machine<br>
                        <input type="checkbox" name="equipment[]" value="CNC MachineB"> CNC Machine(Big)<br>
                        <input type="checkbox" name="equipment[]" value="CNC MachineS"> CNC Machine(Small)<br>
                        <input type="checkbox" name="equipment[]" value="Vinyl Cutter"> Vinyl Cutter<br>
                        <input type="checkbox" name="equipment[]" value="Embroidery Machine1"> Embroidery Machine(One Head)<br>
                        <input type="checkbox" name="equipment[]" value="Embroidery Machine4"> Embroidery Machine(Four Heads)<br>
                        <input type="checkbox" name="equipment[]" value="Flatbed Cutter"> Flatbed Cutter<br>
                        <input type="checkbox" name="equipment[]" value="Vacuum Forming"> Vacuum Forming<br>
                        <input type="checkbox" name="equipment[]" value="Water Jet Machine"> Water Jet Machine<br>
                    </div>
                    <div>
                        <label>Total Invoice:</label>
                        <input type="number" name="total_invoice" step="0.01" required>
                    </div>
                    <div>
                        <label>Attach PDF:</label>
                        <input type="file" name="billing_pdf" accept="application/pdf">
                    </div>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>

        <div class="dashboard">
            <div class="chart-container">
                <canvas id="profileChart"></canvas>
            </div>
            <div class="totals-card">
                <h3>Total Revenue</h3>
                <div class="total-amount">&#8369;<?php echo number_format($ovaTotal, 2); ?></div>
                <h4>By Client Profile</h4>
                <div class="profile-total">
                    <strong>STUDENT:</strong> &#8369;<?php echo number_format($totalsByProfile['STUDENT'], 2); ?>
                </div>
                <div class="profile-total">
                    <strong>MSME:</strong> &#8369;<?php echo number_format($totalsByProfile['MSME'], 2); ?>
                </div>
                <div class="profile-total">
                    <strong>OTHERS:</strong> &#8369;<?php echo number_format($totalsByProfile['OTHERS'], 2); ?>
                </div>
            </div>
        </div>

        <h2>Filter Payment and Release Records</h2>
        <div class="filter-section">
            <form method="GET" class="filter-grid">
                <div>
                    <label>From Month:</label>
                    <select name="from_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'" . (isset($_GET['from_month']) && $_GET['from_month'] == $m ? ' selected' : '') . ">$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>To Month:</label>
                    <select name="to_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'" . (isset($_GET['to_month']) && $_GET['to_month'] == $m ? ' selected' : '') . ">$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>Year:</label>
                    <select name="year">
                        <option value="">All</option>
                        <?php for ($y = date('Y'); $y >= 2016; $y--) {
                            echo "<option value='$y'" . (isset($_GET['year']) && $_GET['year'] == $y ? ' selected' : '') . ">$y</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>Client Profile:</label>
                    <select name="client_profile">
                        <option value="">All</option>
                        <option value="STUDENT" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'STUDENT' ? 'selected' : '' ?>>STUDENT</option>
                        <option value="MSME" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'MSME' ? 'selected' : '' ?>>MSME</option>
                        <option value="OTHERS" <?= isset($_GET['client_profile']) && $_GET['client_profile'] == 'OTHERS' ? 'selected' : '' ?>>OTHERS</option>
                    </select>
                </div>
                <div>
                    <label>Equipment:</label>
                    <select name="equipment">
                        <option value="">All</option>
                        <option value="3D Printer" <?= isset($_GET['equipment']) && $_GET['equipment'] == '3D Printer' ? 'selected' : '' ?>>3D Printer</option>
                        <option value="3D Scanner" <?= isset($_GET['equipment']) && $_GET['equipment'] == '3D Scanner' ? 'selected' : '' ?>>3D Scanner</option>
                        <option value="Laser Cutting Machine" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Laser Cutting Machine' ? 'selected' : '' ?>>Laser Cutting Machine</option>
                        <option value="Print and Cut Machine" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Print and Cut Machine' ? 'selected' : '' ?>>Print and Cut Machine</option>
                        <option value="CNC Machine (Big)" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'CNC Machine (Big)' ? 'selected' : '' ?>>CNC Machine (Big)</option>
                        <option value="CNC Machine (Small)" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'CNC Machine (Small)' ? 'selected' : '' ?>>CNC Machine (Small)</option>
                        <option value="Vinyl Cutter" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Vinyl Cutter' ? 'selected' : '' ?>>Vinyl Cutter</option>
                        <option value="Embroidery Machine (One Head)" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Embroidery Machine (One Head)' ? 'selected' : '' ?>>Embroidery Machine (One Head)</option>
                        <option value="Embroidery Machine (Four Heads)" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Embroidery Machine (Four Heads)' ? 'selected' : '' ?>>Embroidery Machine (Four Heads)</option>
                        <option value="Flatbed Cutter" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Flatbed Cutter' ? 'selected' : '' ?>>Flatbed Cutter</option>
                        <option value="Vacuum Forming" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Vacuum Forming' ? 'selected' : '' ?>>Vacuum Forming</option>
                        <option value="Water Jet Machine" <?= isset($_GET['equipment']) && $_GET['equipment'] == 'Water Jet Machine' ? 'selected' : '' ?>>Water Jet Machine</option>
                    </select>
                </div>
                <div>
                    <!-- <label for=""></label> -->
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <div class="search-section">
            <h2>Search Client Name</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search_name" placeholder="Enter client name" value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '' ?>" style="flex-grow: 1;">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="billing-table">
            <h2>Payment and Release Records</h2>
            <button id="openFormBtn">Add New Payment and Release</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Client</th>
                    <th>Equipment</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($rows as $row) {
                    $formattedDate = date("F d, Y", strtotime($row['billing_date']));
                    echo "<tr data-id='{$row['id']}'>";
                    echo "<td class='profile-cell'>" . htmlspecialchars(trim($row['client_profile'])) . "</td>"; // Trim whitespace
                    echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['equipment']) . "</td>";
                    echo "<td>&#8369;" . number_format($row['total_invoice'], 2) . "</td>";
                    echo "<td>$formattedDate</td>";
                    echo "<td>";
                    if (!empty($row['billing_pdf'])) {
                        echo "<a href='uploads/billing/" . htmlspecialchars($row['billing_pdf']) . "' class='pdf-link' target='_blank'>View PDF</a>";
                    } else {
                        echo "<span class='no-pdf'>None</span>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById("billingModal");
        const btn = document.getElementById("openFormBtn");
        const span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Chart.js configuration
        const ctx = document.getElementById('profileChart').getContext('2d');
        const profileChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['STUDENT', 'MSME', 'OTHERS'],
                datasets: [{
                    data: [
                        <?php echo $totalsByProfile['STUDENT']; ?>,
                        <?php echo $totalsByProfile['MSME']; ?>,
                        <?php echo $totalsByProfile['OTHERS']; ?>
                    ],
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#e74c3c'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 10,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₱' + context.raw.toLocaleString('en-PH', {
                                    minimumFractionDigits: 2
                                });
                            }
                        },
                        bodyFont: {
                            size: 15
                        }
                    }
                },
                cutout: '45%'
            }
        });
    </script>
</body>

</html>