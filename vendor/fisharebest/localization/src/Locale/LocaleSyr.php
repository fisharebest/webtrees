<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSyr;

/**
 * Class LocaleSyr - Syriac
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSyr extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Syriac';
    }

    public function language()
    {
        return new LanguageSyr();
    }
}
