<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands;

use KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands\Mock\CommandWithBadAnalyzers;
use KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands\Mock\CommandWithBadFilter;
use KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands\Mock\CommandWithoutAnalyzers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\Output;

class GeneralCommandTest extends TestCase
{
	/**
	 * @dataProvider provideCreateCheckersBad
	 */
	public function testCreateCheckersWithBadCommands($files, $output, $command)
	{
		$badCommand = new $command();
		$this->expectException(\InvalidArgumentException::class);
		$badCommand->createCheckers($files, $output);
	}

	public function provideCreateCheckersBad()
	{
		$output = $this->createMock(Output::class);
		$files = ['one.php', 'two.php'];

		return [
			[$files, $output, CommandWithoutAnalyzers::class],
			[$files, $output, CommandWithBadAnalyzers::class],
			[$files, $output, CommandWithBadFilter::class],
		];
	}
}
