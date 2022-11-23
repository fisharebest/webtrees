<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAn;

/**
 * Class LocaleAn - Anglo-Saxon / Old-English
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'aragonés';
    }

    public function endonymSortable()
    {
        return 'ARAGONÉS';
    }

    public function language()
    {
        return new LanguageAn();
    }
}
