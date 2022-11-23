<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptHebr;
use Fisharebest\Localization\Territory\TerritoryIl;

/**
 * Class LanguageHe - Representation of the Hebrew language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageHe extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'he';
    }

    public function defaultScript()
    {
        return new ScriptHebr();
    }

    public function defaultTerritory()
    {
        return new TerritoryIl();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
