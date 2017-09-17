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
    'plugins' => $root . 'core/components/versionbroadcast/elements/plugins/',
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
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

$modx->log(\xPDO::LOG_LEVEL_INFO, 'Create package');


/**
 *  Create category
 */

$categoryArr = include $sources['data'] . 'category.php';
$categoryAttributes = include $sources['attributes'] . 'category.php';
$category = $modx->newObject('modCategory');
$category->fromArray($categoryArr, '', true, true);

$modx->log(\xPDO::LOG_LEVEL_INFO, 'Creating category from array: ' . print_r($categoryArr, true));

$categoryVehicle = $builder->createVehicle($category, $categoryAttributes);

/**
 *  Add plugin(s)
 */

$pluginList = include $sources['data'] . 'plugins.php';
$pluginAttributes = include $sources['attributes'] . 'plugins.php';

foreach ($pluginList as $pluginArr) {
    $eventsList = $pluginArr['events'];
    unset($pluginArr['events']);

    $plugin = $modx->newObject('modPlugin');
    $plugin->fromArray($pluginArr, '', true, true);

    $modx->log(\xPDO::LOG_LEVEL_INFO, 'Adding plugin from array: ' . print_r($pluginArr, true));

    $events = [];
    if (count($eventsList) > 0) {
        foreach ($eventsList as $eventsArr) {
            $event = $modx->newObject('modPluginEvent');
            $event->fromArray($eventsArr, '', true, true);
            $events[] = $event;

            $modx->log(\xPDO::LOG_LEVEL_INFO, 'Adding event for plugin from array: ' . print_r($eventsArr, true));
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
    $setting->fromArray($settingsArr, '', true, true);

    $modx->log(\xPDO::LOG_LEVEL_INFO, 'Adding system setting from array: ' . print_r($settingsArr, true));

    $vehicle = $builder->createVehicle($setting, $settingsAttributes);
    $builder->putVehicle($vehicle);
}

/**
 *  Resolvers
 */

$categoryVehicle->resolve('file', [
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);

$categoryVehicle->resolve('php', [
    'source' => $sources['resolvers'] . 'setupoptions.php'
]);

$builder->putVehicle($categoryVehicle);

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

$modx->log(\xPDO::LOG_LEVEL_INFO, 'Sat package attributes');

/**
 *  Build ZIP file
 */

$builder->pack();

$modx->log(\xPDO::LOG_LEVEL_INFO, 'Packed the component');
