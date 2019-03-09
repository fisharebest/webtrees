<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageXh;

/**
 * Class LocaleXh - Xhosa
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleXh extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'isiXhosa';
    }

    public function endonymSortable()
    {
        return 'XHOSA';
    }

    public function language()
    {
        return new LanguageXh();
    }

    public function numberSymbols()
    {
        return array(
            self::GROUP => self::NBSP,
        );
    }
}
