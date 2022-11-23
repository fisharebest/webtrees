<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSbp;

/**
 * Class LocaleSbp - Sangu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSbp extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ishisangu';
    }

    public function endonymSortable()
    {
        return 'ISHISANGU';
    }

    public function language()
    {
        return new LanguageSbp();
    }
}
