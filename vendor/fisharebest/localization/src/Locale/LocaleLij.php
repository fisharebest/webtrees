<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLij;

/**
 * Class LocaleLij - Ligurian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLij extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Líguru';
    }

    public function endonymSortable()
    {
        return 'LÍGURU';
    }

    public function language()
    {
        return new LanguageLij();
    }
}
