<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Commands\Mock;

use KarlosAgudo\Fixtro\CodeQualityTool\Commands\GeneralCommand;

class CommandWithBadAnalyzers extends GeneralCommand
{
	protected $analyzers = [
		[
			'process' => 'KarlosAgudo\Fixtro\CodeQualityTool\Checker\ComposerChecker',
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
