<?php declare(strict_types=1);

namespace Laser\Core;

require __DIR__ . '/TestBootstrapper.php';

(new TestBootstrapper())
    ->setPlatformEmbedded(false)
    ->setEnableCommercial()
    ->setBypassFinals(false)
    ->bootstrap();
