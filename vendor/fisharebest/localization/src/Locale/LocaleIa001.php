<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\Territory001;

/**
 * Class LocaleIaFr
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleIa001 extends LocaleIa
{
    public function territory()
    {
        return new Territory001();
    }
}
