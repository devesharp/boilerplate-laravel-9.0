<?php

namespace App\Supports\Formatters;

use Devesharp\Support\Masks;
use Illuminate\Support\Collection;
use MichaelRubel\Formatters\Formatter;

class RGFormatter implements Formatter
{
    public function __construct(
        public string|null $RG = null
    ) {
    }

    /**
     * Format the date.
     *
     * @param  Collection  $items
     * @return string
     */
    public function format(Collection $items): string
    {
        return Masks::RG($this->RG);
    }
}
