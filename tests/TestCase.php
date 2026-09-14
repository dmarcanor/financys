<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;
use Shared\Domain\Aggregate;

abstract class TestCase extends BaseTestCase
{
    protected function fakeAmount(): string
    {
        return fake()->randomNumber(9)
            . '.'
            . str_pad((string) fake()->randomNumber(8), 8, '0', STR_PAD_LEFT);
    }

    protected function similarTo(object $expected): \Mockery\Matcher\Closure
    {
        return Mockery::on(function ($actual) use ($expected): bool {
            if (get_class($expected) !== get_class($actual)) {
                return false;
            }

            if ($expected instanceof Aggregate) {
                return $this->aggregateMatches($expected, $actual);
            }

            return serialize($expected) === serialize($actual);
        });
    }

    private function aggregateMatches(Aggregate $expected, object $actual): bool
    {
        $expectedClone = clone $expected;
        $actualClone = clone $actual;
        $expectedClone->extractEvents();
        $actualClone->extractEvents();

        if (serialize($expectedClone) !== serialize($actualClone)) {
            return false;
        }

        return $this->eventsMatch($expected, $actual);
    }

    private function eventsMatch(Aggregate $expected, Aggregate $actual): bool
    {
        $expectedEvents = (clone $expected)->extractEvents();
        $actualEvents = (clone $actual)->extractEvents();

        if (count($expectedEvents) !== count($actualEvents)) {
            return false;
        }

        foreach ($expectedEvents as $i => $event) {
            if (get_class($event) !== get_class($actualEvents[$i])) {
                return false;
            }
            if ($event->payload() !== $actualEvents[$i]->payload()) {
                return false;
            }
        }

        return true;
    }
}
