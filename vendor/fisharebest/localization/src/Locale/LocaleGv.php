<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGv;

/**
 * Class LocaleGv - Manx
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGv extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Gaelg';
    }

    public function endonymSortable()
    {
        return 'GAELG';
    }

    public function language()
    {
        return new LanguageGv();
    }
}
