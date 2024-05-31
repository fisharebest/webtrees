<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMr;

/**
 * Class LocaleMr - Marathi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMr extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'मराठी';
    }

    public function language()
    {
        return new LanguageMr();
    }
}
