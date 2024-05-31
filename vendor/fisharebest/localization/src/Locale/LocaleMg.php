<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMg;

/**
 * Class LocaleMg - Malagasy
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMg extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Malagasy';
    }

    public function endonymSortable()
    {
        return 'MALAGASY';
    }

    public function language()
    {
        return new LanguageMg();
    }
}
