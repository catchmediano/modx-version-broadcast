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

    public function isValidateRequest()
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

    public function outputVersion()
    {
        header('Content-Type: application/json');

        return json_encode([
            'version' => $this->modx->getOption('settings_version')
        ]);
    }

    public function isValidConfiguration($fetchRaw = false)
    {
        $targetUri = $this->getSystemSetting('versionbroadcast.uri', $fetchRaw);
        $secret = $this->getSystemSetting('versionbroadcast.secret', $fetchRaw);

        $configs = [$targetUri, $secret];
        foreach($configs as $config) {
            if ($config === null or strlen($config) === 0) {
                return false;
            }
        }

        return true;
    }

    public function getEndpoint($fetchRaw = false)
    {
        $targetUri = self::cleanUri($this->getSystemSetting('versionbroadcast.uri', $fetchRaw));
        $param = $this->getSystemSetting('versionbroadcast.token_param', $fetchRaw, 'token');
        $secret = $this->getSystemSetting('versionbroadcast.secret', $fetchRaw);
        $salt = $this->getSystemSetting('versionbroadcast.salt', $fetchRaw);

        return $targetUri . '?' . $param . '=' . self::generateToken($secret, $salt);
    }

    private function getSystemSetting($key, $raw, $default = '')
    {
        // I am not very proud of this function. Because of a bug (?) it does not work to clear the system setting values
        // while executing from the setup options resolver. This results in getOption returning the old (empty) value
        // even if we have overriden the value in the resolver. This method is used to get around this problem, by fetching
        // the data via SQL queries instead.

        if ($raw) {
            $obj = $this->modx->getObject('modSystemSetting', $key);
            if (!$obj) {
                return $default;
            }

            if ($obj->get('value') === null) {
                return $default;
            }

            return $obj->get('value');
        }

        return $this->modx->getOption($key, null, $default);
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

        $targetUri = self::cleanUri($this->modx->getOption('versionbroadcast.uri', null, ''));
        if (strlen($targetUri) === 0) {
            return false;
        }

        $currentUri = self::cleanUri($uri);

        return $targetUri === $currentUri;
    }

    private function isValidToken()
    {
        $param = $this->modx->getOption('versionbroadcast.token_param', null, 'token');
        if (!isset($_REQUEST[$param])) {
            return false;
        }

        $currentToken = $_REQUEST[$param];
        if (strlen($currentToken) === 0) {
            return false;
        }

        $secret = $this->modx->getOption('versionbroadcast.secret', null, '');
        if ($secret === null or strlen($secret) === 0) {
            // Refuse to do request if no secret value is recorded
            $this->modx->log(\modX::LOG_LEVEL_ERROR, 'Refused to expose version. Missing secret value!');

            return false;
        }

        $salt = $this->modx->getOption('versionbroadcast.salt', null, '');
        if (strlen($salt) === 0) {
            $salt = '';
        }

        return password_verify(self::generateTokenInput($secret, $salt), $currentToken);
    }

    private static function cleanUri($uri)
    {
        if (substr($uri, 0, 1) === '/') {
            $uri = substr($uri, 1);
        }

        return trim($uri);
    }

    private static function generateTokenInput($secret, $salt)
    {
        return $secret . ',,' . $salt;
    }

    private static function generateToken($secret, $salt)
    {
        // We could supplement the password_hash function with the salt parameter, but this option is deprecated
        // for newer versions of PHP. We therefore just string append the salt to the secret value.
        return password_hash(self::generateTokenInput($secret, $salt), \PASSWORD_BCRYPT);
    }
}
