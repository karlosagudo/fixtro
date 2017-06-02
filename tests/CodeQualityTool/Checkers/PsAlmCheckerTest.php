<?php

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\Checkers;

use karlosagudo\Fixtro\CodeQualityTool\Checker\NameSpaceFixer;
use karlosagudo\Fixtro\CodeQualityTool\Checker\PsAlmChecker;
use Symfony\Component\Filesystem\Filesystem;

class PsAlmCheckerTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PsAlm/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PsAlmChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PsAlm/Bad.php'];

		$exit = $this->executeChecker($filesToAnalyzer, PsAlmChecker::class, true);

		self::assertContains('ERROR: InvalidDocblock', $exit[1][0]);
	}

	public function tearDown()
	{
		$fs = new Filesystem();
		$fs->copy(
			__DIR__ . '/../CodeExamples/NamespacesPhp/BadOrigin.php',
            __DIR__ . '/../CodeExamples/NamespacesPhp/Bad.php',
			true
		);
	}
}
