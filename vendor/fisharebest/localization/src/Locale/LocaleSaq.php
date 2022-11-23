<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSaq;

/**
 * Class LocaleSaq - Samburu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSaq extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'Kisampur';
    }

    public function endonymSortable()
    {
        return 'KISAMPUR';
    }

    public function language()
    {
        return new LanguageSaq();
    }
}
