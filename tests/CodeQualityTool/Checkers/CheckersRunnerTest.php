<?php

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\Checkers;

use karlosagudo\Fixtro\CodeQualityTool\Checker\CheckersRunner;
use karlosagudo\Fixtro\CodeQualityTool\Checker\StrictDeclareFixer;
use karlosagudo\Fixtro\CodeQualityTool\Contexts\CommandContext;
use karlosagudo\Fixtro\CodeQualityTool\Events\FixtroEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CheckersRunnerTest extends TestCase
{
    public function testCheckersRunner()
    {
        $filesToAnalyzer = [__DIR__ . '/../CodeExamples/StrictDeclare/Good.php'];
        $output = $this->createMock(ConsoleOutput::class);
        $output->expects(self::atLeastOnce())->method('writeln');
        $checker = new StrictDeclareFixer($filesToAnalyzer, $output, []);
        $commandContext = new CommandContext($output);

        $runner = new CheckersRunner([$checker], $commandContext);
        $runner->run($output);
    }

    public function testEventDispatcherRunner()
    {
        $filesToAnalyzer = [__DIR__ . '/../CodeExamples/StrictDeclare/Good.php'];
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
        $filesToAnalyzer = [__DIR__ . '/../CodeExamples/StrictDeclare/Good.php'];
        $output = $this->createMock(ConsoleOutput::class);
        $output->expects(self::atLeastOnce())->method('writeln');
        $checker = new StrictDeclareFixer($filesToAnalyzer, $output, []);
        $eventConfigLoaded = new FixtroEvent([], []);
        $eventConfigLoaded->setStopSignal();

        print_r(get_declared_classes());


        $commandContext = new CommandContext($output, __DIR__ . '/../CodeExamples/ConfigFile/fixtroStopSignal.yml');

        $runner = new CheckersRunner([$checker], $commandContext);
        $runner->run($output);
    }

}