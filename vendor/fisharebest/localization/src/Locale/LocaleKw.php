<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKw;

/**
 * Class LocaleKw - Cornish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKw extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'kernewek';
    }

    public function endonymSortable()
    {
        return 'KERNEWEK';
    }

    public function language()
    {
        return new LanguageKw();
    }
}
