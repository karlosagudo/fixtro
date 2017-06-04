<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\ProcessBuilder;

class CodeStyleFixer extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Fixing Code Style';

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
			$processBuilder = $this->createProcess($file);
			$processBuilder->setWorkingDirectory($this->fixtroVendorRootPath);
			$process = $processBuilder->getProcess();
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
	 * @param $file
	 *
	 * @todo check RuleSet and apply
	 *
	 * @return ProcessBuilder
	 * @psalm-suppress TypeCoercion
	 */
	protected function createProcess($file): ProcessBuilder
	{
		$runMode = 'fix';
		if (isset($this->parameters['runMode'])) {
			$runMode = $this->parameters['runMode'];
		}

		$ruleSet = $this->findRulesFile();

		$extraParams = '';
		if (isset($this->parameters['extraParams'])) {
			$extraParams = $this->parameters['extraParams'];
		}

		$afterParams = '';
		if (isset($this->parameters['afterParams'])) {
			$afterParams = $this->parameters['afterParams'];
		}

		return new ProcessBuilder(
			[
				$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
				$this->findBinary('php-cs-fixer'),
				$runMode,
				$file,
				$extraParams,
				$ruleSet,
				$afterParams,
			]
		);
	}

	private function findRulesFile(): string
	{
		$possibleFiles = [
			'build/.php_cs',
			'.php_cs',
			'/../.php_cs',
			'/../build/.php_cs',
		];

		// if not found use fixtro vendor one
		$defaultBuildFile = __DIR__.'/../../../build/.php_cs';

		foreach ($possibleFiles as $buildFile) {
			if (file_exists($this->projectPath.'/'.$buildFile)) {
				return '--config='.$this->projectPath.'/'.$buildFile;
			}
		}

		if (isset($this->parameters['ruleFile'])) {
			return $this->parameters['ruleFile'];
		}

		return $defaultBuildFile;
	}
}
