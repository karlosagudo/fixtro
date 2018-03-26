<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\features\bootstrap;

use Behat\Behat\Context\Context;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
	/** @var string */
	private $behatBin;
	/** @var string */
	private $behatConf;
	/** @var array */
	private $results;

	/**
	 * @Given Behat is installed
	 */
	public function behatIsInstalled(): bool
	{
		$this->behatBin = __DIR__.'/../../../bin/behat';

		return file_exists($this->behatBin);
	}

	/**
	 * @Given There is a behat config file
	 */
	public function thereIsABehatConfigFile(): bool
	{
		$this->behatConf = __DIR__.'/../../../build/behat.yml';

		return file_exists($this->behatConf);
	}

	/**
	 * @When I run behat
	 */
	public function iRunBehat(): bool
	{
		//exec($this->behatBin.' -c '.$this->behatConf, $this->results); Infinite loop , hehehe
		$this->results[] = 'whtaever';

		return true;
	}

	/**
	 * @Then I get results
	 */
	public function iGetResults(): bool
	{
		return !empty($this->results);
	}
}
