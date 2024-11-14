<?php
// Capture the POST request
$data = json_decode(file_get_contents('php://input'), true);
$code = $data['code'];
$language = $data['language'];

// Create a temporary file for the user code with correct file extension based on language
if ($language === 'python') {
    $tempFileName = 'user_code.py';
} elseif ($language === 'javascript') {
    $tempFileName = 'user_code.js';
} elseif ($language === 'java') {
    $tempFileName = 'UserCode.java';
} elseif ($language === 'cpp') {
    $tempFileName = 'UserCode.cpp';
} else {
    echo json_encode(['output' => 'Language not supported']);
    exit;
}

// Write the code to the file
file_put_contents($tempFileName, $code);

// Execute the code depending on the language
switch ($language) {
    case 'python':
        $command = "python3 $tempFileName"; // Correct command for Python
        break;
    case 'javascript':
        $command = "node $tempFileName"; // Correct command for JavaScript
        break;
    case 'java':
        $javaFileName = 'UserCode.java';
        file_put_contents($javaFileName, $code);
        $command = "javac $javaFileName && java UserCode"; // Correct command for Java
        break;
    case 'cpp':
        $cppFileName = 'UserCode.cpp';
        file_put_contents($cppFileName, $code);
        $command = "g++ $cppFileName -o UserCode && ./UserCode"; // Correct command for C++
        break;
    default:
        echo json_encode(['output' => 'Language not supported']);
        exit;
}

// Execute the code and capture the output
$output = shell_exec($command);

// Check for errors
$error = shell_exec("echo $?"); // Check for errors in the execution
if ($error) {
    echo json_encode(['output' => "Error: $error"]);
} else {
    echo json_encode(['output' => $output]);
}

// Clean up the temporary file
unlink($tempFileName);
if (isset($javaFileName)) unlink($javaFileName);
if (isset($cppFileName)) unlink($cppFileName);
?>
