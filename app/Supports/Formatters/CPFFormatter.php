<?php

namespace App\Supports\Formatters;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Devesharp\Support\Helpers;
use Devesharp\Support\Masks;
use Illuminate\Support\Collection;
use MichaelRubel\Formatters\Formatter;

class CPFFormatter implements Formatter
{

    public function __construct(
        public string|null $CPF = null
    ) {}

    /**
     * Format the date.
     *
     * @param  Collection  $items
     *
     * @return string
     */
    public function format(Collection $items): string
    {
        return Masks::CPF($this->CPF);
    }
}
