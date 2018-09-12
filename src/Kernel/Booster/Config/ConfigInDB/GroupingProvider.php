<?php
namespace Zento\Kernel\Booster\Config\ConfigInDB;

use Illuminate\Support\Arr;
use Zento\Kernel\Booster\Config\GroupingProviderInterface;

//nginx pass fastcgi_param site_group to php
// fastcgi_param SITEGROUP "global";

class GroupingProvider implements GroupingProviderInterface
{
    public function groupName() {
        return $_SERVER['SITEGROUP'] ?? GroupingProviderInterface::DEFAULT_GROUP;
    }
}