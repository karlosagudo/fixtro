<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpUnitChecker;

class PhpUnitTest extends GeneralCheckerTestCase
{
	public function testFunctionalOk()
	{
		$this->parameters['confFile'] = __DIR__ . '/../CodeExamples/PhpUnit/phpunit.xml';

		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpUnit/GoodExecutedTest.php'];
		$exit = $this->executeChecker($filesToAnalyzer, PhpUnitChecker::class);

		self::assertEquals($exit, [[], []]);
	}

	// Long live Gödel, this test is going to fail.
	public function testFunctionalKo()
	{
		$badCode = <<<EOT
<?php

class BadDummyTest extends \PHPUnit\Framework\TestCase
{
    public function testItsGoingToFail()
    {
        self::assertTrue(false);
    }
}
EOT;
		file_put_contents(__DIR__ . '/../CodeExamples/PhpUnit/BadDummyTest.php', $badCode);

		$filesToAnalyzer = [__DIR__ . '/../CodeExamples/PhpUnit/BadDummyTest.php'];
		$this->parameters['confFile'] = __DIR__ . '/../CodeExamples/PhpUnit/phpunit.xml';
		$exit = $this->executeChecker($filesToAnalyzer, PhpUnitChecker::class, true);

		self::assertEquals($exit[0][2], 'F');
		$this->destroyBadCode(__DIR__ . '/../CodeExamples/PhpUnit/BadDummyTest.php');
	}

	public function tearDown()
	{
		$this->destroyBadCode(__DIR__ . '/../CodeExamples/PhpUnit/BadDummyTest.php');
	}
}
