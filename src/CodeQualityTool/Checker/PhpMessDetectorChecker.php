<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\ProcessBuilder;

class PhpMessDetectorChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Mess Detector Analizer';

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * @psalm-suppress TypeCoercion
	 */
	public function process()
	{
		$ruleFile = $this->findRulesFile();

		foreach ($this->filesToAnalyze as $file) {
			$processBuilder = new ProcessBuilder([
				$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
				$this->findBinary('phpmd'),
				$file,
				'text',
				$ruleFile,
			]);
			$processBuilder->setWorkingDirectory($this->fixtroVendorRootPath);
			$process = $processBuilder->getProcess();
			$this->setProcessLine($process->getCommandLine());
			$process->run(function ($type, $buffer) {
				$this->errors[] = $buffer;
			});

			if (!$process->isSuccessful()) {
				$this->errors[] = sprintf('<error>%s</error>', trim($process->getErrorOutput()));
			}
		}
	}

	private function findRulesFile(): string
	{
		$possibleFiles = [
			'build/phpmd.xml',
			'phpmd.xml',
			'phpmd.xml.dist',
			'../phpmd.xml',
			'../build/phpmd.xml',
			'../phpmd.xml.dist',
		];

		// if not found use fixtro vendor one
		$defaultBuildFile = $this->fixtroVendorRootPath.'/build/phpmd.xml';

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
