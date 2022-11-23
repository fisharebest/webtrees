<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAnn;

/**
 * Class LocaleAnn - Obolo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAnn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Obolo';
    }

    public function endonymSortable()
    {
        return 'OBOLO';
    }

    public function language()
    {
        return new LanguageAnn();
    }
}
