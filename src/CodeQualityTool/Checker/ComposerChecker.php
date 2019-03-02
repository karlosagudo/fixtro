<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

class ComposerChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Checking Composer Files';

	public function process()
	{
		$composerJsonDetected = false;
		$composerLockDetected = false;

		foreach ($this->filesToAnalyze as $file) {
			if ('composer.json' === $file) {
				$composerJsonDetected = true;
			}

			if ('composer.lock' === $file) {
				$composerLockDetected = true;
			}
		}

		if ($composerJsonDetected && !$composerLockDetected) {
			$this->errors[] = 'composer.lock must be committed if composer.json is modified!';
		}
	}
}
