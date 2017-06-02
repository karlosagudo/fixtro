<?php

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\Checkers;

use karlosagudo\Fixtro\CodeQualityTool\Checker\EsLintChecker;
use Symfony\Component\Console\Output\ConsoleOutput;

class EsLintCheckerTest extends GeneralCheckerTestCase
{
    public function setUp()
    {
        $testLinter = new EsLintChecker([], new ConsoleOutput(), []);
        if (!$testLinter->findBinaryEsLint()) {
            self::markTestSkipped('Please install EsLint: http://eslint.org/docs/user-guide/getting-started');
        }
    }

	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/JS/GoodJs.js'];
		$exit = $this->executeChecker($filesToAnalyzer, EsLintChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalOkVerbose()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/JS/GoodJs.js'];
		$exit = $this->executeChecker($filesToAnalyzer, EsLintChecker::class, true);

		self::assertContains('<info>Executed :</info><options=bold>', $exit[0][0]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/JS/BadJs.js'];
		$exit = $this->executeChecker($filesToAnalyzer, EsLintChecker::class);
		$info = $exit[0][0];
		$error = $exit[1];

		self::assertContains('2 problems (2 errors, 0 warnings)', $info);
	}
}
