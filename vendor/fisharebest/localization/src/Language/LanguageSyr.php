<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptSyrc;
use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LanguageSyr - Representation of the Syriac language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSyr extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'syr';
    }

    public function defaultScript()
    {
        return new ScriptSyrc();
    }

    public function defaultTerritory()
    {
        return new TerritoryIq();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
