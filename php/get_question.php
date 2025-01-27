<?php
header('Content-Type: application/json');

$jsonData = file_get_contents('../data/questions.json');
$questions = json_decode($jsonData, true);

if ($questions) {
    $randomIndex = array_rand($questions);
    $question = $questions[$randomIndex];
    echo json_encode($question);
} else {
    echo json_encode(["error" => "Unable to retrieve questions."]);
}
?>
