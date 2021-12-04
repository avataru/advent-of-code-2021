<?php

$numbers = [];
$boards = [];

// Parse the input file and split the data into the array of drawn numbers and the boards
$input = file_get_contents('4-input.txt');
$boards = explode("\n\n", $input);
$numbers = explode(',', array_shift($boards));

// Parse boards, extract all possible lines
array_walk($boards, function(&$board) {

    // Convert the board to an array of numbers so it's easier to manipulate and use later
    $numbers = explode(',', preg_replace('/\s+/', ',', trim($board)));

    // Get all the horizontal lines
    $horizontalLines = array_chunk($numbers, 5);

    // Get all the vertical lines by taking every nth number on each horizontal line
    $verticalLines = [];
    for ($i = 0; $i < 5; $i++) {
        $verticalLine = [];
        foreach ($horizontalLines as $horizontalLine) {
            array_push($verticalLine, $horizontalLine[$i]);
        }
        array_push($verticalLines, $verticalLine);
    }

    // Wrap it all up as an array
    $board = [
        'board' => $numbers,
        'lines' => array_merge($horizontalLines, $verticalLines),
    ];
});

// Let's play BINGO!
for ($n = 0; $n < count($numbers); $n++) {
    // Draw a number by building an array of all drawn numbers so far
    $drawnNumbers = array_slice($numbers, 0, $n + 1);

    // Check each board if it's a winner by looking at each possible lines
    foreach ($boards as $board) {
        foreach ($board['lines'] as $line) {
            if (count(array_intersect($drawnNumbers, $line)) === 5) {
                // Looks like all the numbers on this line are drawn so we have a winner and we can calculate the score
                $boardScoringSum = array_sum($board['board']) - array_sum(array_intersect($drawnNumbers, $board['board']));

                // Output the final answer
                echo $boardScoringSum * end($drawnNumbers);
                exit;
            }
        }
    }
}
