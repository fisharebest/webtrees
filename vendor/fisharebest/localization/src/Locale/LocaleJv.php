<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJv;

/**
 * Class LocaleJv - Javanese
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleJv extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Jawa';
    }

    public function language()
    {
        return new LanguageJv();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
