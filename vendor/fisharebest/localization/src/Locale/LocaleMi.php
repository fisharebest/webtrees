<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMi;

/**
 * Class LocaleDv - Divehi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMi extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Māori';
    }

    public function endonymSortable()
    {
        return 'MĀORI';
    }

    public function language()
    {
        return new LanguageMi();
    }
}
