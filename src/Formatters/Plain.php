<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;
use function Funct\Collection\compact;
use function Funct\String\strip;

function formatToPlain(array $str): string
{
    $formatedPlain = formatElementToPlain($str);
    $result = compact(flattenAll($formatedPlain));
    
    return implode("\n", $result);
}

function formatElementToPlain(array $str, string $path = ''): array
{
    $formatPlain = array_map(function ($value) use ($path) {

        $path = $path === '' ? "{$value['name']}" : "{$path} . {$value['name']}";

        switch ($value['status']) {
            case 'nested':
                $result = convertToPlain($value['children'], "{$path}");
                break;
            case 'changed':
                $oldValue = getValueMap($value['old']);
                $newValue = getValueMap($value['new']);
                $result = "Property '{$path}' was changed. From '{$oldValue}' to '{$newValue}'";
                break;
            case 'unchanged':
                $result = "";
                break;
            case 'added':
                $element = getValueMap($value);
                $result = "Property '{$path}' was added with value: '{$element}'";
                break;
            case 'deleted':
                $result = "Property '{$path}' was removed";
                break;
            default:
                return null;
        }
        return $result;
    }, $str);
    return $formatPlain;
}

function getValueMap($value): string
{
    return (is_object($value) || is_array($value))
        ? 'complex value' : strip(json_encode($value), '"');
}
