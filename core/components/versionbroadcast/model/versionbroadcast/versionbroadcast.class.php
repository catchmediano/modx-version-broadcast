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
        $this->config = array_merge([
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath . 'model/'
        ], $config);
        $this->modx->addPackage('versionbroadcast', $this->config['modelPath']);
    }

    public function run()
    {
        if (!$this->validateRequest()) {
            return null;
        }

        return $this->outputVersion();
    }

    private function validateRequest()
    {
        if (!$this->modx->getOption('friendly_urls')) {
            return false;
        }

        if (!$this->isValidUri()) {
            return false;
        }

        if (!$this->isValidToken()) {
            return false;
        }

        return true;
    }

    private function isValidUri()
    {
        $param = $this->modx->getOption('request_param_alias',null,'q');
        if (!isset($_REQUEST[$param])) {
            return false;
        }

        $uri = $_REQUEST[$param];
        if (strlen($uri) === 0) {
            return false;
        }

        $uriTarget = self::cleanUri($this->modx->getOption('versionbroadcast.uri', null, ''));
        if (strlen($uriTarget) === 0) {
            return false;
        }

        $uriCurrent = self::cleanUri($uri);

        return $uriCurrent === $uriTarget;
    }

    private function isValidToken()
    {
        return true;
    }

    private function outputVersion()
    {
        header('Content-Type: application/json');

        return json_encode([
            'version' => $this->modx->getOption('settings_version')
        ]);
    }

    private static function cleanUri($uri)
    {
        if (substr($uri, 0, 1) === '/') {
            $uri = substr($uri, 1);
        }

        return trim($uri);
    }
}
