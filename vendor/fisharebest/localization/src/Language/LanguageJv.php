<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptJava;
use Fisharebest\Localization\Territory\TerritoryId;

/**
 * Class LanguageJv - Representation of the Javanese language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageJv extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'jv';
    }

    public function defaultScript()
    {
        return new ScriptJava();
    }

    public function defaultTerritory()
    {
        return new TerritoryId();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
