<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\CodeQualityTool\Checker;

class ComposerChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Checking Composer Files';

	public function process()
	{
		$composerJsonDetected = false;
		$composerLockDetected = false;

		foreach ($this->filesToAnalyze as $file) {
			if ($file === 'composer.json') {
				$composerJsonDetected = true;
			}

			if ($file === 'composer.lock') {
				$composerLockDetected = true;
			}
		}

		if ($composerJsonDetected && !$composerLockDetected) {
			$this->errors[] = 'composer.lock must be committed if composer.json is modified!';
		}
	}
}
