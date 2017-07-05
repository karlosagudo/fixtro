<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Checkers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class GeneralCheckerTestCase extends TestCase
{
	protected $parameters = [];

	protected function executeChecker(array $filesToAnalyze, $checkerClass, $verbose = false)
	{
		$verbosity = ConsoleOutput::VERBOSITY_NORMAL;
		if ($verbose) {
			$verbosity = ConsoleOutput::VERBOSITY_VERY_VERBOSE;
		}

		$output = new ConsoleOutput($verbosity);
		$checker = new $checkerClass($filesToAnalyze, $output, $this->parameters);
		$checker->process();

		return $checker->showResults();
	}

	protected function destroyBadCode($file)
	{
		if (file_exists($file)) {
			unlink($file);
		}
	}

    protected function glueExit(array $exit) : string
    {
        $lined = '';
        foreach ($exit as $key1 => $value1) {
            if (!is_array($value1) || count($value1) === 0) {
                continue;
            }

            foreach($value1 as $key2 => $value2) {
                $lined .= $value2;
            }
        }

        return $lined;
    }
}
