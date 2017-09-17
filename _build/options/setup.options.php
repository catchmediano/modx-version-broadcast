<?php
function secure_random_string($length = 32) {
    // PHP7
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }

    // If OpenSSL is available
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    // Fallback
    return substr(sha1(rand() . '//' . time() . 'COW'), $length);
}

$output = '';

$html = '
<style type="text/css">
    #modx-setupoptions-panel { display: none; }
</style>
<script>
    document.getElementsByClassName("x-window-header-text")[0].innerHTML = "Version Broadcast settings";
</script>
<div style="width: 100%; padding: 15px; background-color: #f8d7da; margin-bottom: 15px; box-sizing: border-box; color: #721c24; border: 1px solid #f5c6cb; text-align: center;">
    <strong style="font-size: 120%;">Important security information. Please read instructions below!</strong>
</div>
<div style="padding-bottom: 15px">
<p style="padding-bottom: 10px">Set the settings for Version Broadcast below. Because we expose the version of the MODX installation, <strong>it is important to keep these settings secure to avoid leaking this information.</strong></p>
<p>When installation is finished, the REST endpoint URL is presented. This URL is not stored in clear text anywhere in the installation for security reasons. If you lose the URL, you can either reistall this extra to regenerate it, or you can follow the steps presented in the README to generate it yourself.</p>
</div>
<div style="padding-bottom: 15px">
    <label for="[[+secret_key]]">Secret</label>
    <input type="text" name="[[+secret_key]]" id="[[+secret_key]]" width="300" value="[[+secret_value]]" />
</div>
<div style="padding-bottom: 15px">
    <label for="[[+salt_key]]">Salt</label>
    <input type="text" name="[[+salt_key]]" id="[[+salt_key]]" width="300" value="[[+salt_value]]" />
</div>
<div style="padding-bottom: 15px">
    <label for="[[+uri_key]]">URI</label>
    <input type="text" name="[[+uri_key]]" id="[[+uri_key]]" width="300" value="[[+uri_value]]" />
</div>
<div>
    <label for="[[+token_param_key]]">Token parameter</label>
    <input type="text" name="[[+token_param_key]]" id="[[+token_param_key]]" width="300" value="[[+token_param_value]]" />
</div>';

if (in_array($options[\xPDOTransport::PACKAGE_ACTION], [\xPDOTransport::ACTION_INSTALL, \xPDOTransport::ACTION_UPGRADE])) {
    $chunk = $modx->newObject('modChunk', uniqid());
    $chunk->setCacheable(false);
    $output = $chunk->process([
        'secret_key' => 'versionbroadcast.secret',
        'salt_key' => 'versionbroadcast.salt',
        'uri_key' => 'versionbroadcast.uri',
        'token_param_key' => 'versionbroadcast.token_param',

        'secret_value' => secure_random_string(),
        'salt_value' => secure_random_string(),
        'uri_value' => 'rest/versionbroadcast',
        'token_param_value' => 'token',
    ], $html);
}

return $output;
