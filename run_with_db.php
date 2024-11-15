<?php
header("Content-Type: application/json");
require 'db_config.php';

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'];
$language = $data['language'];
$problemId = $data['problemId'];

$clientId = 'YOUR_JDOODLE_CLIENT_ID';
$clientSecret = 'YOUR_JDOODLE_CLIENT_SECRET';
$outputs = [];

// Fetch test cases from database
$query = $conn->prepare("SELECT input, expected_output FROM test_cases WHERE problem_id = ?");
$query->bind_param("i", $problemId);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $input = $row['input'];
    $expectedOutput = trim($row['expected_output']);

    $payload = json_encode([
        "script" => $code,
        "language" => $language,
        "versionIndex" => "0",
        "stdin" => $input,
        "clientId" => $clientId,
        "clientSecret" => $clientSecret
    ]);

    $ch = curl_init("https://api.jdoodle.com/v1/execute");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseObj = json_decode($response, true);
    $output = isset($responseObj['output']) ? trim($responseObj['output']) : "Error in execution";

    $isPassed = $output === $expectedOutput;
    $outputs[] = [
        "input" => $input,
        "output" => $output,
        "expected_output" => $expectedOutput,
        "passed" => $isPassed
    ];
}

echo json_encode($outputs);
?>
