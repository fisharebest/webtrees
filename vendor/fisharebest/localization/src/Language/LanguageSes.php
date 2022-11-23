<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LanguageSes - Representation of the Koyraboro Senni Songhai language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSes extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ses';
    }

    public function defaultTerritory()
    {
        return new TerritoryMl();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
