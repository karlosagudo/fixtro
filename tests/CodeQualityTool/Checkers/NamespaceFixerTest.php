<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\NameSpaceFixer;
use Symfony\Component\Filesystem\Filesystem;

class NamespaceFixerTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/NamespacesPhp/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, NameSpaceFixer::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/NamespacesPhp/Bad.php'];

		$exit = $this->executeChecker($filesToAnalyzer, NameSpaceFixer::class, true);
		$arCorrectedFile = file(__DIR__.'/../CodeExamples/NamespacesPhp/Bad.php');

		self::assertNotEquals('use b;', $arCorrectedFile[4]);
	}

	public function tearDown()
	{
		$fs = new Filesystem();
		$fs->copy(
			__DIR__.'/../CodeExamples/NamespacesPhp/BadOrigin.php',
			__DIR__.'/../CodeExamples/NamespacesPhp/Bad.php',
			true
		);
	}
}
