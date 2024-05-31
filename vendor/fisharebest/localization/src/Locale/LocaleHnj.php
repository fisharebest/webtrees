<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHnj;

/**
 * Class LocaleHnj - Hmong
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleHnj extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return '𖬇𖬰𖬞 𖬌𖬣𖬵';
    }

    public function language()
    {
        return new LanguageHnj();
    }
}
