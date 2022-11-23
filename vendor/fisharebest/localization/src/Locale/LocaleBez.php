<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageBez;

/**
 * Class LocaleBez - Bena
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleBez extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Hibena';
    }

    public function endonymSortable()
    {
        return 'HIBENA';
    }

    public function language()
    {
        return new LanguageBez();
    }
}
