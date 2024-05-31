<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKs - Representation of the Kashmiri language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ks';
    }

    public function defaultTerritory()
    {
        return new TerritoryIn();
    }

    public function defaultScript()
    {
        return new ScriptArab();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
