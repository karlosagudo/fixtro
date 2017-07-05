<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\CodeExamples\Behat\Ko;

use Behat\Behat\Context\Context;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
	private $behatBin;
	private $behatConf;
	private $results;

	/**
	 * Initializes context.
	 *
	 * Every scenario gets its own context instance.
	 * You can also pass arbitrary arguments to the
	 * context constructor through behat.yml.
	 */
	public function __construct()
	{
	}

	/**
	 * @Given Behat is installed
	 */
	public function behatIsInstalled()
	{
		$this->behatBin = __DIR__.'/../../../../../bin/behat';

		return file_exists($this->behatBin);
	}

	/**
	 * @Given There is a behat config file
	 */
	public function thereIsABehatConfigFile()
	{
		$this->behatConf = __DIR__.'/../../../build/behat.yml';

		return file_exists($this->behatConf);
	}

	/**
	 * @When I run behat
	 */
	public function iRunBehat()
	{
		//exec($this->behatBin.' -c '.$this->behatConf, $this->results); Infinite loop , hehehe
		$this->results[] = 'whtaever';

		throw new \Exception('Failing');
		return false;
	}

	/**
	 * @Then I get results
	 */
	public function iGetResults()
	{
		return !empty($this->results);
	}
}
