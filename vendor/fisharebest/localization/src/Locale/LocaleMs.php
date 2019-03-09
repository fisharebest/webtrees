<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMs;

/**
 * Class LocaleMs - Malay
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleMs extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Melayu';
    }

    public function endonymSortable()
    {
        return 'MELAYU';
    }

    public function language()
    {
        return new LanguageMs();
    }
}
