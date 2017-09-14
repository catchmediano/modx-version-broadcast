<?php
if ($modx->event->name !== 'OnPageNotFound') {
    return;
}

$path = $modx->getOption('versionbroadcast.core_path', null, $modx->getOption('core_path') . 'components/versionbroadcast/');
$versionBroadcast = $modx->getService('versionbroadcast', 'VersionBroadcast', $path . 'model/versionbroadcast/');
if (!($versionBroadcast instanceof VersionBroadcast)) {
    $modx->log(\modX::LOG_LEVEL_ERROR, 'Could not load VersionBroadcast class');
    return;
}

return $versionBroadcast->run();
