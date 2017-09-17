<?php
require_once __DIR__ . '/build.prepare.php';

$root = dirname(dirname(__FILE__)) . '/';
$sources = [
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'options' => $root . '_build/options/',
    'attributes' => $root . '_build/attributes/',
    'resolvers' => $root . '_build/resolvers/',
];

/**
 *  Init MODX
 */

$modx = new \modX();
$modx->initialize('mgr');

/**
 *  Preparations
 */

$modx->setLogLevel(\modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$modx->getService('error', 'error.modError');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

/**
 *  Begin build the package
 */

$builder = new \modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true,'{core_path}components/' . PKG_NAME_LOWER . '/', '{assets_path}components/' . PKG_NAME_LOWER . '/');

/**
 *  Add plugin(s)
 */

$pluginList = include $sources['data'] . 'plugins.php';
$pluginAttributes = include $sources['attributes'] . 'plugins.php';

foreach ($pluginList as $pluginArr) {
    $eventsList = $pluginArr['events'];
    unset($pluginArr['events']);

    $plugin = $modx->newObject('modPlugin');
    $plugin->fromArray($pluginArr);

    $events = [];
    if (count($eventsList) > 0) {
        foreach ($eventsList as $eventsArr) {
            $event = $modx->newObject('modPluginEvent');
            $event->fromArray($eventsArr);
            $events[] = $event;
        }

        $plugin->addMany($events);
    }

    $vehicle = $builder->createVehicle($plugin, $pluginAttributes);
    $builder->putVehicle($vehicle);
}

/**
 *  Add setting(s)
 */

$settingsList = include $sources['data'] . 'settings.php';
$settingsAttributes = include $sources['attributes'] . 'settings.php';

foreach ($settingsList as $settingsArr) {
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray($settingsArr);

    $vehicle = $builder->createVehicle($setting, $settingsAttributes);
    $builder->putVehicle($vehicle);
}

/**
 *  Add files
 */

$builder->setPackageAttributes([
    'license' => file_get_contents($sources['root'] . 'LICENSE'),
    'readme' => file_get_contents($sources['root'] . 'README.md'),
    'changelog' => file_get_contents($sources['root'] . 'changelog.txt'),
    'setup-options' => [
        'source' => $sources['options'] . 'setup.options.php'
    ]
]);

/**
 *  Build ZIP file
 */

$builder->pack();
