<?php

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

require __DIR__ . '/vendor/autoload.php';

$config = include __DIR__ . '/config.php';
$date = new DateTime();

try {
    runChecks($config, $date);
    echo sprintf('Provisioning Advent of Code %s, day %d...', $date->format('Y'), $date->format('d')) . "\n";

    if (createFolder($date)) {
        echo sprintf('Folder %s created', getFolder($date)) . "\n";
    }

    $client = getClient($config);

    if (addSample($client, $date)) {
        echo 'Sample file added' . "\n";
    }

    if (addInput($client, $date)) {
        echo 'Input file added' . "\n";
    }

    if (updateReadme($date)) {
        echo 'Readme updated' . "\n";
    }

    echo 'Done!';
} catch (Exception $e) {
    echo $e->getMessage();
}

function runChecks(array $config, DateTime $date)
{
    if ($date->format('m') < 12) {
        throw new Exception(sprintf('Advent of Code %s has not started yet, come back later', $date->format('Y')));
        exit;
    } elseif ($date->format('j') > 24) {
        throw new Exception(sprintf('Advent of Code %s has finished, come back next year', $date->format('Y')));
        exit;
    } elseif (empty($config['session'])) {
        throw new Exception('Please set the session cookie value in config.php');
        exit;
    }
}

function createFolder(DateTime $date): bool
{
    $folder = getFolder($date);
    if (file_exists($folder)) {
        return false;
    }

    mkdir($folder);
    copy(__DIR__ . '/solution.php.template', $folder . '/solution.php');

    return true;
}

function getFolder(DateTime $date): string
{
    return sprintf('%s/../Day %d', __DIR__, $date->format('d'));
}

function getClient(array $config): Client
{
    $url = 'https://adventofcode.com';
    $client = new Client([
        'base_uri' => $url,
        'verify' => false,
        'cookies' => CookieJar::fromArray([
            'session' => $config['session'],
        ], parse_url($url, PHP_URL_HOST)),
    ]);

    return $client;
}

function addInput(Client $client, DateTime $date): bool
{
    $file = getFolder($date) . '/input.txt';
    if (file_exists($file)) {
        return false;
    }

    $input = $client->get(sprintf('%d/day/%d/input', $date->format('Y'), $date->format('d')))->getBody();
    $size = file_put_contents($file, $input);

    if ($size === false || strlen($input) !== $size) {
        throw new Exception('Error writing input file');
    }

    return true;
}

function addSample(Client $client, DateTime $date): bool
{
    $file = getFolder($date) . '/sample.txt';
    if (file_exists($file)) {
        return false;
    }

    $puzzle = $client->get(sprintf('%d/day/%d', $date->format('Y'), $date->format('d')))->getBody();
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHtml($puzzle);
    $xpath = new DOMXPath($dom);
    $sample = $xpath->query('//pre/code[not(*)][1]')->item(0)->nodeValue;
    $size = file_put_contents($file, $sample);

    if ($size === false || strlen($sample) !== $size) {
        throw new Exception('Error writing sample file');
    }

    return true;
}

function updateReadme(DateTime $date): bool
{
    $readme = file_get_contents(__DIR__ . '/../README.md');

    $day = $date->format('j');
    $daysBadge = sprintf('![Days Completed](https://img.shields.io/badge/days%%20completed-%d-red?style=for-the-badge)', $day);
    $starsBadge = sprintf('![Stars](https://img.shields.io/badge/stars%%20⭐-%d-yellow?style=for-the-badge)', 2 * $day);

    $newReadme = preg_replace('/!\[Days Completed.+?\)/', $daysBadge, $readme);
    $newReadme = preg_replace('/!\[Stars.+?\)/', $starsBadge, $newReadme);

    if ($newReadme === $readme) {
        return false;
    }

    file_put_contents(__DIR__ . '/../README.md', $newReadme);

    return true;
}
