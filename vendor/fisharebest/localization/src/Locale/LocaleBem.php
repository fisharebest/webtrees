<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBem;

/**
 * Class LocaleBem - Bemba
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBem extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ichibemba';
    }

    public function endonymSortable()
    {
        return 'ICHIBEMBA';
    }

    public function language()
    {
        return new LanguageBem();
    }
}
