<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\StrictDeclareFixer;
use Symfony\Component\Filesystem\Filesystem;

class StrictDeclareTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/StrictDeclare/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, StrictDeclareFixer::class);
		$arCorrectedFile = file(__DIR__ . '/../CodeExamples/NamespacesPhp/Good.php');

		self::assertNotEquals('strict_types', $arCorrectedFile[0]);
	}

	public function tearDown()
	{
		$fs = new Filesystem();
		$fs->copy(
			__DIR__ . '/../CodeExamples/StrictDeclare/GoodOrigin.php',
            __DIR__ . '/../CodeExamples/StrictDeclare/Good.php',
			true
		);
	}
}
