<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSd;

/**
 * Class LocaleSd - Sindhi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSd extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'سنڌي';
    }

    public function language()
    {
        return new LanguageSd();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::ARAB_GROUP,
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::NEGATIVE => self::ALM . self::HYPHEN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT . self::ALM;
    }
}
