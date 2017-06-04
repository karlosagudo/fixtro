<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpLintChecker;

class PhpLintTest extends GeneralCheckerTestCase
{
	public function setUp()
	{
		$badCode = '<?php echo "hhello';
		file_put_contents(__DIR__.'/../CodeExamples/PhpLint/BadDummyTest.php', $badCode);
	}

	public function testFunctionalOk()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/PhpLint/Good.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PhpLintChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	public function testFunctionalKo()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/PhpLint/BadDummyTest.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PhpLintChecker::class);

		self::assertEquals($exit[0][0], 'X');
		$this->destroyBadCode(__DIR__.'/../CodeExamples/PhpLint/BadDummyTest.php');
	}

	public function tearDown()
	{
		$this->destroyBadCode(__DIR__.'/../CodeExamples/PhpLint/BadDummyTest.php');
	}
}
