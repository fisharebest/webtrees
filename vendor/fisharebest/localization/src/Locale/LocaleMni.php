<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMni;

/**
 * Class LocaleMni - Meitei
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMni extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'মৈতৈলোন্';
    }

    public function endonymSortable()
    {
        return 'মৈতৈলোন্';
    }

    public function language()
    {
        return new LanguageMni();
    }
}
