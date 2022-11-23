<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDz;

/**
 * Class LocaleDz - Dzongkha
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDz extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'རྫོང་ཁ';
    }

    public function language()
    {
        return new LanguageDz();
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
