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
if (!empty($_GET['client_profile']) && is_array($_GET['client_profile'])) {
    $client_profiles = array_map([$conn, 'real_escape_string'], $_GET['client_profile']);
    if (in_array('OTHERS', $client_profiles)) {
        $client_profiles = array_diff($client_profiles, ['OTHERS']);
        $profileConditions = [];
        if (!empty($client_profiles)) {
            $profileList = "'" . implode("','", $client_profiles) . "'";
            $profileConditions[] = "client_profile IN ($profileList)";
        }
        $profileConditions[] = "client_profile NOT IN ('STUDENT', 'MSME')";
        $whereClauses[] = '(' . implode(' OR ', $profileConditions) . ')';
    } else {
        $profileList = "'" . implode("','", $client_profiles) . "'";
        $whereClauses[] = "client_profile IN ($profileList)";
    }
}
if (!empty($_GET['prepared_by'])) {
    $prepared_by = $conn->real_escape_string(trim($_GET['prepared_by']));
    $whereClauses[] = "LOWER(prepared_by) LIKE LOWER('%$prepared_by%')";
}
// if (!empty($_GET['approved_by'])) {
//     $approved_by = $conn->real_escape_string(trim($_GET['approved_by']));
//     $whereClauses[] = "LOWER(approved_by) LIKE LOWER('%$approved_by%')";
// }
// if (!empty($_GET['payment_received_by'])) {
//     $payment_received_by = $conn->real_escape_string(trim($_GET['payment_received_by']));
//     $whereClauses[] = "LOWER(payment_received_by) LIKE LOWER('%$payment_received_by%')";
// }
// if (!empty($_GET['receipt_acknowledged_by'])) {
//     $receipt_acknowledged_by = $conn->real_escape_string(trim($_GET['receipt_acknowledged_by']));
//     $whereClauses[] = "LOWER(receipt_acknowledged_by) LIKE LOWER('%$receipt_acknowledged_by%')";
// }
if (!empty($_GET['search_name'])) {
    $search_name = $conn->real_escape_string(trim($_GET['search_name']));
    $whereClauses[] = "LOWER(client_name) LIKE LOWER('%$search_name%')";
}

$whereClause = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
$query = "SELECT * FROM billing $whereClause ORDER BY no DESC"; // Changed 'id' to 'no'
$result = $conn->query($query);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

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
    <!-- <link rel="stylesheet" href="style3.css"> -->
</head>

