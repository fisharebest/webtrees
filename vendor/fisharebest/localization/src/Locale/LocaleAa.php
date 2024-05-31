<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAa;

/**
 * Class LocaleAa - Afar
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAa extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Qafar';
    }

    public function endonymSortable()
    {
        return 'QAFAR';
    }

    public function language()
    {
        return new LanguageAa();
    }
}
