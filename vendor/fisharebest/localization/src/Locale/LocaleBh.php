<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBh;

/**
 * Class LocaleBh - Bihari
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBh extends AbstractLocale implements LocaleInterface
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function endonym()
    {
        return 'Bihari';
    }

    public function endonymSortable()
    {
        return 'BIHARI';
    }

    public function language()
    {
        return new LanguageBh();
    }
}
