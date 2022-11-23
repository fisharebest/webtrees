<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Script\ScriptOlck;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageSat - Representation of the Santali language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSat extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sat';
    }

    public function defaultScript()
    {
        return new ScriptOlck();
    }

    public function defaultTerritory()
    {
        return new TerritoryIn();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
