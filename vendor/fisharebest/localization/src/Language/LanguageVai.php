<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptVaii;
use Fisharebest\Localization\Territory\TerritoryLr;

/**
 * Class LanguageVai - Representation of the Vai language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageVai extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'vai';
    }

    public function defaultScript()
    {
        return new ScriptVaii();
    }

    public function defaultTerritory()
    {
        return new TerritoryLr();
    }
}
