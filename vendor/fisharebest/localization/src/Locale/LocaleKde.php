<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKde;

/**
 * Class LocaleKde - Makonde
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKde extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Chimakonde';
    }

    public function endonymSortable()
    {
        return 'CHIMAKONDE';
    }

    public function language()
    {
        return new LanguageKde();
    }
}
