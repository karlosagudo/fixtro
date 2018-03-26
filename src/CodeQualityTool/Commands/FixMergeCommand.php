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

	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$gitFiles = new GitFiles($this->config);
		$files = $gitFiles->getMergedFiles();

		if (in_array('composer.lock', $files)) {
			$this->logger->info('Updating Composer dependencies');
			exec($this->getProjectRootPath().'/bin/composer install');
		}

		return 1;
	}
}
