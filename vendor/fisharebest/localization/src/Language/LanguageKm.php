<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptKhmr;
use Fisharebest\Localization\Territory\TerritoryKh;

/**
 * Class LanguageKm - Representation of the Central Khmer language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKm extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'km';
    }

    public function defaultScript()
    {
        return new ScriptKhmr();
    }

    public function defaultTerritory()
    {
        return new TerritoryKh();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
