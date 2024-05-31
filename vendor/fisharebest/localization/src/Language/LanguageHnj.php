<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptHmng;
use Fisharebest\Localization\Territory\TerritoryCn;

/**
 * Class LanguageHnj - Representation of the Hmong language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageHnj extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'hnj';
    }

    public function defaultScript()
    {
        return new ScriptHmng();
    }

    public function defaultTerritory()
    {
        return new TerritoryCn();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
