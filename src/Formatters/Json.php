<?php

namespace Differ\Formatters\Json;

function formatToJson(array $str): string
{
    $formatJson = json_encode($str);
    
    if ($formatJson == false) {
        throw new \Exception('json encode error');
    }
    return $formatJson;
}
