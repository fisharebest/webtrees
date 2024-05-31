<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMai;
use Fisharebest\Localization\Script\ScriptDeva;

/**
 * Class LocaleMai - Maithili
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMai extends AbstractLocale implements LocaleInterface
{
    public function endonym()
    {
        return 'मैथिली';
    }

    public function endonymSortable()
    {
        return 'मैथिली';
    }

    public function language()
    {
        return new LanguageMai();
    }

    public function script()
    {
        return new ScriptDeva();
    }
}
