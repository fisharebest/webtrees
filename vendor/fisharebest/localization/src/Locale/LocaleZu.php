<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZu;

/**
 * Class LocaleZu - Zulu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'isiZulu';
    }

    public function endonymSortable()
    {
        return 'ISIZULU';
    }

    public function language()
    {
        return new LanguageZu();
    }
}
