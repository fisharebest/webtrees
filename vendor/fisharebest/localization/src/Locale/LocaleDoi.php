<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDoi;

/**
 * Class LocaleDoi - Dogri
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDoi extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'डोगरी';
    }

    public function endonymSortable()
    {
        return 'डोगरी';
    }

    public function language()
    {
        return new LanguageDoi();
    }
}
