<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMy;

/**
 * Class LocaleMy - Burmese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMy extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'မြန်မာ';
    }

    public function language()
    {
        return new LanguageMy();
    }

    protected function minimumGroupingDigits()
    {
        return 3;
    }
}
