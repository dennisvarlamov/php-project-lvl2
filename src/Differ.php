<?php

namespace Differ\Differ;

use function Differ\Builder\generate;
use function Differ\Formatters\Json\formatToJson;
use function Differ\Formatters\Plain\formatToPlain;
use function Differ\Formatters\Pretty\formatToPretty;
use function Differ\Parser\parse;

function genDiff(string $firstFilePath, string $secondFilePath, string $format): string
{
    if (!$firstFilePath || !$secondFilePath) {
        throw new \Exception('Missing one of the files');
    }

    $firstFileFormat = pathinfo($firstFilePath, PATHINFO_EXTENSION);
    $secondFileFormat = pathinfo($secondFilePath, PATHINFO_EXTENSION);

    $firstFileData = parse(file_get_contents($firstFilePath), $firstFileFormat);
    $secondFileData = parse(file_get_contents($secondFilePath), $secondFileFormat);
    //print_r($firstFileData);
    print_r($secondFileData);
    $diffGen = generate($firstFileData, $secondFileData);
    switch ($format) {
        case 'json':
            $renderedDiff = formatToJson($diffGen);
            break;
        case 'plain':
            $renderedDiff = formatToPlain($diffGen);
        default:
            $renderedDiff = formatToPretty($diffGen);
    }
    return $renderedDiff;
}
