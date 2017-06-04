<?php


namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\CodeExamples\PhpStan;


class Bad
{
    /**
     * @var string $test
     * @return string
     */
    public function echoWorld(integer $test): string
    {
        echo 'Here';
    }
}