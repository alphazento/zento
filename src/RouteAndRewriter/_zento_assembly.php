<?php
return [
    "Zento_RouteAndRewriter"=> [
        "version" => "0.0.1",
        "providers" => [
            "Zento\\RouteAndRewriter\\Providers\\RouteAndRewriterServiceProvider"
        ],
        "depends"=>[
            "Zento_Kernel"
        ]
    ]
];