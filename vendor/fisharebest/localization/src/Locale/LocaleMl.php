<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMl;

/**
 * Class LocaleMl - Malayalam
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMl extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'മലയാളം';
    }

    public function language()
    {
        return new LanguageMl();
    }
}
