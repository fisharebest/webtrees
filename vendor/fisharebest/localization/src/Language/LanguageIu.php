<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Script\ScriptCans;
use Fisharebest\Localization\Territory\TerritoryCa;

/**
 * Class LanguageIu - Representation of the Inuktitut language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'iu';
    }

    public function defaultScript()
    {
        return new ScriptCans();
    }

    public function defaultTerritory()
    {
        return new TerritoryCa();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
