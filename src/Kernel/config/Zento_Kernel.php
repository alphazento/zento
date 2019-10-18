<?php

return [
    'dynamicattribute_management' => true,
    'config_extend' => [
        'extra_repository' => \Zento\Kernel\Booster\Config\ConfigInDB\ConfigRepository::class,
        'grouping_provider' => null         //config can be different by grouping
    ],
    'use_web_theme' => true
];