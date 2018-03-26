<?php

declare(strict_types=1);
/**
 * Date: 5/7/17
 * Time: 10:34.
 */

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Process\Process;

class BehatChecker extends AbstractChecker implements CheckerInterface
{
	/** @var string */
	protected $title = 'Executing Behat';

	/** @var array */
	protected $filterOutput = [
		'.*',
	];

	/**
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 *
	 * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
	 */
	public function process()
	{
		$buildFile = $this->findBuildBehatFile();

		if (empty($buildFile)) {
			$this->outputChecker[] = 'No behat conf file found';

			return;
		}

		$process = new Process(
			[
				$this->fixtroVendorRootPath.'/bin/php_no_xdebug',
				$this->findBinary('behat'),
				'-c',
				$buildFile,
				'--strict',
				'--stop-on-failure',
				'--lang en',
			]
		);

		$process->setWorkingDirectory($this->projectPath);
		$process->setTimeout(3600);
		$this->setProcessLine($process->getCommandLine());
		$process->run(function ($type, $buffer) {
			$this->outputChecker[] = $buffer;
		});

		$exit = implode('', $this->outputChecker);
		if (false !== strpos($exit, 'Failed scenarios')) {
			$this->errors[] = sprintf('<error>%s</error>', trim($exit));
		}
	}

	private function findBuildBehatFile(): string
	{
		if (isset($this->parameters['confFile']) &&
			file_exists($this->parameters['confFile'])) {
			return $this->parameters['confFile'];
		}

		$possibleFiles = [
			'build/behat.yml',
			'build/behat.yml.dist',
			'behat.yml',
			'behat.yml.dist',
			'../behat.yml',
			'../build/behat.yml',
			'../build/behat.yml.dist',
		];

		foreach ($possibleFiles as $buildFile) {
			if (file_exists($this->projectPath.'/'.$buildFile)) {
				return $this->projectPath.'/'.$buildFile;
			}
		}

		return '';
	}
}
