<?php

namespace Differ\Builder;

use function Funct\Collection\union;

function generate(array $firstFileData, array $secondFileData): array
{
    $keys = array_values(union(array_keys($firstFileData), array_keys($secondFileData)));

    $result = array_map(function ($key) use ($firstFileData, $secondFileData) {

        if (!array_key_exists($key, $firstFileData)) {
            return ['name' => $key, 'value' => $secondFileData[$key], 'status' => 'added'];
        } if (!array_key_exists($key, $secondFileData)) {
            return ['name' => $key, 'value' => $firstFileData[$key], 'status' => 'deleted'];
        } if (is_array($firstFileData[$key]) && is_array($secondFileData[$key])) {
            return [
                'name' => $key,
                'children' => generate($firstFileData[$key], $secondFileData[$key]),
                'status' => 'nested'
            ];
        } if ($firstFileData[$key] !== $secondFileData[$key]) {
            return [
                'name' => $key, 'old' => $firstFileData[$key],
                'new' => $secondFileData[$key], 'status' => 'changed'
            ];
        } if ($firstFileData[$key] === $secondFileData[$key]) {
            return ['name' => $key, 'element' => $firstFileData[$key], 'status' => 'unchanged'];
        }
    }, $keys);

    return $result;
}
