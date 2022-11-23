<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTok;

/**
 * Class LocaleTok - Toki Pona
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTok extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Toki Pona';
    }

    public function endonymSortable()
    {
        return 'TOKI PONA';
    }

    public function language()
    {
        return new LanguageTok();
    }
}
