<?php
if (isset($object) && isset($object->xpdo)) {
    $modx = $object->xpdo;
}

switch ($options[\xPDOTransport::PACKAGE_ACTION]) {
    case \xPDOTransport::ACTION_UPGRADE:
    case \xPDOTransport::ACTION_INSTALL:
        @set_time_limit(0);

        /*
         * $corePath = $modx->getOption('versionx.core_path',null,$modx->getOption('core_path').'components/versionx/');
        $versionx = $modx->getService('versionx','VersionX',$corePath.'/model/');
        $versionx->initialize('mgr');
         */
        break;
    default:
        break;
}

return true;
