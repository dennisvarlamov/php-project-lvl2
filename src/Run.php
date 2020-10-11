<?php

namespace Differ\Run;

use Docopt;

use function Differ\Differ\genDiff;

function run()
{
    $doc = <<<DOC
Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>
    
    Options:
      -h --help                     Show this screen
      -v --version                  Show version
      --format <fmt>                Report format [default: pretty]
DOC;

    $argc = Docopt::handle($doc);

    $firstFilePath = realpath($argc['<firstFile>']);
    $secondFilePath = realpath($argc['<secondFile>']);

    try {
        echo genDiff($firstFilePath, $secondFilePath, $argc['--format']);
    } catch (\Exception $exseption) {
        echo  $exseption->getMessage();
    }
}
