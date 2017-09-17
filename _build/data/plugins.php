<?php
return [
    [
        'name' => 'hookVersionBroadcast',
        'description' => 'Plugin that fires on page not find to broadcast the MODX Revolution version if the URI and tokens are valid',
        'category' => 0,
        'static' => true,
        'source' => 1,
        'plugincode' => trim(str_replace(['<?php', '?>'], '', file_get_contents($sources['plugins'] . 'plugin.versionbroadcast.php'))),
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/plugin.versionbroadcast.php',
        'events' => [
            [
                'event' => 'OnPageNotFound',
                'priority' => 0,
                'propertyset' => 0
            ]
        ]
    ]
];
