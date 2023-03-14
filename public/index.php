<?php

$autoload = __DIR__ . '/../vendor/autoload_runtime.php';
if (!file_exists($autoload)){
    die('Preparing to install vendor dependencies...');
}

require_once __DIR__ . '/../vendor/autoload_runtime.php';

return function (array $context) {

}