<?php declare(strict_types=1);

use Composer\InstalledVersions;

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Laser\Core\Profiling\Profiling::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'test' => true],
    Laser\Core\Framework\Framework::class => ['all' => true],
    Laser\Core\System\System::class => ['all' => true],
    Laser\Core\Content\Content::class => ['all' => true],
    Laser\Core\Checkout\Checkout::class => ['all' => true],
    Laser\Core\DevOps\DevOps::class => ['all' => true],
    Laser\Core\Maintenance\Maintenance::class => ['all' => true],
];

if (InstalledVersions::isInstalled('symfony/web-profiler-bundle')) {
    $bundles[Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class] = ['dev' => true, 'test' => true, 'phpstan_dev' => true];
}
return $bundles;
