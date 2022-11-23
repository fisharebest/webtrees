<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAk;

/**
 * Class LocaleAk - Akan
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Akan';
    }

    public function endonymSortable()
    {
        return 'AKAN';
    }

    public function language()
    {
        return new LanguageAk();
    }
}
