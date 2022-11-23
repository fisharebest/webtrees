<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAs;

/**
 * Class LocaleAs - Assamese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAs extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'অসমীয়া';
    }

    public function language()
    {
        return new LanguageAs();
    }
}
