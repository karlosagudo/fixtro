<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\Process;

class PsAlmChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Static Analysis Psalm';

	/** @var array */
	protected $filterOutput = [
		'Checks took',
		'and used',
		'INFO: ',
		'Scanning files',
		'Analyzing files',
		'Psalm was able to infer types',
	];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * @psalm-suppress TypeCoercion
	 */
	public function process()
	{
		$ruleFile = $this->findRulesFile();
		foreach ($this->filesToAnalyze as $file) {
			$process = new Process(
				[
					$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
					$this->fixtroVendorRootPath.'/bin/psalm',
					'-c='.$ruleFile,
					'-m',
					$file,
				]
			);

			$process->setTimeout(3600);
			$this->setProcessLine($process->getCommandLine());
			$process->run(function ($type, $buffer) {
				$this->outputChecker[] = $buffer;
			});

			if (!$process->isSuccessful() || false !== strpos(implode('', $this->outputChecker), 'ERROR')) {
				$this->errors = $this->outputChecker;
				$this->errors[] = 'EXECUTED:'.str_replace("'", '', $process->getCommandLine());
			}
		}
	}

	private function findRulesFile(): string
	{
		$possibleFiles = [
			'build/psalm.xml',
			'psalm.xml',
			'psalm.xml.dist',
			'../psalm.xml',
			'../build/psalm.xml',
		];

		// if not found use fixtro vendor one
		$defaultBuildFile = $this->fixtroVendorRootPath.'/build/psalm.xml';

		foreach ($possibleFiles as $buildFile) {
			if (file_exists($this->projectPath.'/'.$buildFile)) {
				return $this->projectPath.'/'.$buildFile;
			}
		}

		if (isset($this->parameters['ruleFile']) &&
			file_exists($this->projectPath.$this->parameters['ruleFile'])) {
			return $this->projectPath.$this->parameters['ruleFile'];
		}

		return $defaultBuildFile;
	}
}
