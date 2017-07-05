<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FixEntireCommand extends GeneralCommand
{
	/**
	 * DEFINE YOUR ANALYZERS HERE.
	 * Rule File as relative to root folder.
	 *
	 * @var array
	 */
	protected $analyzers = [
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\ComposerChecker',
			'filter' => 'getComposerFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpLintChecker',
			'filter' => 'getPhpFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\CodeStyleFixer',
			'filter' => 'getPhpFiles',
			'parameters' => [
				'ruleFile' => '--rules=@Symfony',
				'runMode' => 'fix',
				//'extraParams' => '--dry-run',
				//'afterParams' => ' --diff',
			],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\NameSpaceFixer',
			'filter' => 'getPhpFiles',
			'parameters' => ['configFolder' => './build'],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpMessDetectorChecker',
			'filter' => 'getPhpFiles',
			'parameters' => ['ruleFile' => '/build/phpmd.xml'],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpUnitChecker',
			'filter' => 'getNullFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\BehatChecker',
			'filter' => 'getNullFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PsAlmChecker',
			'filter' => 'getPhpFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\EsLintChecker',
			'filter' => 'getJsFiles',
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpStanChecker',
			'filter' => 'getPhpFiles',
		],
];

	/**
	 * Configure command.
	 */
	protected function configure()
	{
		$this->setName('entire')
			->setDescription('Checks the whole project');
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @return int
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$files = $this->findAllFiles();

		return $this->executeCheckersAndShowResult($output, $files);
	}

	/**
	 * @return array
	 * @psalm-suppress TooManyArguments
	 */
	private function findAllFiles()
	{
		$srcFolder = $this->config['sourceFolders'] ?? $this->getProjectRootPath();
		$ignoreFolders = $this->config['ignoreFolders'] ?? ['vendor'];

		$srcFolder = $this->parseFolders($srcFolder);
		$ignoreFolders = $this->parseFolders($ignoreFolders);

		$finder = new Finder();
		$finder
			->files()
			->in($srcFolder)
		;

		if (is_array($ignoreFolders)) {
			foreach ($ignoreFolders as $ignoreFolder) {
				$finder->notPath($ignoreFolder);
			}
		}

		$return = [];
		foreach ($finder as $file) { /* @var SplFileInfo $file */
			$return[] = $file->getRealPath();
		}

		$this->logger->debug(sprintf('Processing %d files', count($return)));

		return $return;
	}

	/**
	 * @param $srcFolder
	 *
	 * @return array|string
	 */
	private function parseFolders($srcFolder)
	{
		if (!is_array($srcFolder)) {
			$srcFolder = [$srcFolder];
		}

		$srcFolder = array_filter($srcFolder, function ($possibleFolder) {
			return is_dir($possibleFolder);
		});

		return $srcFolder;
	}
}
