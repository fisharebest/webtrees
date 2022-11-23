<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSes;

/**
 * Class LocaleSes - Koyraboro Senni
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSes extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Koyraboro senni';
    }

    public function endonymSortable()
    {
        return 'KOYRABORO SENNI';
    }

    public function language()
    {
        return new LanguageSes();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
