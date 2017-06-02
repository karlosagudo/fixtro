<?php

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\codeExamples\Events;

use karlosagudo\Fixtro\CodeQualityTool\Events\FixtroEvent;

class PostLoad extends FixtroEvent
{
    public function __invoke(FixtroEvent $event)
    {
        $event->setStopSignal(true);
    }
}