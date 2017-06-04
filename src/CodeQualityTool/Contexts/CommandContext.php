<?php

namespace KarlosAgudo\Fixtro\CodeQualityTool\Contexts;

use KarlosAgudo\Fixtro\CodeQualityTool\Events\FixtroEvent;
use KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ConfigurationNotFoundException;
use KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;

/**
 * Created by PhpStorm.
 * User: karlos
 * Date: 22/05/17
 * Time: 19:01.
 */
class CommandContext
{
	/** @var EventDispatcher */
	private $eventDispatcher;
	/** @var ConsoleLogger */
	private $logger;
	/** @var array */
	private $config;

	/**
	 * CommandContext constructor.
	 *
	 * @param OutputInterface $output
	 * @param string          $configFile
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ConfigurationNotFoundException
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent
	 * @throws \Symfony\Component\Yaml\Exception\ParseException
	 */
	public function __construct(OutputInterface $output, string $configFile = '')
	{
		$this->logger = new ConsoleLogger($output, []);
		$this->eventDispatcher = new EventDispatcher();
		$fixtroConfig = $this->findFixtroConfig($configFile);

		if ($fixtroConfig) {
			$this->logger->info('Found Fixtro Config File:'.$fixtroConfig);
			$this->config = $this->parseFixtroConfig($fixtroConfig);
		}
	}

	/**
	 * @return EventDispatcher
	 */
	public function getEventDispatcher(): EventDispatcher
	{
		return $this->eventDispatcher;
	}

	/**
	 * @return ConsoleLogger
	 */
	public function getLogger(): ConsoleLogger
	{
		return $this->logger;
	}

	/**
	 * @return array
	 */
	public function getConfig(): array
	{
		return $this->config;
	}

	/**
	 * @return string
	 */
	public function getProjectRootPath(): string
	{
		if (\Phar::running(true)) {
			$rootPath = './';

			return $rootPath;
		}

		return $_SERVER['PWD'];
	}

	/**
	 * @param string $configFile
	 *
	 * @return false|string
	 */
	private function findFixtroConfig(string $configFile = '')
	{
		if (!empty($configFile) && file_exists($configFile)) {
			return $configFile;
		}

		if (file_exists($this->getProjectRootPath().'/fixtro.yml')) {
			return $this->getProjectRootPath().'/fixtro.yml';
		}

		if (file_exists($this->getProjectRootPath().'/build/fixtro.yml')) {
			return $this->getProjectRootPath().'/build/fixtro.yml';
		}

		return false;
	}

	/**
	 * @param string $configFile
	 *
	 * @return array
	 *
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ExecutionStoppedByEvent
	 * @throws \Symfony\Component\Yaml\Exception\ParseException
	 * @throws \KarlosAgudo\Fixtro\CodeQualityTool\Exceptions\ConfigurationNotFoundException
	 */
	private function parseFixtroConfig(string $configFile): array
	{
		if (!is_readable($configFile)) {
			throw new ConfigurationNotFoundException(
				sprintf('The file %s doesn\'t exists or its not readable', $configFile)
			);
		}

		$configContent = file_get_contents($configFile);
		if (!$configContent) {
			throw new ConfigurationNotFoundException(
				sprintf('The file %s doesn\'t exists or its not readable', $configFile)
			);
		}

		$config = Yaml::parse($configContent);

		$config['ignoreFolders'] = $config['ignoreFolders'] ?: ['vendors'];
		$config['sourceFolders'] = $config['sourceFolders'] ?: ['src'];

		if (!is_array($config['ignoreFolders'])) {
			$config['ignoreFolders'] = [$config['ignoreFolders']];
		}

		if (!is_array($config['sourceFolders'])) {
			$config['sourceFolders'] = [$config['sourceFolders']];
		}

		if (isset($config['events']) && is_array($config['events'])) {
			$this->findAndLoadComposerAutoload();
			$this->loadEvents($config['events']);
			$this->throwEvent('config.post_load', $config);
		}

		return $config;
	}

	/**
	 * @param array $events
	 */
	private function loadEvents(array $events)
	{
		foreach ($events as $eventName => $callable) {
			if (!is_callable(new $callable())) {
				$this->logger->info('Invalid event:'.$eventName.' is not callable:'.$callable);
				continue;
			}

			$this->logger->debug('[EVENT-Listener] Callable: {callable} Listening to Event: {eventName}',
				['callable' => $callable, 'eventName' => $eventName]
			);

			$this->eventDispatcher->addListener($eventName, new $callable());
		}
	}

	/**
	 * @return mixed
	 */
	private function findAndLoadComposerAutoload()
	{
		if (is_file($this->getProjectRootPath().'/vendor/autoload.php') && !$this->isFixtro()) {
			$this->logger->info('Loading composer autoload');

			return require_once $this->getProjectRootPath().'/vendor/autoload.php';
		}
	}

	/**
	 * @param string     $eventName
	 * @param array      $infoMessages
	 * @param null|array $errors
	 *
	 * @throws ExecutionStoppedByEvent
	 */
	public function throwEvent(string $eventName, array $infoMessages, $errors = [])
	{
		$eventConfigLoaded = new FixtroEvent($infoMessages, $errors);
		$this->logger->debug('[EVENT] {eventName} launched', ['eventName' => $eventName]);
		$this->getEventDispatcher()->dispatch($eventName, $eventConfigLoaded);

		if ($eventConfigLoaded->getStopSignal()) {
			throw new ExecutionStoppedByEvent('Execution stopped by '.$eventName.' Event. Exception Launched');
		}

		if ($eventConfigLoaded->getPassSignal()) {
			return 'PASS-SIGNAL';
		}

		return $eventConfigLoaded;
	}

	private function isFixtro()
	{
		if (file_exists(__DIR__.'/../../../../../bin/fixtro') ||
			file_exists(__DIR__.'/../../../../../../bin/fixtro')
		) { //installed in vendors/bin
			return false;
		}

		return true;
	}
}
