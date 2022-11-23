<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptGeor;
use Fisharebest\Localization\Territory\TerritoryGe;

/**
 * Class LanguageKa - Representation of the Georgian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKa extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ka';
    }

    public function defaultScript()
    {
        return new ScriptGeor();
    }

    public function defaultTerritory()
    {
        return new TerritoryGe();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
