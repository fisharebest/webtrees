<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNyn;

/**
 * Class LocaleNyn - Nyankole
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNyn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Runyankore';
    }

    public function endonymSortable()
    {
        return 'RUNYANKORE';
    }

    public function language()
    {
        return new LanguageNyn();
    }
}
