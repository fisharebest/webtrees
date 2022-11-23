<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCeb;

/**
 * Class LocaleCeb - Cebuano
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCeb extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Cebuano';
    }

    public function endonymSortable()
    {
        return 'CEBUANO';
    }

    public function language()
    {
        return new LanguageCeb();
    }
}
