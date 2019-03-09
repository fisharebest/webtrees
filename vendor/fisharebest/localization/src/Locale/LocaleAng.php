<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAng;

/**
 * Class LocaleAng - Anglo-Saxon / Old-English
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleAng extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ænglisc';
    }

    public function endonymSortable()
    {
        return 'ÆNGLISC';
    }

    public function language()
    {
        return new LanguageAng();
    }
}
