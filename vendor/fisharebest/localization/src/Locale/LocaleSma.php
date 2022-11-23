<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSma;

/**
 * Class LocaleSma
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSma extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Åarjelsaemien gïele';
    }

    public function endonymSortable()
    {
        return 'AARJELSAMIEN GIELE';
    }

    public function language()
    {
        return new LanguageSma();
    }
}
