<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule12;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LanguageMzn - Representation of the Mazanderani language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMzn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mzn';
    }

    public function defaultScript()
    {
        return new ScriptArab();
    }

    public function defaultTerritory()
    {
        return new TerritoryIr();
    }

    public function pluralRule()
    {
        return new PluralRule12();
    }
}
