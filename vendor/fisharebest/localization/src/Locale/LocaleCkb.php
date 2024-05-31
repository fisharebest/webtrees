<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCkb;

/**
 * Class LocaleCkb - Sorani
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCkb extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'کوردیی ناوەندی';
    }

    public function language()
    {
        return new LanguageCkb();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP    => self::ARAB_GROUP,
            self::DECIMAL  => self::ARAB_DECIMAL,
            self::NEGATIVE => self::RTL_MARK . self::HYPHEN,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT;
    }
}
