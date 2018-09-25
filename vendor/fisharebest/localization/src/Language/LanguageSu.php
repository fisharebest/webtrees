<?php namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptSund;
use Fisharebest\Localization\Territory\TerritoryId;

/**
 * Class LanguageSu - Representation of the Sotho language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LanguageSu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'su';
    }

    public function defaultScript()
    {
        return new ScriptSund();
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
