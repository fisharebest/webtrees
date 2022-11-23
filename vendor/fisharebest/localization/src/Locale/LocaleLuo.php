<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLuo;

/**
 * Class LocaleLuo - Luo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLuo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Dholuo';
    }

    public function endonymSortable()
    {
        return 'DHOLUO';
    }

    public function language()
    {
        return new LanguageLuo();
    }
}
