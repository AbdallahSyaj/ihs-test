<?php

namespace Tests\Support;

use App\Models\Product;

class RestorableProduct extends Product
{
    protected $table = 'products';

    public bool $skipActiveSlugPrecheck = false;

    public function isActiveSlugTakenByAnotherProduct(): bool
    {
        if ($this->skipActiveSlugPrecheck) {
            return false;
        }

        return parent::isActiveSlugTakenByAnotherProduct();
    }
}
