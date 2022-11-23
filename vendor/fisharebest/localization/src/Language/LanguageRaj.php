<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageRaj - Representation of the Rajastani language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageRaj extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'raj';
    }

    public function defaultScript()
    {
        return new ScriptDeva();
    }

    public function defaultTerritory()
    {
        return new TerritoryIn();
    }
}
