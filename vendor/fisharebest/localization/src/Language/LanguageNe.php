<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptDeva;
use Fisharebest\Localization\Territory\TerritoryNp;

/**
 * Class LanguageNe - Representation of the Nepali language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNe extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ne';
    }

    public function defaultScript()
    {
        return new ScriptDeva();
    }

    public function defaultTerritory()
    {
        return new TerritoryNp();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
