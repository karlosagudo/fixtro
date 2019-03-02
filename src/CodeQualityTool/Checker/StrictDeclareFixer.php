<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\Process;

class StrictDeclareFixer extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Strict Declare Fixer';

	/** @var array */
	protected $filterOutput = [
		'You are running php-cs-fixer with xdebug enabled. This has a major impact on runtime performance.',
		'Loaded config default.',
		'Using cache file',
		'Fixed all files in',
		'Checked all files',
];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function process()
	{
		foreach ($this->filesToAnalyze as $file) {
			$process = $this->createProcess($file);
			$process->setWorkingDirectory($this->fixtroVendorRootPath);
			$this->setProcessLine($process->getCommandLine());
			$process->run(function ($type, $buffer) {
				$this->outputChecker[] = $buffer;
			});

			if (!$process->isSuccessful()) {
				$this->errors[] = sprintf('<error>%s</error>', trim($process->getErrorOutput()));
			}
		}
	}

	/**
	 * @param string $file
	 *
	 * @return Process
	 * @psalm-suppress TypeCoercion
	 */
	protected function createProcess(string $file): Process
	{
		return new Process(
			[
				$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
				$this->findBinary('php-cs-fixer'),
				'fix',
				$file,
				'--allow-risky=yes',
				'--rules=declare_strict_types',
			]
		);
	}
}
