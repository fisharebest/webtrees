<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryPk;

/**
 * Class LanguagePs - Representation of the Pushto language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguagePs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ps';
    }

    public function defaultScript()
    {
        return new ScriptArab();
    }

    public function defaultTerritory()
    {
        return new TerritoryPk();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
