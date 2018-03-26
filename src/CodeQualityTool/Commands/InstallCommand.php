<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InstallCommand.
 *
 * @psalm-suppress MissingConstructor
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallCommand extends Command
{
	/** @var LoggerInterface */
	private $logger;

	/** @var QuestionHelper */
	private $questionHelper;

	/**
	 * Configure command.
	 *
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 */
	protected function configure()
	{
		$this->setName('install')
			->setDescription('Install the precommit hook and composer depencencies')
			;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 *
	 * @throws \Symfony\Component\Console\Exception\RuntimeException
	 * @throws \Symfony\Component\Console\Exception\LogicException
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->logger = new ConsoleLogger($output, []);

		$this->logger->info('Install composer dependencies');
		$this->questionHelper = $this->getHelper('question');

		$this->installComposerDependencies();
		$this->installPreCommit($input, $output);
		$this->installPostMerge($input, $output);

		if ($this->findFixtroConfig()) {
			return 0;
		}
		$this->installConfigFile($input, $output);

		return 0;
	}

	/**
	 * Do a composer install in fixtro dir.
	 */
	protected function installComposerDependencies()
	{
		if ($this->isComposerInstalledGlobally()) {
			$this->logger->debug('Composer installed globally');
			chdir($this->getFixtroVendorsBinPath());
			$command = 'composer install';
			$this->logger->debug('Executed '.$command);
			exec(escapeshellcmd($command), $exit);
			$this->exitCommandToOutput($exit);

			return;
		}
		$this->logger->debug('Looking for a composer installation');

		//look for composer.phar in the project
		$possiblePhars = [
			__DIR__.'/../../../../../../vendor/bin/composer',
			__DIR__.'/../../../../../../bin/composer',
			__DIR__.'/../../../../../composer.phar',
			__DIR__.'/../../../../../../composer.phar',
			$this->getProjectRootPath().'/composer.phar',
			$this->getProjectRootPath().'/composer',
			$this->getProjectRootPath().'/vendor/bin/composer.phar',
			$this->getProjectRootPath().'/vendor/bin/composer',
			];

		foreach ($possiblePhars as $possiblePhar) {
			if (file_exists($possiblePhar)) {
				$this->logger->debug('Found composer at:'.$possiblePhar);
				chdir($this->getFixtroVendorsBinPath());
				$command = 'php '.$possiblePhar.' install';
				exec(escapeshellcmd($command), $exit);
				$this->logger->debug('Executed '.$command);
				$this->exitCommandToOutput($exit);

				return;
			}
		}
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return bool
	 *
	 * @throws \Symfony\Component\Console\Exception\RuntimeException
	 */
	private function installPreCommit(InputInterface $input, OutputInterface $output): bool
	{
		if (!is_dir($this->getProjectRootPath().'/.git')) {
			return true;
		}

		$questionHook = new ConfirmationQuestion(
			'<info>Do you want to install the precommit hook?</info> (yes/no) Default: yes',
			true
		);

		if ($this->questionHelper->ask($input, $output, $questionHook)) {
			$this->installPreCommitHook();
		}

		return true;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return bool
	 *
	 * @throws \Symfony\Component\Console\Exception\RuntimeException
	 */
	private function installPostMerge(InputInterface $input, OutputInterface $output): bool
	{
		if (!is_dir($this->getProjectRootPath().'/.git')) {
			return true;
		}

		$questionHook = new ConfirmationQuestion(
			'<info>Do you want to install the postmerge hook?</info> (yes/no) Default: yes',
			true
		);

		if ($this->questionHelper->ask($input, $output, $questionHook)) {
			$this->installPostMergeHook();
		}

		return true;
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return bool
	 *
	 * @throws \Symfony\Component\Console\Exception\RuntimeException
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 * @throws \Symfony\Component\Console\Exception\LogicException
	 */
	private function installConfigFile(InputInterface $input, OutputInterface $output): bool
	{
		$questionConfigs = new ConfirmationQuestion(
			'<info>Do you want to create config files for the runners/fixers?</info> (yes/no) Default: yes',
			true);

		if ($this->questionHelper->ask($input, $output, $questionConfigs)) {
			return $this->createConfigFile($input, $output);
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function isComposerInstalledGlobally(): bool
	{
		exec('composer -v', $composerCmd);
		if (empty($composerCmd)) {
			return false;
		}

		return true;
	}

	/**
	 * @return string
	 */
	protected function getFixtroVendorsBinPath(): string
	{
		return dirname(dirname(dirname(__DIR__))).'/'; //fixtro local
	}

	/**
	 * @param array $exit
	 */
	protected function exitCommandToOutput(array $exit)
	{
		foreach ($exit as $line) {
			$this->logger->info($line);
		}
	}

	private function installPreCommitHook()
	{
		$this->logger->info('Installing as Git Hook precommit');
		$binFolder = dirname(__DIR__, 3).'/bin/';
		chdir($this->getProjectRootPath());

		if (is_file($this->getProjectRootPath().'/.git/hooks/pre-commit') ||
			is_link($this->getProjectRootPath().'/.git/hooks/pre-commit')
		) {
			unlink($this->getProjectRootPath().'/.git/hooks/pre-commit');
		}
		if (!@mkdir('.git/hooks/', 0777, true) && !is_dir('.git/hooks/')) {
			throw new FileNotFoundException();
		}

		symlink($binFolder.'/fixtro-precommit', '.git/hooks/pre-commit');
	}

	private function installPostMergeHook()
	{
		$this->logger->info('Installing as Git Hook postMerge');
		$binFolder = dirname(__DIR__, 3).'/bin/';
		chdir($this->getProjectRootPath());

		if (is_file($this->getProjectRootPath().'/.git/hooks/post-merge') ||
			is_link($this->getProjectRootPath().'/.git/hooks/post-merge')
		) {
			unlink($this->getProjectRootPath().'/.git/hooks/post-merge');
		}

		if (!@mkdir('.git/hooks/', 0777, true) && !is_dir('.git/hooks/')) {
			throw new FileNotFoundException();
		}

		symlink($binFolder.'/fixtro-postmerge', '.git/hooks/post-merge');
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return bool
	 *
	 * @throws \Symfony\Component\Console\Exception\LogicException
	 * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
	 */
	private function createConfigFile(InputInterface $input, OutputInterface $output): bool
	{
		$questionHelper = $this->getHelper('question');
		$buildFolder = $this->getPossibleLocalBuildFolder();

		if (!$buildFolder) {
			$buildFolder = $this->getPossibleLocalRootFolder();
		}

		if ($buildFolder) {
			$buildFolder = realpath($buildFolder);
			$questionConfigs = new ConfirmationQuestion(
				'<info>Do you want to create config files at:</info>'.$buildFolder.' (yes/no) Default: yes',
				true);

			if ($questionHelper->ask($input, $output, $questionConfigs)) {
				$this->copyDefaultConfigTo($buildFolder);

				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function isFixtro(): bool
	{
		return !(file_exists(__DIR__.'/../../../../../bin/fixtro') ||
			file_exists(__DIR__.'/../../../../../../bin/fixtro'));
	}

	/**
	 * @return null|string
	 */
	private function getPossibleLocalBuildFolder()
	{
		if ($this->isFixtro()) {
			return __DIR__.'/../../../build';
		}

		if (is_dir(__DIR__.'/../../../../../../build')) {
			return __DIR__.'/../../../../../../build';
		}

		if (is_dir(__DIR__.'/../../../../../../../build')) {
			return __DIR__.'/../../../../../../../build';
		}

		return null;
	}

	/**
	 * @return null|string
	 */
	private function getPossibleLocalRootFolder()
	{
		if (is_dir(__DIR__.'/../../../../../../')) {
			return __DIR__.'/../../../../../../';
		}

		return null;
	}

	/**
	 * @param $buildFolder
	 *
	 * @return bool
	 */
	private function copyDefaultConfigTo(string $buildFolder): bool
	{
		$fileSystem = new Filesystem();
		try {
			$fileSystem->copy(__DIR__.'/../../../build/fixtro.yml', $buildFolder.'/fixtro.yml');
		} catch (\Exception $e) {
			$this->logger->alert('Couldn\'t generate default config in {buildFolder}',
				['buildFolder' => $buildFolder]);

			return false;
		}

		return true;
	}

	/**
	 * @return false|string
	 */
	private function findFixtroConfig()
	{
		if (file_exists($this->getProjectRootPath().'/fixtro.yml')) {
			return $this->getProjectRootPath().'/fixtro.yml';
		}

		if (file_exists($this->getProjectRootPath().'/build/fixtro.yml')) {
			return $this->getProjectRootPath().'/build/fixtro.yml';
		}

		return false;
	}

	/**
	 * @return string
	 */
	protected function getProjectRootPath(): string
	{
		if (\Phar::running(true)) {
			return  './';
		}

		return $_SERVER['PWD'];
	}
}
