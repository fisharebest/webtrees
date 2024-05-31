<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageQu;

/**
 * Class LocaleQu - Quechua
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleQu extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Runasimi';
    }

    public function endonymSortable()
    {
        return 'RUNASIMI';
    }

    public function language()
    {
        return new LanguageQu();
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
