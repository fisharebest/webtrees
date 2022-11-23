<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSmj;

/**
 * Class LocaleSmj
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSmj extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'julevsámegiella';
    }

    public function endonymSortable()
    {
        return 'JULEVSAMEGIELLA';
    }

    public function language()
    {
        return new LanguageSmj();
    }
}
