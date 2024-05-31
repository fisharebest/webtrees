<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMk;

/**
 * Class LocaleMk - Macedonian
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'македонски';
    }

    public function endonymSortable()
    {
        return 'МАКЕДОНСКИ';
    }

    public function language()
    {
        return new LanguageMk();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::DOT,
            self::DECIMAL => self::COMMA,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
