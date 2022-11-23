<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptArmn;
use Fisharebest\Localization\Territory\TerritoryAm;

/**
 * Class LanguageHy - Representation of the Armenian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageHy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'hy';
    }

    public function defaultScript()
    {
        return new ScriptArmn();
    }

    public function defaultTerritory()
    {
        return new TerritoryAm();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
