<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\PsAlmChecker;
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

		self::assertContains('ERROR', $exit[0][3]);
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
