<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptKore;
use Fisharebest\Localization\Territory\TerritoryKr;

/**
 * Class LanguageKo - Representation of the Korean language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ko';
    }

    public function defaultScript()
    {
        return new ScriptKore();
    }

    public function defaultTerritory()
    {
        return new TerritoryKr();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
