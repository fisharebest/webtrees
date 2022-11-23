<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule3;

/**
 * Class LanguagePrg - Representation of the Old Prussian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguagePrg extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'prg';
    }

    public function pluralRule()
    {
        return new PluralRule3();
    }
}
