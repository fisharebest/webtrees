<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKam;

/**
 * Class LocaleKam - Kamba
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKam extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kikamba';
    }

    public function endonymSortable()
    {
        return 'KIKAMBA';
    }

    public function language()
    {
        return new LanguageKam();
    }
}
