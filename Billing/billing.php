<?php
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billing Form</title>
</head>
<body>
    <h2>Billing Form</h2>
    <form action="save_billing.php" method="POST" enctype="multipart/form-data">
        <label>Client Name:</label>
        <input type="text" name="client_name" required>
        
        <label>Billing Date:</label>
        <input type="date" name="billing_date" required><br>

        <label>Client Profile:</label><br>
        <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
        <input type="radio" name="client_profile" value="MSME" required> MSME<br>
        <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
        <input type="text" name="client_profile_other"><br>

        <label>Equipment Used:</label><br>
        <input type="checkbox" name="equipment[]" value="3D Printer"> 3D Printer<br>
        <input type="checkbox" name="equipment[]" value="3D Scanner"> 3D Scanner<br>
        <input type="checkbox" name="equipment[]" value="Laser Cutting Machine"> Laser Cutting Machiner<br>
        <input type="checkbox" name="equipment[]" value="Print and Cut Machine"> Print and Cut Machine<br>
        <input type="checkbox" name="equipment[]" value="CNC MachineB"> CNC Machine(Big)<br>
        <input type="checkbox" name="equipment[]" value="CNC MachineS"> CNC Machine(Small)<br>
        <input type="checkbox" name="equipment[]" value="Vinly Cutter"> Vinly Cutter<br>
        <input type="checkbox" name="equipment[]" value="Embriodert Machine1"> Embriodert Machine(One Head)<br>
        <input type="checkbox" name="equipment[]" value="Embriodert Machine4"> Embriodert Machine(Four Heads)<br>
        <input type="checkbox" name="equipment[]" value="Faltbed Cutter"> Flatbed Cutter<br>
        <input type="checkbox" name="equipment[]" value="Vacuum Forming"> Vacuum Forming<br>
        <input type="checkbox" name="equipment[]" value="Water Jet Machine"> Water Jet Machine<br>
        <br>

        <label>Total Invoice:</label>
        <input type="number" name="total_invoice" step="0.01" required><br>

        <label>Attach PDF:</label>
        <input type="file" name="billing_pdf" accept="application/pdf"><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>