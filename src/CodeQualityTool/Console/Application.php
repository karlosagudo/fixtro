<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Console;

use KarlosAgudo\Fixtro\CodeQualityTool\Commands\FixBranchCommand;
use KarlosAgudo\Fixtro\CodeQualityTool\Commands\FixEntireCommand;
use KarlosAgudo\Fixtro\CodeQualityTool\Commands\FixMergeCommand;
use KarlosAgudo\Fixtro\CodeQualityTool\Commands\FixPreCommitCommand;
use KarlosAgudo\Fixtro\CodeQualityTool\Commands\InstallCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
	public function __construct()
	{
		parent::__construct('Fixtro', '@package_version@');
	}

	protected function getDefaultCommands()
	{
		$commands = parent::getDefaultCommands();
		$commands[] = new FixPreCommitCommand();
		$commands[] = new FixEntireCommand();
		$commands[] = new FixBranchCommand();
		$commands[] = new InstallCommand();
		$commands[] = new FixMergeCommand();

		return $commands;
	}
}
