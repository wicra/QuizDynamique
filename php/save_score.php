<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = isset($_POST['score']) ? intval($_POST['score']) : 0;
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : 'Unknown';

    $data = [
        'username' => $username,
        'score' => $score,
        'date' => date('Y-m-d H:i:s')
    ];

    $file = '../data/scores.json';
    $scores = json_decode(file_get_contents($file), true) ?: [];
    $scores[] = $data;

    file_put_contents($file, json_encode($scores));
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
