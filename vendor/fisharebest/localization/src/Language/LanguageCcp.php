<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptCakm;
use Fisharebest\Localization\Territory\TerritoryBd;

/**
 * Class LanguageCcp - Representation of the Chakma language.
 *
 * @TODO          Plural rules
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageCcp extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ccp';
    }

    public function defaultTerritory()
    {
        return new TerritoryBd();
    }

    public function defaultScript()
    {
        return new ScriptCakm();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
