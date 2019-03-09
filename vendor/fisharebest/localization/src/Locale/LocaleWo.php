<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWo;

/**
 * Class LocaleWo - Wo
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleWo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Wolof';
    }

    public function endonymSortable()
    {
        return 'WOLOF';
    }

    public function language()
    {
        return new LanguageWo();
    }

    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::COMMA,
            self::GROUP   => self::DOT,
        );
    }
}
