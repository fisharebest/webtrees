<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYue;
use Fisharebest\Localization\Territory\TerritoryHk;

/**
 * Class LocaleYueHk - Chinese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleYueHk extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return '粵語';
    }

    public function language()
    {
        return new LanguageYue();
    }

    public function territory()
    {
        return new TerritoryHk();
    }
}
