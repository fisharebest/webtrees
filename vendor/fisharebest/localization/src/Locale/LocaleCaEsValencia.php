<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Variant\VariantValencia;

/**
 * Class LocaleCaEsValencia
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCaEsValencia extends LocaleCaEs
{
    public function variant()
    {
        return new VariantValencia();
    }
}
