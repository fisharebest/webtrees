<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguagePcm - Representation of the Nigerian Pidgin language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguagePcm extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'pcm';
    }

    public function defaultTerritory()
    {
        return new TerritoryNg();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
