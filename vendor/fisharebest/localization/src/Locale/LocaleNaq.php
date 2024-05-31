<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNaq;

/**
 * Class LocaleNaq - Nama
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNaq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Khoekhoegowab';
    }

    public function endonymSortable()
    {
        return 'KHOEKHOEGOWAB';
    }

    public function language()
    {
        return new LanguageNaq();
    }
}
