<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use KarlosAgudo\Fixtro\CodeQualityTool\GitFiles\GitFiles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixPreCommitCommand extends GeneralCommand
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
			],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\NameSpaceFixer',
			'filter' => 'getPhpFiles',
			'parameters' => ['configFolder' => './build'],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\StrictDeclareFixer',
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
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\PhpStanChecker',
			'filter' => 'getPhpFiles',
		],
];

	/**
	 * Configure command.
	 */
	protected function configure()
	{
		$this->setName('precommit')
			->setDescription('Checks precommited files');
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @return int
	 *
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$gitFiles = new GitFiles($this->config);
		$files = $gitFiles->getPreCommitFiles();

		$return = $this->executeCheckersAndShowResult($output, $files);

		if (0 === $return) { //everything went well so we add changed files
			$gitFiles->stageUpdatedFiles();
		}

		return $return;
	}
}
