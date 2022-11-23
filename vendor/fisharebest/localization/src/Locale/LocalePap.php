<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePap;

/**
 * Class LocalePap - Papiamentu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePap extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Papiamentu';
    }

    public function endonymSortable()
    {
        return 'PAPIAMENTU';
    }

    public function language()
    {
        return new LanguagePap();
    }
}
