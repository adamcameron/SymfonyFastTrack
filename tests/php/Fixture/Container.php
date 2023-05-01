<?php

namespace adamcameron\symfonythefasttrack\tests\Fixture;

use adamcameron\symfonythefasttrack\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Container
{
    public static function getContainer(): ContainerInterface
    {
        $kernel = new Kernel("test", false);
        $kernel->boot();

        return $kernel->getContainer();
    }
}
