<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBho;

/**
 * Class LocaleBho - Bhojpuri
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBho extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'भोजपुरी';
    }

    public function endonymSortable()
    {
        return 'भोजपुरी';
    }

    public function language()
    {
        return new LanguageBho();
    }
}
