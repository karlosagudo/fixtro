<?php
/**
 * Date: 5/7/17
 * Time: 12:04
 */

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;


use KarlosAgudo\Fixtro\CodeQualityTool\Checker\BehatChecker;

class BehatTest extends GeneralCheckerTestCase
{
    public function testFunctionalOk()
    {
        $this->parameters['confFile'] = __DIR__ . '/../CodeExamples/Behat/Ok/behat.yml';

        $filesToAnalyzer = [];
        $exit = $this->executeChecker($filesToAnalyzer, BehatChecker::class);

        self::assertEquals($exit, [[], []]);
    }

    // Long live Gödel, this test is going to fail.
    public function testFunctionalKo()
    {
        $this->parameters['confFile'] = __DIR__ . '/../CodeExamples/Behat/Ko/behat.yml';

        $filesToAnalyzer = [];
        $exit = $this->executeChecker($filesToAnalyzer, BehatChecker::class);

        $exit = $this->glueExit($exit);
        $hasFailures = strpos($exit, 'Failed scenario') !== false;

        self::assertTrue($hasFailures);
    }
}