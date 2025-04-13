<?php
$conn = new mysqli('localhost', 'root', '', 'fablab_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all job requests from database
$sql = "SELECT * FROM job_requests ORDER BY request_date DESC";
$result = $conn->query($sql);
$jobRequests = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobRequests[] = $row;
    }
}

// Calculate totals for job requests
$totalRequests = count($jobRequests);

// Initialize status counts
$statusCounts = [
    'Pending' => 0,
    'In Progress' => 0,
    'Completed' => 0,
    'Cancelled' => 0
];

foreach ($jobRequests as $request) {
    if(isset($request['status']) && isset($statusCounts[$request['status']])) {
        $statusCounts[$request['status']]++;
    }
}

// For the chart data (used in JavaScript later)
$chartData = [
    'labels' => array_keys($statusCounts),
    'data' => array_values($statusCounts),
    'colors' => [
        'rgba(245, 158, 11, 0.8)', // Pending (warning)
        'rgba(37, 99, 235, 0.8)',   // In Progress (primary)
        'rgba(16, 185, 129, 0.8)',  // Completed (success)
        'rgba(239, 68, 68, 0.8)'    // Cancelled (danger)
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Request Records</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Font Import */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    /* CSS Reset and Base Styles */
    *, *::before, *::after {
        box-sizing: border-box;
    }

    :root {
        --color-primary: #2563eb;
        --color-primary-dark: #1d4ed8;
        --color-primary-light: #dbeafe;
        --color-success: #10b981;
        --color-success-light: #d1fae5;
        --color-warning: #f59e0b;
        --color-warning-light: #fef3c7;
        --color-danger: #ef4444;
        --color-danger-light: #fee2e2;
        --color-gray-50: #f9fafb;
        --color-gray-100: #f3f4f6;
        --color-gray-200: #e5e7eb;
        --color-gray-300: #d1d5db;
        --color-gray-400: #9ca3af;
        --color-gray-500: #6b7280;
        --color-gray-600: #4b5563;
        --color-gray-700: #374151;
        --color-gray-800: #1f2937;
        --color-gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius-sm: 0.125rem;
        --radius: 0.25rem;
        --radius-md: 0.375rem;
        --radius-lg: 0.5rem;
        --radius-xl: 0.75rem;
        --radius-2xl: 1rem;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        line-height: 1.6;
        color: var(--color-gray-800);
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Typography */
    h1, h2, h3, h4, h5, h6 {
        margin-top: 0;
        color: var(--color-gray-900);
        letter-spacing: -0.025em;
        line-height: 1.25;
    }

    h1 {
        font-size: 1.875rem; /* 30px */
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    h2 {
        font-size: 1.5rem; /* 24px */
        font-weight: 600;
        margin-bottom: 1rem;
    }

    h3 {
        font-size: 1.25rem; /* 20px */
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    h4 {
        font-size: 1.125rem; /* 18px */
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    /* Dashboard Section with gradient accents */
    .dashboard {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
        margin-bottom: 28px;
    }

    .chart-container {
        background: white;
        background-image: linear-gradient(to bottom right, #ffffff, #f0f7ff);
        padding: 20px;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        height: 300px;
        border: 1px solid var(--color-gray-200);
    }

    .totals-card {
        background: white;
        padding: 20px;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--color-gray-200);
        position: relative;
        overflow: hidden;
    }

    .totals-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(to right, var(--color-primary), var(--color-success));
    }

    .total-amount {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
        margin: 16px 0;
        display: flex;
        align-items: center;
    }

    .total-amount::before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 24px;
        background-color: var(--color-primary);
        margin-right: 12px;
        border-radius: var(--radius);
    }

    .profile-total {
        margin: 12px 0;
        padding: 10px 16px;
        border-radius: var(--radius);
        background-color: var(--color-gray-50);
        border-left: 3px solid var(--color-primary);
        font-size: 14px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .profile-total:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
    }

    .profile-total:nth-child(4) {
        border-left-color: var(--color-success);
    }

    .profile-total:nth-child(5) {
        border-left-color: var(--color-warning);
    }

    .profile-total:nth-child(6) {
        border-left-color: var(--color-danger);
    }

    /* Filter Section with improved spacing and visuals */
    .filter-section {
        background-color: white;
        padding: 24px;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        margin-bottom: 28px;
        border: 1px solid var(--color-gray-200);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .search-form {
        display: flex;
        gap: 16px;
        margin-top: 20px;
    }

    /* Form Elements with better accessibility */
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--color-gray-700);
        font-size: 14px;
    }

    select, 
    input[type="text"], 
    input[type="date"], 
    input[type="number"],
    textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--color-gray-300);
        border-radius: var(--radius);
        font-size: 14px;
        box-sizing: border-box;
        transition: all 0.2s ease;
        color: var(--color-gray-800);
        background-color: white;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        padding-right: 40px;
    }

    select:focus, 
    input[type="text"]:focus, 
    input[type="date"]:focus, 
    input[type="number"]:focus,
    textarea:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Improve focus visibility for accessibility */
    :focus-visible {
        outline: 2px solid var(--color-primary);
        outline-offset: 2px;
    }

    button {
        background-color: var(--color-primary);
        color: white;
        border: none;
        padding: 10px 18px;
        font-size: 14px;
        border-radius: var(--radius);
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    button:hover {
        background-color: var(--color-primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    button:active {
        transform: translateY(0);
        box-shadow: var(--shadow-sm);
    }

    /* Table with more professional styling */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background-color: white;
        box-shadow: var(--shadow-md);
        border-radius: var(--radius-xl);
        overflow: hidden;
        margin-bottom: 28px;
        border: 1px solid var(--color-gray-200);
    }

    th, td {
        padding: 14px 18px;
        text-align: left;
    }

    th {
        background-color: var(--color-primary);
        color: white;
        font-weight: 500;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        position: sticky;
        top: 0;
    }

    th:first-child {
        border-top-left-radius: var(--radius-sm);
    }

    th:last-child {
        border-top-right-radius: var(--radius-sm);
    }

    td {
        border-bottom: 1px solid var(--color-gray-200);
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background-color: var(--color-gray-50);
    }

    /* Priority badges */
    .priority-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: var(--radius);
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .priority-high {
        background-color: var(--color-danger-light);
        color: var(--color-danger);
    }

    .priority-medium {
        background-color: var(--color-warning-light);
        color: var(--color-warning);
    }

    .priority-low {
        background-color: var(--color-success-light);
        color: var(--color-success);
    }

    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: var(--radius);
        font-size: 12px;
        font-weight: 500;
    }

    .status-pending {
        background-color: var(--color-warning-light);
        color: var(--color-warning);
    }

    .status-in-progress {
        background-color: var(--color-primary-light);
        color: var(--color-primary);
    }

    .status-completed {
        background-color: var(--color-success-light);
        color: var(--color-success);
    }

    .status-cancelled {
        background-color: var(--color-danger-light);
        color: var(--color-danger);
    }

    /* Links and interactive elements */
    a {
        color: var(--color-primary);
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    a:hover {
        color: var(--color-primary-dark);
        text-decoration: underline;
    }

    .file-link {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background-color: var(--color-primary-light);
        border-radius: var(--radius);
        color: var(--color-primary);
        font-weight: 500;
        border: 1px solid rgba(37, 99, 235, 0.2);
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .file-link::before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-right: 6px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'%3E%3C/path%3E%3Cpolyline points='14 2 14 8 20 8'%3E%3C/polyline%3E%3Cline x1='16' y1='13' x2='8' y2='13'%3E%3C/line%3E%3Cline x1='16' y1='17' x2='8' y2='17'%3E%3C/line%3E%3Cpolyline points='10 9 9 9 8 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
    }

    .file-link:hover {
        background-color: rgba(37, 99, 235, 0.15);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .no-file {
        color: var(--color-gray-400);
        font-style: italic;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
    }

    .no-file::before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-right: 6px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
    }

    /* Enhanced Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.25s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 28px;
        border: none;
        width: 80%;
        max-width: 650px;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        animation: slideIn 0.3s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    @keyframes slideIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .close {
        color: var(--color-gray-500);
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .close:hover {
        color: var(--color-gray-900);
        background-color: var(--color-gray-100);
    }

    /* Enhanced Form styling */
    #jobRequestForm div {
        margin-bottom: 20px;
    }

    #jobRequestForm input[type="radio"],
    #jobRequestForm input[type="checkbox"] {
        margin-right: 8px;
        width: 16px;
        height: 16px;
        accent-color: var(--color-primary);
    }

    #jobRequestForm input[type="file"] {
        padding: 12px;
        background-color: var(--color-gray-50);
        border: 2px dashed var(--color-gray-300);
        border-radius: var(--radius);
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
        width: 100%; 
    }

    #jobRequestForm input[type="file"]:hover {
        background-color: var(--color-primary-light);
        border-color: var(--color-primary);
    }

    #jobRequestForm button[type="submit"] {
        padding: 12px 24px;
        background-color: var(--color-success);
        color: white;
        font-weight: 500;
        margin-top: 12px;
        width: 100%;
        border-radius: var(--radius);
        font-size: 16px;
    }

    #jobRequestForm button[type="submit"]:hover {
        background-color: #0da76a;
    }

    /* Add New Job Request Button */
    #openFormBtn {
        margin-bottom: 20px;
        background-color: var(--color-success);
        float: right;
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: var(--radius);
    }

    #openFormBtn::before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-right: 8px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='12' y1='5' x2='12' y2='19'%3E%3C/line%3E%3Cline x1='5' y1='12' x2='19' y2='12'%3E%3C/line%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
    }

    #openFormBtn:hover {
        background-color: #0da76a;
    }

    /* Animations for chart load */
    #requestChart {
        opacity: 0;
        animation: fadeUp 0.6s ease forwards;
        animation-delay: 0.3s;
    }

    @keyframes fadeUp {
        from { 
            opacity: 0;
            transform: translateY(10px);
        }
        to { 
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form column layout */
    .form-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Improved Responsive Styles */
    @media (max-width: 992px) {
        .dashboard {
            grid-template-columns: 1fr;
        }
        
        .chart-container {
            height: 300px;
        }
        
        .container {
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }
        
        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .modal-content {
            width: 90%;
            margin: 10% auto;
            padding: 24px;
        }
        
        .chart-container {
            height: 260px;
        }
        
        h1 {
            font-size: 1.5rem;
        }
        
        h2 {
            font-size: 1.25rem;
        }
        
        table {
            font-size: 13px;
        }
        
        th, td {
            padding: 12px;
        }

        .form-columns {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
        
        .search-form {
            flex-direction: column;
        }
        
        .modal-content {
            padding: 20px;
            margin: 15% auto;
            width: 95%;
        }
        
        h1 {
            font-size: 1.25rem;
        }
        
        button {
            padding: 8px 16px;
        }
    }

    /* Print styles */
    @media print {
        body {
            font-size: 12pt;
            background: white;
        }
        
        .container {
            max-width: 100%;
            padding: 0;
        }
        
        .filter-section, .dashboard, #openFormBtn {
            display: none !important;
        }
        
        table {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        th {
            background-color: #eee !important;
            color: black !important;
        }
        
        .file-link, .no-file {
            display: none;
        }
    }
    .progress-bar {
        height: 4px;
        background-color: var(--color-primary);
        margin-top: 6px;
        border-radius: var(--radius-sm);
        transition: width 0.3s ease;
    }

    .profile-total {
        position: relative;
    }

    /* Color the progress bars by status */
    .profile-total:nth-child(2) .progress-bar { background-color: var(--color-warning); } /* Pending */
    .profile-total:nth-child(3) .progress-bar { background-color: var(--color-primary); } /* In Progress */
    .profile-total:nth-child(4) .progress-bar { background-color: var(--color-success); } /* Completed */
    .profile-total:nth-child(5) .progress-bar { background-color: var(--color-danger); } /* Cancelled */

    /* Utility classes */
    .text-success { color: var(--color-success); }
    .text-warning { color: var(--color-warning); }
    .text-danger { color: var(--color-danger); }
    .text-primary { color: var(--color-primary); }

    .bg-success-light { background-color: var(--color-success-light); }
    .bg-warning-light { background-color: var(--color-warning-light); }
    .bg-danger-light { background-color: var(--color-danger-light); }
    .bg-primary-light { background-color: var(--color-primary-light); }

    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    .font-normal { font-weight: 400; }

    .text-sm { font-size: 12px; }
    .text-md { font-size: 14px; }
    .text-lg { font-size: 16px; }
    .text-xl { font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Job Request Records</h1>
        <!-- The Modal -->
        <div id="jobRequestModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Job Request Form</h2>
                <form id="jobRequestForm" enctype="multipart/form-data">
                    <div class="form-columns">
                        <div>
                            <label>Request Title:</label>
                            <input type="text" name="request_title" required>
                        </div>
                        
                        <div>
                            <label>Request Date:</label>
                            <input type="date" name="request_date" required>
                        </div>
                    </div>

                    <div class="form-columns">
                        <div>
                            <label>Client Name:</label>
                            <input type="text" name="client_name" required>
                        </div>
                        
                        <div>
                            <label>Contact Number:</label>
                            <input type="text" name="contact_number" required>
                        </div>
                    </div>

                    <div>
                        <label>Client Profile:</label><br>
                        <input type="radio" name="client_profile" value="STUDENT" required> STUDENT<br>
                        <input type="radio" name="client_profile" value="MSME" required> MSME<br>
                        <input type="radio" name="client_profile" value="OTHERS" required> OTHERS (Specify):
                        <input type="text" name="client_profile_other">
                    </div>

                    <div>
                        <label>Request Description:</label>
                        <textarea name="request_description" placeholder="Provide detailed information about the job request..." required></textarea>
                    </div>

                    <div>
                        <label>Equipment Needed:</label><br>
                        <input type="checkbox" name="equipment[]" value="3D Printer"> 3D Printer<br>
                        <input type="checkbox" name="equipment[]" value="3D Scanner"> 3D Scanner<br>
                        <input type="checkbox" name="equipment[]" value="Laser Cutting Machine"> Laser Cutting Machine<br>
                        <input type="checkbox" name="equipment[]" value="Print and Cut Machine"> Print and Cut Machine<br>
                        <input type="checkbox" name="equipment[]" value="CNC MachineB"> CNC Machine(Big)<br>
                        <input type="checkbox" name="equipment[]" value="CNC MachineS"> CNC Machine(Small)<br>
                        <input type="checkbox" name="equipment[]" value="Vinly Cutter"> Vinly Cutter<br>
                        <input type="checkbox" name="equipment[]" value="Embriodert Machine1"> Embriodert Machine(One Head)<br>
                        <input type="checkbox" name="equipment[]" value="Embriodert Machine4"> Embriodert Machine(Four Heads)<br>
                        <input type="checkbox" name="equipment[]" value="Faltbed Cutter"> Flatbed Cutter<br>
                        <input type="checkbox" name="equipment[]" value="Vacuum Forming"> Vacuum Forming<br>
                        <input type="checkbox" name="equipment[]" value="Water Jet Machine"> Water Jet Machine<br>
                    </div>

                    <div class="form-columns">
                        <div>
                            <label>Priority Level:</label><br>
                            <input type="radio" name="priority" value="Low" required> Low<br>
                            <input type="radio" name="priority" value="Medium" required> Medium<br>
                            <input type="radio" name="priority" value="High" required> High<br>
                        </div>
                        
                        <div>
                            <label>Estimated Completion Date:</label>
                            <input type="date" name="completion_date" required>
                        </div>
                    </div>

                    <div>
                        <label>Attach Reference Files:</label>
                        <input type="file" name="reference_file" accept=".pdf,.jpg,.jpeg,.png,.zip">
                        <p class="text-sm text-primary">Accepted formats: PDF, JPG, PNG, ZIP (Max: 10MB)</p>
                    </div>

                    <button type="submit">Submit Job Request</button>
                </form>
            </div>
        </div>
        
        <div class="dashboard">
</div>

        <h2>Filter Job Requests</h2>
        <div class="filter-section">
            <form method="GET" class="filter-grid">
                <div>
                    <label>From Month:</label>
                    <select name="from_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'>$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>To Month:</label>
                    <select name="to_month">
                        <option value="">All</option>
                        <?php for ($m = 1; $m <= 12; $m++) {
                            $monthName = date("F", mktime(0, 0, 0, $m, 1));
                            echo "<option value='$m'>$monthName</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>Year:</label>
                    <select name="year">
                        <option value="">All</option>
                        <?php for ($y = date('Y'); $y >= 2016; $y--) {
                            echo "<option value='$y'>$y</option>";
                        } ?>
                    </select>
                </div>
                <div>
                    <label>Client Profile:</label>
                    <select name="client_profile">
                        <option value="">All</option>
                        <option value="STUDENT">STUDENT</option>
                        <option value="MSME">MSME</option>
                        <option value="OTHERS">OTHERS</option>
                    </select>
                </div>
                <div>
                    <label>Status:</label>
                    <select name="status">
                        <option value="">All</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label>Priority:</label>
                    <select name="priority">
                        <option value="">All</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <button type="submit">Filter</button>
                </div>
            </form>
            <h2>Search Requests</h2>
            <form method="GET" class="search-form">
                <input type="text" name="search_term" placeholder="Search by title, client name, or description" style="flex-grow: 1;">
                <button type="submit">Search</button>
            </form>
        </div>

        <h2>Job Requests</h2>
        <button id="openFormBtn" style="margin-bottom: 15px; float: right;">Add New Request</button>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Profile</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Files</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($jobRequests as $request) {
                    $requestDate = date("F d, Y", strtotime($request['request_date']));
                    $completionDate = date("F d, Y", strtotime($request['completion_date']));
                    
                    // Determine priority badge class
                    $priorityClass = strtolower($request['priority']);
                    
                    // Determine status badge class
                    $statusClass = strtolower(str_replace(' ', '-', $request['status']));
                    
                    echo "<tr data-id='{$request['id']}'>";
                    echo "<td>{$request['request_title']}</td>";
                    echo "<td>{$request['client_name']}</td>";
                    echo "<td>{$request['client_profile']}</td>";
                    echo "<td><span class='priority-badge priority-{$priorityClass}'>{$request['priority']}</span></td>";
                    echo "<td><span class='status-badge status-{$statusClass}'>{$request['status']}</span></td>";
                    echo "<td>{$requestDate}</td>";
                    echo "<td>";
                    if (!empty($request['reference_file'])) {
                        echo "<a href='requests/{$request['reference_file']}' class='file-link' target='_blank'>View Files</a>";
                    } else {
                        echo "<span class='no-file'>None</span>";
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
        const modal = document.getElementById("jobRequestModal");
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
        
        // AJAX form submission
        document.getElementById("jobRequestForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch("save_request.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new row to table
                    const table = document.querySelector("table tbody");
                    const newRow = document.createElement("tr");
                    
                    // Format date
                    const formattedDate = new Date(data.request_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    
                    // Create priority badge class
                    let priorityClass = '';
                    if (data.priority === 'High') priorityClass = 'priority-high';
                    else if (data.priority === 'Medium') priorityClass = 'priority-medium';
                    else if (data.priority === 'Low') priorityClass = 'priority-low';
                    
                    // Create status badge class
                    let statusClass = 'status-pending';
                    if (data.status === 'In Progress') statusClass = 'status-in-progress';
                    else if (data.status === 'Completed') statusClass = 'status-completed';
                    else if (data.status === 'Cancelled') statusClass = 'status-cancelled';
                    
                    // Create cells
                    newRow.innerHTML = `
                        <td>${data.request_title}</td>
                        <td>${data.client_name}</td>
                        <td>${data.client_profile}</td>
                        <td><span class="priority-badge ${priorityClass}">${data.priority}</span></td>
                        <td><span class="status-badge ${statusClass}">${data.status}</span></td>
                        <td>${formattedDate}</td>
                        <td>${data.reference_file ? `<a href="request/${data.reference_file}" class="file-link" target="_blank">View Files</a>` : '<span class="no-file">None</span>'}</td>
                    `;
                    
                    // Insert at the top of the table
                    table.insertBefore(newRow, table.firstChild);
                    
                    // Close modal and reset form
                    modal.style.display = "none";
                    this.reset();
                    alert("Job request submitted successfully!");
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while submitting the form.");
            });
        });
    </script>
</body>
</html>