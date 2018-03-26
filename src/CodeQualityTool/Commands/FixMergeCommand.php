<?php

declare(strict_types=1);
/**
 * Date: 26/3/18
 * Time: 9:38.
 */

namespace KarlosAgudo\Fixtro\CodeQualityTool\Commands;

use KarlosAgudo\Fixtro\CodeQualityTool\GitFiles\GitFiles;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixMergeCommand extends GeneralCommand
{
	/**
	 * Configure command.
	 */
	protected function configure()
	{
		$this->setName('postmerge')
			->setDescription('Checks post merged files');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$gitFiles = new GitFiles($this->config);
		$files = $gitFiles->getMergedFiles();

		print_r($files);

		return count($files);
//		die();
//
//		$return = $this->executeCheckersAndShowResult($output, $files);
//
//		if (0 === $return) { //everything went well so we add changed files
//			$gitFiles->stageUpdatedFiles();
//		}
//
//		return $return;
	}
}
