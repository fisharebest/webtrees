<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZh;

/**
 * Class LocaleZh - Chinese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return '中文';
    }

    public function language()
    {
        return new LanguageZh();
    }
}
