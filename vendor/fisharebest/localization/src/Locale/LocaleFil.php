<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFil;

/**
 * Class LocaleFil - Filipino
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFil extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Filipino';
    }

    public function endonymSortable()
    {
        return 'FILIPINO';
    }

    public function language()
    {
        return new LanguageFil();
    }
}
