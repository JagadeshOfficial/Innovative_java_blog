<?php
header("Content-Type: application/json");

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Receive and decode the incoming JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'] ?? 'print("Hello, world!")';  // Sample code if code is empty
$language = $data['language'] ?? 'python3';
$testCases = $data['testCases'] ?? ['Hello, world!'];  // Default test case

$clientId = '8bc929e0dd9f5635f821ebfcacfaa6d5';
$clientSecret = 'd03ac6ac7efa1c3284b6e8ca91ac579d48abaca8d831beada5c6c980dd9a6651';
$outputs = [];

// Process each test case
foreach ($testCases as $testCase) {
    // Prepare the payload for JDoodle API
    $payload = json_encode([
        "script" => $code,
        "language" => $language,
        "versionIndex" => "0",
        "stdin" => $testCase,
        "clientId" => $clientId,
        "clientSecret" => $clientSecret
    ]);

    // Initialize cURL for API call
    $ch = curl_init("https://api.jdoodle.com/v1/execute");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Execute the cURL request and check for errors
    $response = curl_exec($ch);
    if ($response === false) {
        $outputs[] = "Error in execution: Unable to reach JDoodle API. cURL error: " . curl_error($ch);
    } else {
        $responseObj = json_decode($response, true);
        if (isset($responseObj['error'])) {
            $outputs[] = "Error in execution: " . $responseObj['error'];
        } else {
            $output = trim($responseObj['output']);
            $outputs[] = "Input: $testCase\nOutput: $output";
        }
    }
    curl_close($ch);
}

// Send all outputs back to the frontend as JSON
echo json_encode(["output" => implode("\n\n", $outputs)]);
?>
