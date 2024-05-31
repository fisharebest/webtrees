<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageMai - Representation of the Maithili language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMai extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mai';
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
