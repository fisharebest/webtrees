<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCe;

/**
 * Class LocaleCe - Chechen
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCe extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'нохчийн';
    }

    public function endonymSortable()
    {
        return 'НОХЧИЙН';
    }

    public function language()
    {
        return new LanguageCe();
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::NBSP . self::PERCENT;
    }
}
