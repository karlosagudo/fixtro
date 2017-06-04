<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands\Mock;

use KarlosAgudo\Fixtro\CodeQualityTool\Commands\GeneralCommand;

class CommandWithBadFilter extends GeneralCommand
{
	protected $analyzers = [
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\ComposerChecker',
			'filter' => 'does-not-exists',
		],
];

	/**
	 * Configure command.
	 */
	protected function configure()
	{
		$this->setName('bad-test');
	}
}
