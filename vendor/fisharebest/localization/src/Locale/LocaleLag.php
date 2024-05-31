<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLag;

/**
 * Class LocaleLag - Langi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLag extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kɨlaangi';
    }

    public function endonymSortable()
    {
        return 'KILAANGI';
    }

    public function language()
    {
        return new LanguageLag();
    }
}
