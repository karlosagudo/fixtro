<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\ComposerChecker;

class ComposerCheckerTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = ['composer.lock', 'composer.json'];
		$exit = $this->executeChecker($filesToAnalyzer, ComposerChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = ['composer.json'];
		$exit = $this->executeChecker($filesToAnalyzer, ComposerChecker::class);

		self::assertContains('composer.lock must be committed', $exit[1][0]);
	}
}
