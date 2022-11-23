<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMn;

/**
 * Class LocaleMn - Mongolian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'монгол';
    }

    public function endonymSortable()
    {
        return 'МОНГОЛ';
    }

    public function language()
    {
        return new LanguageMn();
    }
}
