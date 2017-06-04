<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use KarlosAgudo\Fixtro\CodeQualityTool\Checker\CheckersRunner;
use KarlosAgudo\Fixtro\CodeQualityTool\Contexts\CommandContext;
use KarlosAgudo\Fixtro\CodeQualityTool\FilterFiles\GeneralFilters;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class GeneralCommand.
 */
class GeneralCommand extends Command
{
	/** @var array */
	protected $checkers = [];

	/** @var array */
	protected $config = [];

	/** @var LoggerInterface */
	protected $logger;

	/** @var EventDispatcher */
	protected $eventDispatcher;

	/** @var CommandContext */
	private $context;

	/** @var array */
	protected $analyzers;

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ConfigurationNotFoundException
	 */
	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->context = new CommandContext($output);
		$this->logger = $this->context->getLogger();
		$this->eventDispatcher = $this->context->getEventDispatcher();
		$this->config = $this->context->getConfig();
	}

	/**
	 * @param OutputInterface $output
	 * @param array           $files
	 *
	 * @return int
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function executeCheckersAndShowResult(OutputInterface $output, array $files): int
	{
		$this->context->throwEvent('files.loaded', $files, []);
		$checkers = $this->createCheckers($files, $output);
		$runner = new CheckersRunner($checkers, $this->context);
		$result = $runner->run($output);

		if (!$result) {
			$this->showFixTheCode($output);

			return -1;
		}

		$this->showCorrect($output);

		return 0;
	}

	/**
	 * where magic happens.
	 *
	 * @param array           $files
	 * @param OutputInterface $output
	 *
	 * @return array
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent
	 * @throws \InvalidArgumentException
	 */
	public function createCheckers(array $files, OutputInterface $output)
	{
		$checkers = [];
		if (!isset($this->analyzers) || !is_array($this->analyzers)) {
			throw new \InvalidArgumentException('Define analyzers');
		}
		$filterClass = new GeneralFilters($files);

		foreach ($this->analyzers as $analyzer) {
			$shortName = (new \ReflectionClass($analyzer['process']))->getShortName();
			$analyzerConfigKey = lcfirst($shortName);
			$eventAnalyzerName = strtolower($analyzerConfigKey);

			if ($this->isDisabledAnalyzerInConfig($analyzerConfigKey)) {
				continue;
			}

			$this->checkAnalyzerIsCorrect($analyzer);
			$process = $analyzer['process'];
			$filter = $analyzer['filter'];
			$filesFound = $this->applyFilter($filterClass, $filter);
			if (!$filesFound && $filter !== 'getNullFiles') {
				continue;
			}
			$this->context->throwEvent('analyzer.'.$eventAnalyzerName.'.files', $filesFound);

			$parameters = $this->getParameters($analyzer);
			$checkers[] = new $process($filesFound, $output, $parameters);
		}

		return $checkers;
	}

	/**
	 * @param OutputInterface $output
	 */
	public function showFixTheCode(OutputInterface $output)
	{
		$draw = $this->context->getConfig()['badMessage'] ?? file_get_contents(__DIR__.'/../Ascii/badMessage');

		return $output->write($draw);
	}

	/**
	 * @param OutputInterface $output
	 */
	public function showCorrect(OutputInterface $output)
	{
		$draw = $this->context->getConfig()['goodMessage'] ?? file_get_contents(__DIR__.'/../Ascii/goodMessage');

		return $output->write('<bg=green>'.$draw.'</>');
	}

	/**
	 * @param $analyzer
	 *
	 * @throws \InvalidArgumentException
	 */
	private function checkAnalyzerIsCorrect($analyzer)
	{
		if (!is_array($analyzer) || !isset($analyzer['process'], $analyzer['filter'])) {
			throw new \InvalidArgumentException('The analyzer should be an array with process, and filter keys');
		}
	}

	/**
	 * @param GeneralFilters $filterClass
	 * @param string         $filter
	 *
	 * @return array|mixed
	 */
	private function applyFilter(GeneralFilters $filterClass, string $filter)
	{
		if (!method_exists($filterClass, $filter)) {
			throw new \InvalidArgumentException('Invalid filter:'.$filter);
		}

		return call_user_func([$filterClass, $filter]);
	}

	/**
	 * @param $analyzer
	 *
	 * @return array
	 */
	private function getParameters($analyzer): array
	{
		$parameters = [];
		if (isset($analyzer['parameters'])) {
			$parameters = $analyzer['parameters'];
		}

		return $parameters;
	}

	/**
	 * @return string
	 */
	protected function getProjectRootPath(): string
	{
		return $this->context->getProjectRootPath();
	}

	/**
	 * @param $analyzerConfigKey
	 *
	 * @return bool
	 */
	private function isDisabledAnalyzerInConfig($analyzerConfigKey): bool
	{
		return isset($this->config[$analyzerConfigKey], $this->config[$analyzerConfigKey]['enable']) &&
			$this->config[$analyzerConfigKey]['enable'] === false;
	}
}
