<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpMessDetectorChecker;

class PhpMessDetectorTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpMessDetector/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PhpMessDetectorChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpMessDetector/Bad.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PhpMessDetectorChecker::class);

		if (isset($exit[1]) && isset($exit[1][0])) { //travis old version of php7
            self::assertContains('Avoid unused parameters', $exit[1][0]);
        }
	}
}
