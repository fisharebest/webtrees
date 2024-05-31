<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTh;

/**
 * Class LocaleTh - Thai
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'ไทย';
    }

    public function language()
    {
        return new LanguageTh();
    }
}
