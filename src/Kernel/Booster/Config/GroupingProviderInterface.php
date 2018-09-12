<?php
namespace Zento\Kernel\Booster\Config;

use Illuminate\Support\Arr;

interface GroupingProviderInterface
{
    const DEFAULT_GROUP = 'global';
    public function groupName();
}