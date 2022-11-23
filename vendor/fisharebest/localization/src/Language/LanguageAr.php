<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule12;
use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LanguageAr - Representation of the Arabic language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAr extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ar';
    }

    public function defaultScript()
    {
        return new ScriptArab();
    }

    public function pluralRule()
    {
        return new PluralRule12();
    }
}