<body>
    <div class="back-button">
        <a href="staff-home.php">&larr; Back</a>
    </div>
    <div class="container">
        <h1>Payment and Release</h1>
        <!-- The Modal -->
        <div id="billingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="billingModalTitle">Payment and Releasing Form</h2>
                <form id="billingForm" enctype="multipart/form-data" action="add-billing-handler.php" method="POST">

                    <input type="hidden" name="billing_id" id="billingIdField">

                    <!-- Section 1: Personal Information -->
                    <h3>Personal Information</h3>
                    <div>
                        <label>No:</label>
                        <input type="number" id="noField" name="no" readonly>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="date" required>
                    </div>
                    <div>
                        <label>Client Name:</label>
                        <input type="text" name="client_name" required>
                    </div>
                    <div>
                        <label>Address:</label>
                        <input type="text" name="address" required>
                    </div>
                    <div>
                        <label>Contact No:</label>
                        <input type="number" name="contact_no" required>
                    </div>
                    <div>
                        <label>Client Profile:</label>
                        <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
                        <input type="radio" name="client_profile" value="MSME" required> MSME<br>
                        <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
                        <input type="text" name="client_profile_other">
                    </div>
                    <div>
                        <label>Description of the Project:</label>
                        <textarea class="description" name="description" required></textarea>
                    </div>

                    <!-- Section 2: Details of the Service to be Rendered -->
                    <h3>Details of the Service to be Rendered</h3>
                    <table id="serviceTable">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Total Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="service_name[]" required></td>
                                <td><input type="text" name="unit[]" required></td>
                                <td><input type="text" name="rate[]" required></td>
                                <td><input type="number" name="total_cost[]" step="0.01" required class="cost-input"></td>
                                <td><button type="button" class="removeRowBtn">Remove</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" id="addRowBtn">Add Row</button>
                    <div>
                        <label>Total:</label>
                        <input type="number" id="totalCost" name="total" step="0.01" readonly>
                    </div>

                    <!-- Completion Information -->
                    <!-- <div>
                        <label>Completion Date:</label>
                        <input type="date" name="completion_date" required>
                    </div> -->
                    <div>
                        <label>Prepared By:</label>
                        <input type="text" name="prepared_by" value="<?= $_SESSION['staff_name'] ?>" readonly>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="prepared_date" value="<?= date('Y-m-d') ?>" readonly>
                    </div>

                    <!-- Section 3: Order Payment -->
                    <!-- <h3>3. Order Payment</h3>
                    <div>
                        <label>Please issue an Official Receipt in favor of:</label>
                        <input type="text" name="or_favor" required>
                    </div>
                    <div>
                        <label>For the amount of:</label>
                        <input type="number" id="totalAmount" name="or_amount" step="0.01" readonly>
                    </div>
                    <div>
                        <label>Approved by:</label>
                        <input type="text" name="approved_by" required>
                    </div> -->

                    <!-- Section 4: Payment -->
                    <!-- <h3>4. Payment</h3>
                    <div>
                        <label>OR No:</label>
                        <input type="number" name="or_no" required>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="payment_date" required>
                    </div>
                    <div>
                        <label>Payment Received by:</label>
                        <input type="text" name="payment_received_by" required>
                    </div> -->

                    <!-- Section 5: Receipt of Completed Work -->
                    <!-- <h3>5. Receipt of Completed Work</h3>
                    <div>
                        <label>I, the client, acknowledge that I have received the above product.</label>
                        <input type="text" name="receipt_acknowledged_by" required>
                    </div>
                    <div>
                        <label>Date:</label>
                        <input type="date" name="receipt_date" required>
                    </div> -->

                    <!-- Section 6: Reference File -->
                    <h3>Reference File</h3>
                    <div>
                        <label>Upload Reference File (PDF, DOC, DOCX, JPG, PNG):</label>
                        <input type="file" name="billing_pdf" accept=".pdf,.doc,.docx,.jpg,.png" required>
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
                    <div style="display: flex; gap: 0.2rem;">
                        <input type="checkbox" name="client_profile[]" value="STUDENT" <?= isset($_GET['client_profile']) && in_array('STUDENT', (array)$_GET['client_profile']) ? 'checked' : '' ?>> STUDENT<br>
                        <input type="checkbox" name="client_profile[]" value="MSME" <?= isset($_GET['client_profile']) && in_array('MSME', (array)$_GET['client_profile']) ? 'checked' : '' ?>> MSME<br>
                        <input type="checkbox" name="client_profile[]" value="OTHERS" <?= isset($_GET['client_profile']) && in_array('OTHERS', (array)$_GET['client_profile']) ? 'checked' : '' ?>> OTHERS<br>
                    </div>
                </div>
                <div>
                    <label>Prepared By:</label>
                    <input type="text" name="prepared_by" value="<?= isset($_GET['prepared_by']) ? htmlspecialchars($_GET['prepared_by']) : '' ?>">
                </div>
                <div class="filter-div">
                    <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <h2>Search Client Name</h2>
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search_name" placeholder="Enter client name"
                    value="<?= isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : '' ?>"
                    style="flex-grow: 1;">
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
                    <th>Date</th>
                    <th>No.</th>
                    <th>Client</th>
                    <th>Service Description</th>
                    <th>Client Profile</th>
                    <th>Total Amount</th>
                    <th>Reference</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr data-id="<?= $row['no'] ?>">
                        <td><?= date("F d, Y", strtotime($row['billing_date'])) ?></td>
                        <td><?= htmlspecialchars($row['no']) ?></td>
                        <td><?= htmlspecialchars($row['client_name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars(trim($row['client_profile'])) ?></td>
                        <td>&#8369;<?= number_format($row['total_invoice'], 2) ?></td>
                        <td>
                            <?php if (!empty($row['billing_pdf'])): ?>
                                <a href="uploads/billing/<?= htmlspecialchars($row['billing_pdf']) ?>" class="ref-link" target="_blank">View PDF</a>
                            <?php else: ?>
                                <span class="no-pdf">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-container">
                            <button class="edit-btn" data-id="<?= $row['no'] ?>">Edit</button>
                            <button class="delete-btn" data-id="<?= $row['no'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById("billingModal");
        const btn = document.getElementById("openFormBtn");
        const span = document.getElementsByClassName("close")[0];
        const form = document.getElementById("billingForm");

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
            form.reset(); // Reset the form when the modal is closed
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                form.reset(); // Reset the form when clicking outside the modal
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

        // Add new row to the service table
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const table = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
        <td><input type="text" name="service_name[]" required></td>
        <td><input type="text" name="unit[]" required></td>
        <td><input type="text" name="rate[]" required></td>
        <td><input type="number" name="total_cost[]" step="0.01" required class="cost-input"></td>
        <td><button type="button" class="removeRowBtn">Remove</button></td>
    `;

            table.appendChild(newRow);

            // Add event listener for the remove button
            newRow.querySelector('.removeRowBtn').addEventListener('click', function() {
                newRow.remove();
                updateTotalCost();
            });

            // Add event listener for the cost input
            newRow.querySelector('.cost-input').addEventListener('input', updateTotalCost);
        });

        // Function to update the total cost dynamically
        function updateTotalCost() {
            const costInputs = document.querySelectorAll('.cost-input');
            let total = 0;

            costInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });

            // Update the total in both the Total field and the "For the amount of" section
            document.getElementById('totalCost').value = total.toFixed(2);
            document.getElementById('totalAmount').value = total.toFixed(2);
        }

        // Initial event listeners for existing rows
        document.querySelectorAll('.cost-input').forEach(input => {
            input.addEventListener('input', updateTotalCost);
        });
        document.querySelectorAll('.removeRowBtn').forEach(button => {
            button.addEventListener('click', function() {
                button.closest('tr').remove();
                updateTotalCost();
            });
        });

        // Fetch the next available 'no' value when the form is opened
        document.getElementById('openFormBtn').addEventListener('click', function() {

            // Clear the billing_id field to ensure it's treated as a new record
            document.getElementById('billingIdField').value = '';

            // Fetch the next available 'no' value
            fetch('fetch-billing-handler.php?action=get_next_no')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('noField').value = data.next_no; // Populate the 'no' field
                    } else {
                        alert('Error fetching the next number: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching the next number.');
                });

            // Clear service details table and add a default row
            const serviceTableBody = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
            serviceTableBody.innerHTML = ''; // Clear existing rows

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
<td><input type=\"text\" name=\"service_name[]\" required></td>
<td><input type=\"text\" name=\"unit[]\" required></td>
<td><input type=\"text\" name=\"rate[]\" required></td>
<td><input type=\"number\" name=\"total_cost[]\" step=\"0.01\" required class=\"cost-input\"></td>
<td><button type=\"button\" class=\"removeRowBtn\">Remove</button></td>
`;
            serviceTableBody.appendChild(newRow);

            // Add event listeners for the new row
            newRow.querySelector('.removeRowBtn').addEventListener('click', function() {
                newRow.remove();
                updateTotalCost();
            });
            newRow.querySelector('.cost-input').addEventListener('input', updateTotalCost);

            // Reset the form fields (excluding the service table which is handled above)
            const form = document.getElementById('billingForm');
            form.reset();

            // Set the modal title for adding a new record
            document.getElementById('billingModalTitle').innerText = 'Add New Record';

            // Show the modal
            modal.style.display = "block";
        });

        // Edit Button Click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id'); // Get the 'no' value from the data-id attribute
                fetch(`fetch-billing-handler.php?no=${id}`) // Use 'no' as the identifier
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const form = document.getElementById('billingForm');
                            form.reset();

                            // Populate form fields with fetched data
                            document.getElementById('billingIdField').value = data.billing.no; // Set the billing_id
                            document.getElementById('noField').value = data.billing.no || '';
                            document.querySelector('[name="date"]').value = data.billing.billing_date || '';
                            document.querySelector('[name="client_name"]').value = data.billing.client_name || '';
                            document.querySelector('[name="address"]').value = data.billing.address || '';
                            document.querySelector('[name="contact_no"]').value = data.billing.contact_no || '';

                            // Handle client profile radio buttons
                            const clientProfileRadio = document.querySelector(`[name="client_profile"][value="${data.billing.client_profile}"]`);
                            if (clientProfileRadio) {
                                clientProfileRadio.checked = true;
                                document.querySelector('[name="client_profile_other"]').value = ''; // Clear "OTHERS" field
                            } else {
                                // If no matching radio button, assume "OTHERS"
                                document.querySelector('[name="client_profile"][value="OTHERS"]').checked = true;
                                document.querySelector('[name="client_profile_other"]').value = data.billing.client_profile || '';
                            }

                            document.querySelector('[name="description"]').value = data.billing.description || '';
                            // document.querySelector('[name="completion_date"]').value = data.billing.completion_date || '';
                            document.querySelector('[name="prepared_by"]').value = data.billing.prepared_by || '';
                            document.querySelector('[name="prepared_date"]').value = data.billing.prepared_date || '';
                            // document.querySelector('[name="or_favor"]').value = data.billing.or_favor || '';
                            // document.querySelector('[name="or_amount"]').value = data.billing.or_amount || '';
                            // document.querySelector('[name="approved_by"]').value = data.billing.approved_by || '';
                            // document.querySelector('[name="or_no"]').value = data.billing.or_no || '';
                            // document.querySelector('[name="payment_date"]').value = data.billing.payment_date || '';
                            // document.querySelector('[name="payment_received_by"]').value = data.billing.payment_received_by || '';
                            // document.querySelector('[name="receipt_acknowledged_by"]').value = data.billing.receipt_acknowledged_by || '';
                            // document.querySelector('[name="receipt_date"]').value = data.billing.receipt_date || '';

                            // Update modal title
                            document.getElementById('billingModalTitle').innerText = 'Edit Record';

                            // Populate service details
                            const serviceTableBody = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
                            serviceTableBody.innerHTML = ''; // Clear existing rows
                            data.billing.services.forEach(service => {
                                const newRow = document.createElement('tr');
                                newRow.innerHTML = `
                            <td><input type="text" name="service_name[]" value="${service.service_name}" required></td>
                            <td><input type="text" name="unit[]" value="${service.unit}" required></td>
                            <td><input type="text" name="rate[]" value="${service.rate}" required></td>
                            <td><input type="number" name="total_cost[]" value="${service.total_cost}" step="0.01" required class="cost-input"></td>
                            <td><button type="button" class="removeRowBtn">Remove</button></td>
                        `;
                                serviceTableBody.appendChild(newRow);

                                // Add event listener for the remove button
                                newRow.querySelector('.removeRowBtn').addEventListener('click', function() {
                                    newRow.remove();
                                    updateTotalCost();
                                });

                                // Add event listener for the cost input
                                newRow.querySelector('.cost-input').addEventListener('input', updateTotalCost);
                            });

                            // Show the modal
                            document.getElementById('billingModal').style.display = 'block';
                        } else {
                            alert('Error fetching billing data: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Close Modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('billingModal').style.display = 'none';
        });

        // Delete Button Click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this record?')) {
                    fetch(`delete-billing-handler.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Record deleted successfully.');
                                location.reload();
                            } else {
                                alert('Error deleting record: ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
</body>

</html>