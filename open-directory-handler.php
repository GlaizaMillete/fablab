<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $path = $data['path'];

    if (!empty($path) && is_dir($path)) {
        // Use shell_exec to open the directory in the file explorer
        $escapedPath = escapeshellarg($path);
        shell_exec("explorer $escapedPath");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid directory path']);
    }
    exit();
}
