<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptLaoo;
use Fisharebest\Localization\Territory\TerritoryLa;

/**
 * Class LanguageLo - Representation of the Lao language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageLo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'lo';
    }

    public function defaultScript()
    {
        return new ScriptLaoo();
    }

    public function defaultTerritory()
    {
        return new TerritoryLa();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
