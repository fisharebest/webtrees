<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGd;

/**
 * Class LocaleGd - Scottish Gaelic
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGd extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Gàidhlig';
    }

    public function endonymSortable()
    {
        return 'GAIDHLIG';
    }

    public function language()
    {
        return new LanguageGd();
    }
}
