<?php

return [
    'config_extend' => [
        'extra_repository' => \Zento\Kernel\Booster\Config\ConfigInDB\ConfigRepository::class,
        'grouping_provider' => null         //config can be different by grouping
    ]
];