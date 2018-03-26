<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\Process;

class EsLintChecker extends AbstractChecker implements CheckerInterface
{
	const VERSION_RETURN_REGEXP = '/v(.*)/';

	/** @var string */
	protected $title = 'Checking JS files';

	/** @var array */
	protected $filterOutput = [
];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function process()
	{
		$binFile = $this->findBinaryEsLint();
		if (!$binFile) {
			$this->outputChecker[] = 'Fixtro can\'t find esLint, please install
             it: http://eslint.org/docs/user-guide/getting-started';
		}

		$confFile = $this->findConfFile();
		foreach ($this->filesToAnalyze as $file) {
			$process = $this->createProcess($binFile, $confFile, $file);
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
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 *
	 * @throws \Symfony\Component\Process\Exception\LogicException
	 * @throws \Symfony\Component\Process\Exception\RuntimeException
	 */
	public function findBinaryEsLint()
	{
		if (is_file($this->getProjectPath().'/node_modules/.bin/eslint')) {
			return $this->getProjectPath().'/node_modules/.bin/eslint';
		}

		$process = new Process(
			[
				'eslint',
				'-v',
			]
		);

		$process->run(function ($type, $buffer) {
			$this->outputChecker[] = $buffer;
		});

		$isFound = array_filter($this->outputChecker, function ($value) {
			return preg_match(self::VERSION_RETURN_REGEXP, $value);
		});

		$this->outputChecker = [];

		if (count($isFound)) {
			return 'eslint';
		}

		return false;
	}

	/**
	 * @return string
	 */
	private function findConfFile(): string
	{
		$possibleFiles = [
			'build/.eslintrc',
			'.eslintrc',
			'/../.eslintrc',
			'/../build/.eslintrc',
		];

		// if not found use fixtro vendor one
		$defaultBuildFile = __DIR__.'/../../../build/.eslintrc';

		foreach ($possibleFiles as $buildFile) {
			if (file_exists($this->projectPath.'/'.$buildFile)) {
				return '-c='.$this->projectPath.'/'.$buildFile;
			}
		}

		if (isset($this->parameters['ruleFile'])) {
			return $this->parameters['ruleFile'];
		}

		return $defaultBuildFile;
	}

	/**
	 * @param string $binFile
	 * @param string $confFile
	 * @param string $file
	 *
	 * @return Process
	 */
	private function createProcess(string $binFile, string $confFile, string $file): Process
	{
		return new Process(
			[
				$binFile,
				$confFile,
				$file,
			]
		);
	}
}
