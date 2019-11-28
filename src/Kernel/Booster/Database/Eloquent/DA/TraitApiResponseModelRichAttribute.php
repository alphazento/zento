<?php

namespace Zento\Kernel\Booster\Database\Eloquent\DA;
use Zento\Kernel\Facades\ShareBucket;

trait TraitApiResponseModelRichAttribute {

    public function toArray() {
        if (ShareBucket::get('MODEL_RICH_MODE', false)) {
            // foreach(static::$RichRelations ?? [] as $key) {
            //     if ($relation = $this->relations[$key]) {

            //     }
            // }
        }
        return parent::toArray();
    }
}