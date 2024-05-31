<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptTibt;
use Fisharebest\Localization\Territory\TerritoryBt;

/**
 * Class LanguageDz - Representation of the Dzongkha language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageDz extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'dz';
    }

    public function defaultScript()
    {
        return new ScriptTibt();
    }

    public function defaultTerritory()
    {
        return new TerritoryBt();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
