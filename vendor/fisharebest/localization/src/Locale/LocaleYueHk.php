<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYue;
use Fisharebest\Localization\Territory\TerritoryHk;

/**
 * Class LocaleYueHk - Chinese
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
