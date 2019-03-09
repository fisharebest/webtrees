<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTk;

/**
 * Class LocaleTk - Turkmen
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleTk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'türkmen dili';
    }

    public function endonymSortable()
    {
        return 'TURKMEN DILI';
    }

    public function language()
    {
        return new LanguageTk();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::NBSP,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
