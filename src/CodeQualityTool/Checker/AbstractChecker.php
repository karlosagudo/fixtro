<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\Checker;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractChecker
{
	/** @var array */
	protected $outputChecker = [];
	/** @var array */
	protected $errors = [];
	/** @var string */
	protected $title = '';
	/** @var array */
	protected $parameters = [];
	/** @var OutputInterface */
	protected $output;
	/** @var array */
	protected $filesToAnalyze;
	/** @var array */
	protected $filterOutput = [];
	/** @var string */
	protected $fixtroVendorRootPath;
	/** @var string */
	protected $projectPath;
	/** @var array */
	private $processString = [];

	abstract protected function process();

	/**
	 * ComposerChecker constructor.
	 *
	 * @param array           $filesToAnalyze
	 * @param OutputInterface $output
	 * @param array           $parameters
	 */
	public function __construct(array $filesToAnalyze, OutputInterface $output, array $parameters)
	{
		$this->filesToAnalyze = $filesToAnalyze;
		$this->output = $output;
		$this->parameters = $parameters;
		$this->fixtroVendorRootPath = $this->getFixtroVendorRootPath();
		$this->projectPath = $this->getProjectPath();
	}

	/**
	 * Start the process of checking.
	 *
	 * @return bool
	 */
	public function startProcess()
	{
		$this->output->writeln("<info>{$this->title}</info> <info>#files: ".count($this->filesToAnalyze).'</info>');

		$this->process();

		return true;
	}

	/**
	 * @return array
	 * @todo: create a responseChecker Object
	 * @psalm-suppress TooManyArguments
	 */
	public function showResults()
	{
		return $this->filterOutput();
	}

	/**
	 * Filter not important info.
	 */
	protected function filterOutput()
	{
		$info = [];

		// No filter info if we are in verbose
		if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
			$this->putCommandFirst();

			return [$this->outputChecker, $this->errors];
		}

		foreach ($this->outputChecker as $line) {
			if (is_array($line)) {
				$line = implode("\n", $line);
			}
			if ($result = $this->filterLines($line)) {
				$info[] = $result;
			}
		}

		return [$info, $this->errors];
	}

	/**
	 * @param string $line
	 *
	 * @return mixed|string
	 */
	protected function filterLines(string $line)
	{
		foreach ($this->filterOutput as $cleanFilter) {
			if (preg_match("/$cleanFilter/", $line)) {
				return false;
			}
		}

		if (empty(trim($line))) {
			return false;
		}

		return $line;
	}

	/**
	 * @return string
	 */
	protected function getFixtroVendorRootPath(): string
	{
		return realpath(__DIR__.'/../../../'); //fixtro local
	}

	/**
	 * @return string
	 */
	protected function getProjectPath(): string
	{
		if ($this->isFixtro()) { // Fixtro is tested with Fixtro
			return realpath(__DIR__.'/../../');
		}

		return realpath(__DIR__.'/../../../../../../');
	}

	/**
	 * @param string $process
	 */
	protected function setProcessLine(string $process)
	{
		$this->processString[] = str_replace("'", '', $process);
	}

	/**
	 * put the executed process first.
	 */
	protected function putCommandFirst()
	{
		if (count($this->processString)) {
			foreach ($this->processString as $processString) {
				$lineProcess = '<info>Executed :</info><options=bold>'.$processString.'</>';
				array_unshift($this->outputChecker, $lineProcess);
			}
		}
	}

	private function isFixtro()
	{
		if (file_exists(__DIR__.'/../../../../../bin/fixtro') ||
			file_exists(__DIR__.'/../../../../../../bin/fixtro')
		) { //installed in vendors/bin
			return false;
		}

		return true;
	}

	/**
	 * Check if the project already has phpunit, or phpcsfixer, or other to use the local one.
	 *
	 * @param string $binaryFile
	 *
	 * @return string
	 */
	protected function findBinary(string $binaryFile): string
	{
	    //first we try locally in the project, if not, from fixtro dependencies
        if (file_exists($this->getProjectPath().'/bin/'.$binaryFile)) {
            return $this->getProjectPath().'/bin/'.$binaryFile;
        }

		return $this->fixtroVendorRootPath.'/bin/'.$binaryFile;
	}

	/**
	 * @param array $errors
	 *
	 * @return array
	 */
	protected function returnErrors(array $errors): array
	{
		foreach ($this->errors as $line) {
			if ($result = $this->filterLines($line)) {
				$errors[] = $result;
			}
		}

		return $errors;
	}
}
