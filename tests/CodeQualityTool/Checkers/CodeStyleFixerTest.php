<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\CodeStyleFixer;

class CodeStyleFixerTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/CodeStylePhp/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, CodeStyleFixer::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/CodeStylePhp/Bad.php'];
		$this->parameters = [
			'extraParams' => '--dry-run',
			'afterParams' => ' --diff',
		];

		$exit = $this->executeChecker($filesToAnalyzer, CodeStyleFixer::class, true);

		$hasErrorDetected = strpos($exit[0][2],'/Bad.php') !== false;

		self::assertTrue($hasErrorDetected);
	}
}
