<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\ProcessBuilder;

class PhpStanChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Executing PhpStan';

	/** @var array */
	protected $filterOutput = [
		'\d+\%',
		'[OK]',
];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * @psalm-suppress TypeCoercion
	 *
	 * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
	 */
	public function process()
	{
		$processAr = [
			$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
			$this->findBinary('phpstan'),
			'analyse',
			'-l',
			'4',
		];
		$configPhpStan = $this->findConfigPhpStan();

		if (!empty($configPhpStan)) {
			$processAr = array_merge($processAr, ['-c', $configPhpStan]);
		}

		$processAr = array_merge($processAr, $this->filesToAnalyze);

		$processBuilder = new ProcessBuilder($processAr);
		$process = $processBuilder->getProcess();
		$this->setProcessLine($process->getCommandLine());
		$process->run(function ($type, $buffer) {
			$this->outputChecker[] = $buffer;
		});

		$lastText = end($this->outputChecker);

		if (!$process->isSuccessful() || strpos($lastText, '[ERROR]') !== false) {
			$output = array_filter($this->outputChecker, function ($element) {
				return !preg_match('/\d+\%/', $element);
			});
			$output = implode(PHP_EOL, $output);

			$this->errors[] = sprintf('<error>%s</error>', trim($output));
		}
	}

	/**
	 * @return string
	 */
	private function findConfigPhpStan(): string
	{
		if (isset($this->parameters['confFile']) &&
			file_exists($this->parameters['confFile'])) {
			return $this->parameters['confFile'];
		}

		$possibleFiles = [
			'build/phpstan.neon',
			'build/phpstan.neon',
			'phpstan.neon',
			'phpstan.neon',
			'../phpstan.neon',
			'../build/phpstan.neon',
		];

		foreach ($possibleFiles as $buildFile) {
			if (file_exists($this->projectPath.'/'.$buildFile)) {
				return $this->projectPath.'/'.$buildFile;
			}
		}

		return '';
	}
}
