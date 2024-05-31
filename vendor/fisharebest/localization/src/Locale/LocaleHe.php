<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHe;

/**
 * Class LocaleHe - Hebrew
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHe extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'עברית';
    }

    public function language()
    {
        return new LanguageHe();
    }

    public function numberSymbols()
    {
        return array(
            self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
        );
    }
}
