<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTg;

/**
 * Class LocaleTg - Tajik
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleTg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'тоҷикӣ';
    }

    public function language()
    {
        return new LanguageTg();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }
}
