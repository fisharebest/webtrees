<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBal;

/**
 * Class LocaleBal - Baluchi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBal extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Tok Pisin';
    }

    public function language()
    {
        return new LanguageBal();
    }
}
