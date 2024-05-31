<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptEthi;
use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LanguageAm - Representation of the Amharic language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAm extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'am';
    }

    public function defaultScript()
    {
        return new ScriptEthi();
    }

    public function defaultTerritory()
    {
        return new TerritoryEt();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
