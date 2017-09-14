<?php
require_once __DIR__ . '/build.config.php';

if (file_exists(__DIR__ . '/build.environment.php')) {
    require_once __DIR__ . '/build.environment.php';
}
else {
    require_once __DIR__ . '/build.environment.default.php';
}

$configCore = MODX_BASE_PATH . 'config.core.php';
if (!file_exists($configCore)) {
    die('Failed to load config.core.php file.');
}

require_once $configCore;

if (!defined('MODX_CORE_PATH')) {
    die('Constant MODX_CORE_PATH not defined.');
}

$configInc = MODX_CORE_PATH . 'config/config.inc.php';
if (!file_exists($configInc)) {
    die('Failed to load config.inc.php file.');
}

require_once $configInc;
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

if (!class_exists('modX')) {
    die('Failed to load the modX class.');
}
