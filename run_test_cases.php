<?php
// Sample array of test cases
$testCases = [
    ['input' => '2 3', 'expected' => '5'],
    ['input' => '3 7', 'expected' => '10']
];

// Loop through each test case and execute
foreach ($testCases as $testCase) {
    $input = $testCase['input'];
    $expected = $testCase['expected'];

    // Some logic, maybe calling your function, using the $input and $expected
    // Example: run your function here, e.g., `sum($input);`
    $output = runTest($input);  // Assuming 'runTest()' is a function you want to call

    // Check if the output matches the expected result
    if ($output == $expected) {
        echo "Test passed\n";
    } else {
        echo "Test failed: Expected $expected, got $output\n";
    }
}

// Sample function to run the test (Replace with your actual logic)
function runTest($input) {
    // Assuming a simple sum operation for the test
    $numbers = explode(' ', $input);  // Split input into numbers
    return array_sum($numbers);  // Return the sum
}
?>
