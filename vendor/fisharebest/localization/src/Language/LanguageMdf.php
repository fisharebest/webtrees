<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageCv - Representation of the Chuvash language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMdf extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mdf';
    }

    public function defaultScript()
    {
        return new ScriptCyrl();
    }

    public function defaultTerritory()
    {
        return new TerritoryRu();
    }
}
