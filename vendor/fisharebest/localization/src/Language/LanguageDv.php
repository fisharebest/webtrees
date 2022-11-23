<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptThaa;
use Fisharebest\Localization\Territory\TerritoryMv;

/**
 * Class LanguageDv - Representation of the Divehi language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageDv extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'dv';
    }

    public function defaultScript()
    {
        return new ScriptThaa();
    }

    public function defaultTerritory()
    {
        return new TerritoryMv();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
