<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\Commands\Mock;

use karlosagudo\Fixtro\CodeQualityTool\Commands\GeneralCommand;

class CommandWithBadFilter extends GeneralCommand
{
	protected $analyzers = [
		[
			'process' => 'karlosagudo\Fixtro\CodeQualityTool\Checker\ComposerChecker',
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
