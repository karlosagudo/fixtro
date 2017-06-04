<?php


namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;


use KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpStanChecker;

class PhpStanCheckerTest extends GeneralCheckerTestCase
{
    public function testFunctionalOk()
    {
        $filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpStan/Good.php'];
        $exit = $this->executeChecker($filesToAnalyzer, PhpStanChecker::class);

        self::assertEquals($exit, [[], []]);
    }

    public function testFunctionalKo()
    {
        $filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpStan/Bad.php'];
        $exit = $this->executeChecker($filesToAnalyzer, PhpStanChecker::class);

        self::assertContains('Bad.php', $exit[0][1]);
    }
}