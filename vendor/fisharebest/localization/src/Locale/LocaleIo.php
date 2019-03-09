<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIo;

/**
 * Class LocaleIo - Ido
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
