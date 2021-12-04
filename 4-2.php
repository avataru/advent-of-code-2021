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
$lastWinningBoardScore = 0;
for ($n = 0; $n < count($numbers); $n++) {
    // We'll filter out winning boards so we don't have to check them once they won
    $remainingBoards = [];
    // Draw a number by building an array of all drawn numbers so far
    $drawnNumbers = array_slice($numbers, 0, $n + 1);

    foreach ($boards as $board) {
        $boardWon = false;

        foreach ($board['lines'] as $line) {
            // Once we found a winning board we can keep the score and continue with the next board
            if (count(array_intersect($drawnNumbers, $line)) === 5) {
                $boardWon = true;
                $boardScoringSum = array_sum($board['board']) - array_sum(array_intersect($drawnNumbers, $board['board']));
                $lastWinningBoardScore = $boardScoringSum * end($drawnNumbers);
                break;
            }
        }

        if (!$boardWon) {
            array_push($remainingBoards, $board);
        }
    }

    $boards = $remainingBoards;
}

// Once all the numbers have been drawn and all the boards have been checked we can output the last winning board score
echo $lastWinningBoardScore;
