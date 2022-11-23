<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBgc;

/**
 * Class LocaleBgc - Haryanvi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBgc extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'हरियाणवी';
    }

    public function language()
    {
        return new LanguageBgc();
    }
}
