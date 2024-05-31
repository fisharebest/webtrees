<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNb;

/**
 * Class LocaleNb - Norwegian Bokmål
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNb extends AbstractLocale implements LocaleInterface
{
    public function collation()
    {
        return 'danish_ci';
    }

    public function endonym()
    {
        return 'norsk bokmål';
    }

    public function endonymSortable()
    {
        return 'NORSK BOKMAL';
    }

    public function language()
    {
        return new LanguageNb();
    }
}
