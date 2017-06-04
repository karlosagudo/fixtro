<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\CheckersRunner;
use KarlosAgudo\Fixtro\CodeQualityTool\Checker\StrictDeclareFixer;
use KarlosAgudo\Fixtro\CodeQualityTool\Contexts\CommandContext;
use KarlosAgudo\Fixtro\CodeQualityTool\Events\FixtroEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CheckersRunnerTest extends TestCase
{
	public function testCheckersRunner()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/StrictDeclare/Good.php'];
		$output = $this->createMock(ConsoleOutput::class);
		$output->expects(self::atLeastOnce())->method('writeln');
		$checker = new StrictDeclareFixer($filesToAnalyzer, $output, []);
		$commandContext = new CommandContext($output);

		$runner = new CheckersRunner([$checker], $commandContext);
		$runner->run($output);
	}

	public function testEventDispatcherRunner()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/StrictDeclare/Good.php'];
		$output = $this->createMock(ConsoleOutput::class);
		$output->expects(self::atLeastOnce())->method('writeln');
		$checker = new StrictDeclareFixer($filesToAnalyzer, $output, []);
		$eventConfigLoaded = new FixtroEvent([], []);

		$commandContext = $this->createMock(CommandContext::class);
		$commandContext->expects(self::atLeastOnce())
			->method('throwEvent')->willReturn(new EventDispatcher());

		$runner = new CheckersRunner([$checker], $commandContext);
		$runner->run($output);
	}

	public function testEventStopSignalRunner()
	{
		$filesToAnalyzer = [__DIR__.'/../CodeExamples/StrictDeclare/Good.php'];
		$output = $this->createMock(ConsoleOutput::class);
		//$output->expects(self::atLeastOnce())->method('writeln');
		$checker = new StrictDeclareFixer($filesToAnalyzer, $output, []);
		$eventConfigLoaded = new FixtroEvent([], []);
		$eventConfigLoaded->setStopSignal();

		try {
			$commandContext = new CommandContext($output, __DIR__.'/../CodeExamples/ConfigFile/fixtroStopSignal.yml');
			$runner = new CheckersRunner([$checker], $commandContext);
			$runner->run($output);
		} catch (\Exception $e) {
			self::assertEquals(get_class($e), 'KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent');
			$when = new \DateTime($eventConfigLoaded->when());
			$twoSecondsAgo = new \DateTime(date('c', time() - 2));

			self::assertTrue($when > $twoSecondsAgo);
		}
	}
}
