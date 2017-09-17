<?php
if (isset($object) && isset($object->xpdo)) {
    $modx = $object->xpdo;
}

switch ($options[\xPDOTransport::PACKAGE_ACTION]) {
    case \xPDOTransport::ACTION_UPGRADE:
    case \xPDOTransport::ACTION_INSTALL:
        @set_time_limit(0);

        $path = $modx->getOption('versionbroadcast.core_path', null, $modx->getOption('core_path') . 'components/versionbroadcast/');
        $versionBroadcast = $modx->getService('versionbroadcast', 'VersionBroadcast', $path . 'model/versionbroadcast/');
        if (!($versionBroadcast instanceof VersionBroadcast)) {
            $modx->log(\modX::LOG_LEVEL_ERROR, 'Could not load VersionBroadcast class');
            return;
        }

        $settings = [
            'versionbroadcast_secret',
            'versionbroadcast_salt',
            'versionbroadcast_uri',
            'versionbroadcast_token_param',
        ];

        foreach ($settings as $setting) {
            if (!isset($options[$setting]) or strlen($options[$setting]) === 0) {
                continue;
            }

            $settingObjKey = str_replace('_', '.', $setting);
            $settingObj = $modx->getObject('modSystemSetting', $settingObjKey);

            if (!$settingObj) {
                continue;
            }

            $settingObj->set('value', $options[$setting]);
            $settingObj->save();
        }

        $modx->cacheManager->refresh();

        // Output the endpoint
        if (!$versionBroadcast->isValidConfiguration(true)) {
            $modx->log(\modX::LOG_LEVEL_ERROR, 'Invalid configuration. Endpoint not enabled!');

            return true;
        }

        $modx->log(\modX::LOG_LEVEL_ERROR, 'Endpoint URL: ' . $versionBroadcast->getEndpoint(true));

    break;
    default:
    break;
}

return true;
