<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDav;

/**
 * Class LocaleDav - Taita
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDav extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kitaita';
    }

    public function endonymSortable()
    {
        return 'KITAITA';
    }

    public function language()
    {
        return new LanguageDav();
    }
}
