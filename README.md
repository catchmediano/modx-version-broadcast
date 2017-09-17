# MODX Version Broadcast

This README is written in Markdown. If you read this in the MODX manager, it may be better to read it here: https://github.com/catchmediano/modx-version-broadcast

A MODX Revolution extra that exposes the current MODX version via a secure REST endpoint. The data is secured by validating a secret request token.

**Security notice: It is very important that the MODX Revolution version is kept secret via a secure endpoint. There exists serious exploits and bugs that can be used to hack your site. If your site is not up to date with the latest version, and hackers find your current installed version, it is a serious security threat. The authors of this extra takes no responsibility for any problems that might be caused by using this software. See the license.**

## Requirements

Please let us know if you have problems with this software and you meet the requirements.

* PHP  >= 5.5 (because of use of  [`password_hash`](http://php.net/password_hash) and [`password_verify`](http://php.net/password_verify))
* MODX Revolution >= 2.2

## Generate the Endpoint URL

If you lost the endpoint URL, you can generate it yourself by copying the following snippet and running it on your site:

```
$path = $modx->getOption('versionbroadcast.core_path', null, $modx->getOption('core_path') . 'components/versionbroadcast/');
$versionBroadcast = $modx->getService('versionbroadcast', 'VersionBroadcast', $path . 'model/versionbroadcast/');
if (!($versionBroadcast instanceof VersionBroadcast)) {
    $modx->log(\modX::LOG_LEVEL_ERROR, 'Could not load VersionBroadcast class');
    return;
}

$modx->log(\modX::LOG_LEVEL_ERROR, 'Endpoint URL: ' . $versionBroadcast->getEndpoint());
```

The endpoint will be logged to the Error log found in the manager interface.

Note that this URL is without the base URL prefix. This is because the plugin runs across all contexts. This means that if you have a site with base URL `http://www.site.tld`, and the endpoint URL is `rest/versionbroadcast?token=foobar`, then the final URL is `http://www.site.tld/rest/versionbroadcast?token=foobar`.

## Report Problems

If you encounter a problem with this software, please create a [issue](https://github.com/catchmediano/modx-version-broadcast/issues) on the official [GitHub repository](https://github.com/catchmediano/modx-version-broadcast/). You can also get in touch with the author on the [MODX Community Slack](https://modx.org) with the user @optimuscrime. 

## Contribute

Feel free to fork or contribute to the extra.
