<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIo;

/**
 * Class LocaleIo - Ido
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleIo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Ido';
    }

    public function endonymSortable()
    {
        return 'IDO';
    }

    public function language()
    {
        return new LanguageIo();
    }
}
