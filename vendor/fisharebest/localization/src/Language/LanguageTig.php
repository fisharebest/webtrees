<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptEthi;
use Fisharebest\Localization\Territory\TerritoryEr;

/**
 * Class LanguageTig - Representation of the Tigre language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageTig extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'tig';
    }

    public function defaultScript()
    {
        return new ScriptEthi();
    }

    public function defaultTerritory()
    {
        return new TerritoryEr();
    }

    public function pluralRule()
    {
        return new PluralRule1(); //
    }
}
