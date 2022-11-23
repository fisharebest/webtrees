<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUg;

/**
 * Class LocaleUg - Uyghur
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleUg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ئۇيغۇرچە';
    }

    public function language()
    {
        return new LanguageUg();
    }
}
