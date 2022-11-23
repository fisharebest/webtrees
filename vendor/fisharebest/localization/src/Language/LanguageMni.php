<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptMtei;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageMai - Representation of the Meitei language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMni extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mni';
    }

    public function defaultScript()
    {
        return new ScriptMtei();
    }

    public function defaultTerritory()
    {
        return new TerritoryIn();
    }
}
