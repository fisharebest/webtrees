<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEe;

/**
 * Class LocaleEe - Ewe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEe extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Eʋegbe';
    }

    public function endonymSortable()
    {
        return 'EWEGBE';
    }

    public function language()
    {
        return new LanguageEe();
    }
}
