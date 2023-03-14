<?php declare(strict_types=1);

namespace Laser\Core\DevOps\StaticAnalyze\PHPStan;

use Laser\Core\DevOps\StaticAnalyze\StaticAnalyzeKernel;
use Laser\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$classLoader = require __DIR__ . '/phpstan-bootstrap.php';

$pluginLoader = new StaticKernelPluginLoader($classLoader);

$kernel = new StaticAnalyzeKernel('phpstan_dev', true, $pluginLoader, 'phpstan-test-cache-id');
$kernel->boot();

return new Application($kernel);
