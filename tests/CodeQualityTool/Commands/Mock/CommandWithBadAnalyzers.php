<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\Commands\Mock;

use karlosagudo\Fixtro\CodeQualityTool\Commands\GeneralCommand;

class CommandWithBadAnalyzers extends GeneralCommand
{
	protected $analyzers = [
		[
			'process' => 'karlosagudo\Fixtro\CodeQualityTool\Checker\ComposerChecker',
			'filter' => null,
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
