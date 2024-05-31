<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIg;

/**
 * Class LocaleIg - Igbo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Igbo';
    }

    public function endonymSortable()
    {
        return 'IGBO';
    }

    public function language()
    {
        return new LanguageIg();
    }
}
