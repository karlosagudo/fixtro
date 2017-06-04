<?php

namespace KarlosAgudo\Fixtro\CodeQualityTool\Events;

use Symfony\Component\EventDispatcher\Event;

class FixtroEvent extends Event
{
	/**
	 * @var array|null
	 */
	private $infoMessages;
	/**
	 * @var array|null
	 */
	private $errorMessages;
	/**
	 * @var \DateTimeImmutable
	 */
	private $ocurredOn;
	/**
	 * @var bool
	 */
	private $stopSignal;
	/**
	 * @var bool
	 */
	private $passSignal;

	/**
	 * FixtroEvent constructor.
	 *
	 * @param array|null $infoMessages
	 * @param array|null $errorMessages
	 */
	public function __construct($infoMessages = [], $errorMessages = [])
	{
		$this->infoMessages = $infoMessages;
		$this->errorMessages = $errorMessages;
		$this->ocurredOn = new \DateTimeImmutable();
		$this->stopSignal = false;
		$this->passSignal = false;
	}

	public function getInfo()
	{
		return $this->infoMessages;
	}

	public function setInfo(array $infoMessages = []): self
	{
		$this->infoMessages = $infoMessages;

		return $this;
	}

	public function setErrors(array $errorMessages = []): self
	{
		$this->errorMessages = $errorMessages;

		return $this;
	}

	public function getError()
	{
		return $this->errorMessages;
	}

	public function when()
	{
		return $this->ocurredOn->format('c');
	}

	public function setStopSignal(bool $stopSignal = true)
	{
		$this->stopSignal = $stopSignal;
		if ($stopSignal) {
			$this->stopPropagation();
		}

		return $this;
	}

	public function getStopSignal(): bool
	{
		return $this->stopSignal;
	}

	public function setPassSignal(bool $passSignal = true)
	{
		$this->passSignal = $passSignal;
		if ($passSignal) {
			$this->stopPropagation();
		}

		return $this;
	}

	public function getPassSignal(): bool
	{
		return $this->passSignal;
	}
}
