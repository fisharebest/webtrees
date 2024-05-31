<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTeo;

/**
 * Class LocaleTeo - Teso
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTeo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kiteso';
    }

    public function endonymSortable()
    {
        return 'KITESO';
    }

    public function language()
    {
        return new LanguageTeo();
    }
}
