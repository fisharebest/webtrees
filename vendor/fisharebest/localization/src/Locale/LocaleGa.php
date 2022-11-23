<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGa;

/**
 * Class LocaleGa - Irish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Gaeilge';
    }

    public function endonymSortable()
    {
        return 'GAEILGE';
    }

    public function language()
    {
        return new LanguageGa();
    }
}
