<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTwq;

/**
 * Class LocaleTwq - Tasawaq
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTwq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tasawaq senni';
    }

    public function endonymSortable()
    {
        return 'TASAWAQ SENNI';
    }

    public function language()
    {
        return new LanguageTwq();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
