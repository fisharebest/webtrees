<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePs;

/**
 * Class LocalePs - Pashto
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePs extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'پښتو';
    }

    public function language()
    {
        return new LanguagePs();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::ARAB_GROUP,
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT;
    }
}
