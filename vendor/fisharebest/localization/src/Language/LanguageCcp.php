<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptCakm;
use Fisharebest\Localization\Territory\TerritoryBd;

/**
 * Class LanguageCgg - Representation of the Chakma language.
 *
 * @TODO          Plural rules
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageCcp extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ccp';
    }

    public function defaultTerritory()
    {
        return new TerritoryBd();
    }

    public function defaultScript()
    {
        return new ScriptCakm();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
