<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIi;

/**
 * Class LocaleIi - Sichuan Yi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIi extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ꆈꌠꉙ';
    }

    public function language()
    {
        return new LanguageIi();
    }
}
