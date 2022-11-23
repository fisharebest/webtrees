<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCgg;

/**
 * Class LocaleCgg - Chiga
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCgg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Rukiga';
    }

    public function endonymSortable()
    {
        return 'RUKIGA';
    }

    public function language()
    {
        return new LanguageCgg();
    }
}
