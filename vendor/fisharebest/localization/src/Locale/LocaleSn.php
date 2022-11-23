<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSn;

/**
 * Class LocaleSn - Shona
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSn extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'chiShona';
    }

    public function endonymSortable()
    {
        return 'CHISHONA';
    }

    public function language()
    {
        return new LanguageSn();
    }
}
