<?php

namespace KarlosAgudo\Fixtro\Tests\CodeQualityTool\Events;

use KarlosAgudo\Fixtro\CodeQualityTool\Events\FixtroEvent;

class PostLoad extends FixtroEvent
{
	public function __invoke(FixtroEvent $event)
	{
		$event->setStopSignal(true);
	}
}
