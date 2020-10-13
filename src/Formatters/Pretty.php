<?php

namespace Differ\Formatters\Pretty;

use function Funct\Strings\strip;

const DEFAULT_INDENT = 4;

function formatToPretty(array $str): string
{
    $renderedDiff = formatElementToPretty($str);
    return "{\n{$renderedDiff}\n}";
}

function formatElementToPretty(array $str, int $depth = 0): string
{
    $indent = str_repeat(" ", DEFAULT_INDENT * $depth);
    
    $renderDiff = array_map(function ($value) use ($indent, $depth) {
        
        switch ($value['status']) {
            case 'nested':
                $element = formatElementToPretty($value['children'], $depth + 1);
                $currentElement = "{$indent}    {$value['name']}: {\n{$element}\n    {$indent}}";
                break;
            case 'changed':
                $oldValue = getValueMap($value['old'], $depth);
                $newValue = getValueMap($value['new'], $depth);
                $oldRaw = "{$indent}  + {$value['name']}: {$newValue}";
                $newRaw = "{$indent}  - {$value['name']}: {$oldValue}";
                $currentElement = "{$oldRaw}\n{$newRaw}";
                break;
            case 'unchanged':
                $oldValue = getValueMap($value, $depth);
                $currentElement =  "{$indent}{$value}: {$oldValue}";
                break;
            case 'deleted':
                $element = getValueMap($value['element'], $depth);
                $currentElement = "{$indent}  - {$value['name']}: {$element}";
                break;
            case 'added':
                $element = getValueMap($value['element'], $depth);
                $currentElement = " {$indent} + {$value['name']}: {$element}";
                break;
            default:
                $element = getValueMap($value['element'], $depth);
                $currentElement = "{$indent}    {$value['name']}: {$element}";
        }
        return $currentElement;
    }, $str);

    $result = implode("\n", $renderDiff);
    return "{$result}";
}

function getValueMap($value, $depth)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    }
    switch (gettype($value)) {
        case 'array':
            return getArrayMap($value, $depth);
        default:
            return strip(json_encode($value), '"');
    }
}

function getArrayMap($value, int $depth = 0): string
{
    $keys = array_keys($value);
    $indent = str_repeat(" ", DEFAULT_INDENT * ($depth + 1));

    $renderedArray = array_reduce($keys, function ($acc, $key) use ($keys, $value, $indent) {
        $acc[] = "{$indent}    {$key}: {$value[$key]}";
        return $acc;
    }, []);

    $result = implode("\n", $renderedArray);

    return "{\n{$result}\n{$indent}}";
}
