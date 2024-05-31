<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\Territory419;

/**
 * Class LocaleEs419 - Latin American Spanish
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEs419 extends LocaleEs
{
    public function endonym()
    {
        return 'español latinoamericano';
    }

    public function endonymSortable()
    {
        return 'ESPANOL LATINOAMERICANO';
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP   => self::COMMA,
            self::DECIMAL => self::DOT,
        );
    }

    public function territory()
    {
        return new Territory419();
    }
}
