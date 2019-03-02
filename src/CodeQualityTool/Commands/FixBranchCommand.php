<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use KarlosAgudo\Fixtro\CodeQualityTool\GitFiles\GitFiles;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixBranchCommand extends GeneralCommand
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
				'extraParams' => '--dry-run',
				'afterParams' => ' --diff',
			],
		],
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\NameSpaceFixer',
			'filter' => 'getPhpFiles',
			'parameters' => ['params' => '--dry-run'],
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
];

	/**
	 * Configure command.
	 */
	protected function configure()
	{
		$this->setName('branch')
			->setDescription('Check a branch')
			->addArgument('branch', InputArgument::REQUIRED, 'Name of the branch');
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 *
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * @psalm-suppress PossiblyInvalidArgument
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$branch = $input->getArgument('branch');
		if (!$branch) {
			throw new \InvalidArgumentException('Need to specify the name of the branch');
		}
		$gitFiles = new GitFiles($this->config['ignoreFolders']);
		$files = $gitFiles->getBranchDiffFiles($branch);

		return $this->executeCheckersAndShowResult($output, $files);
	}
}
