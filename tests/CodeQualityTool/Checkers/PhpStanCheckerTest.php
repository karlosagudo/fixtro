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
        if (isset($exit[1][0])) {
            self::assertContains('Bad.php', $exit[1][0]);
            return true;
        }
        self::assertContains('Bad.php', $exit[0][1]);
    }
}