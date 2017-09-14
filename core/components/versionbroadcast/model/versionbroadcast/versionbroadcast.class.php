<?php

class VersionBroadcast {

    private $modx;
    private $config;

    function __construct(\modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $this->request = null;
        $basePath = $this->modx->getOption(
            'genericrouter.core_path',
            $config,
            $this->modx->getOption('core_path') . 'components/versionbroadcast/'
        );
        $assetsUrl = $this->modx->getOption(
            'genericrouter.assets_url',
            $config,
            $this->modx->getOption('assets_url') . 'components/versionbroadcast/'
        );
        $this->config = array_merge([
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath . 'model/'
        ], $config);
        $this->modx->addPackage('versionbroadcast', $this->config['modelPath']);
    }

    public function run()
    {
        return json_encode([
            'version' => $this->modx->getOption('settings_version')
        ]);
    }
}
