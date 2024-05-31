<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMgo;

/**
 * Class LocaleMgo - Metaʼ
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMgo extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'metaʼ';
    }

    public function endonymSortable()
    {
        return 'META';
    }

    public function language()
    {
        return new LanguageMgo();
    }
}
