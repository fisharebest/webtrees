<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCcp;

/**
 * Class LocaleCcp - Chakma
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleCcp extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return '𑄌𑄋𑄴𑄟𑄳𑄦';
    }

    public function language()
    {
        return new LanguageCcp();
    }
}
