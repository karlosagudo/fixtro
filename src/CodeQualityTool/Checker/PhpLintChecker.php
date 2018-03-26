<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\Process;

class PhpLintChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Linter php file';

	/** @var array */
	protected $filterOutput = [
		'PHP 7',
		'parallel jobs',
		'Fixed all files in \d\.\d seconds, \d.\d MB memory used',
		'found',
		'^\.',
		'100 %',
		'Checked',
];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function process()
	{
		$command = [
			$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
			$this->findBinary('parallel-lint'),
			'--exclude',
			'vendor',
			implode(' ', $this->filesToAnalyze),
		];

		$process = new Process($command);
		$process->enableOutput();
		$process->setWorkingDirectory($this->fixtroVendorRootPath);
		$this->setProcessLine($process->getCommandLine());
		$process->run(function ($type, $buffer) {
			$this->outputChecker[] = $buffer;
		});

		if (in_array('X', $this->outputChecker) || !$process->isSuccessful()) {
			$this->errors = $this->outputChecker;
			$this->errors[] = 'EXECUTED:'.str_replace("'", '', $process->getCommandLine());
		}
	}
}
