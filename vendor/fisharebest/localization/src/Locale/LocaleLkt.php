<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLkt;

/**
 * Class LocaleLkt - Lakota
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLkt extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Lakȟólʼiyapi';
    }

    public function endonymSortable()
    {
        return 'LAKHOLIYAPI';
    }

    public function language()
    {
        return new LanguageLkt();
    }
}
