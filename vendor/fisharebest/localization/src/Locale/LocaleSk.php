<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSk;

/**
 * Class LocaleSk - Slovak
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSk extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'slovak_ci';
    }

    public function endonym()
    {
        return 'slovenčina';
    }

    public function endonymSortable()
    {
        return 'SLOVENCINA';
    }

    public function language()
    {
        return new LanguageSk();
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
