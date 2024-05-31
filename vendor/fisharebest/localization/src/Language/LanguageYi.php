<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptHebr;

/**
 * Class LanguageYi - Representation of the Yiddish language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageYi extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'yi';
    }

    public function defaultScript()
    {
        return new ScriptHebr();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
