<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\CodeQualityTool\Console;

use karlosagudo\Fixtro\CodeQualityTool\Commands\FixBranchCommand;
use karlosagudo\Fixtro\CodeQualityTool\Commands\FixEntireCommand;
use karlosagudo\Fixtro\CodeQualityTool\Commands\FixPreCommitCommand;
use karlosagudo\Fixtro\CodeQualityTool\Commands\InstallCommand;
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
		//$commands[] = new SelfUpdateCommand();

		return $commands;
	}
}
