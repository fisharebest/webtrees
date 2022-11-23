<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBrx;

/**
 * Class LocaleBrx - Bodo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBrx extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'बर’';
    }

    public function language()
    {
        return new LanguageBrx();
    }
}
