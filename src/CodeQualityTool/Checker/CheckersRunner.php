<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use KarlosAgudo\Fixtro\CodeQualityTool\Contexts\CommandContext;
use KarlosAgudo\Fixtro\CodeQualityTool\Events\FixtroEvent;
use Symfony\Component\Console\Output\OutputInterface;

class CheckersRunner
{
	/** @var array */
	private $checkers;
	/** @var CommandContext */
	private $context;

	/**
	 * CheckersRunner constructor.
	 *
	 * @param array          $checkers
	 * @param CommandContext $context
	 */
	public function __construct(array $checkers, CommandContext $context)
	{
		$this->checkers = $checkers;
		$this->context = $context;
	}

	/**
	 * @param OutputInterface $output
	 *
	 * @return bool
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent
	 */
	public function run(OutputInterface $output)
	{
		$failed = false;
		foreach ($this->checkers as $checker) {
			$eventAnalyzerName = $this->getEventName($checker);
			$preExecute = $this->context->throwEvent('analyzer.'.$eventAnalyzerName.'.pre',
													 $this->context->getConfig(), []);
			if ('PASS-SIGNAL' === $preExecute) {
				$this->context->getLogger()->info('[SKIPPED] Skipped by event');
				continue;
			}

			$checker->startProcess();
			list($info, $errors) = $checker->showResults();
			$result = $this->context->throwEvent('analyzer.'.$eventAnalyzerName.'.after', $info, $errors);

			if (FixtroEvent::class === get_class($result)) {
				$info = $result->getInfo();
				$errors = $result->getError();
			}

			$this->showInfo($output, $errors, $info);

			if (count($errors)) {
				$this->showErrors($output, $errors);
				$failed = true;

				break;
			}
		}

		return !$failed;
	}

	/**
	 * @param $checker
	 *
	 * @return string
	 */
	private function getEventName(AbstractChecker $checker): string
	{
		$shortName = (new \ReflectionClass($checker))->getShortName();
		$analyzerConfigKey = lcfirst($shortName);

		return strtolower($analyzerConfigKey);
	}

	/**
	 * @param OutputInterface $output
	 * @param array           $errors
	 * @param array           $info
	 */
	private function showInfo(OutputInterface $output, array $errors, array $info)
	{
		if (!count($errors) || $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
			foreach ($info as $line) {
				$output->writeln($line);
			}
		}
	}

	/**
	 * @param OutputInterface $output
	 * @param array           $errors
	 */
	private function showErrors(OutputInterface $output, array $errors)
	{
		foreach ($errors as $error) {
			$output->writeln("<error>$error</error>");
		}
	}
}
