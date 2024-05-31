<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVo;

/**
 * Class LocaleVo - Volapük
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleVo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Volapük';
    }

    public function endonymSortable()
    {
        return 'VOLAPUK';
    }

    public function language()
    {
        return new LanguageVo();
    }
}
