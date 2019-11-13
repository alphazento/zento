<?php
return [
    "Zento_Kernel"=>[
        "version" => "0.0.1",
        "commands" => [
            "\\Zento\\Kernel\\PackageManager\\Console\\Commands\\MakePackage",
            "\\Zento\\Kernel\\PackageManager\\Console\\Commands\\EnablePackage",
            "\\Zento\\Kernel\\PackageManager\\Console\\Commands\\DisablePackage",
            "\\Zento\\Kernel\\ThemeManager\\Console\\Commands\\ListTheme",
            "\\Zento\\Kernel\\Booster\\Events\\Commands\\ListListener",
            '\Zento\Kernel\Booster\Config\Console\Commands\ConfigSet'
        ]
    ]
];