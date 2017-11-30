<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\CodeExamples\PsAlm;

class Bad
{
    /**
     * @param string $hello
     */
    public function helloWorld(int $hello) : bool
    {
        echo $hello;
    }
}