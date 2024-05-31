<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptBrah - Representation of the Brahmi script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptBrah extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Brah';
    }

    public function numerals()
    {
        return array('𑁦', '𑁧', '𑁨', '𑁩', '𑁪', '𑁫', '𑁬', '𑁭', '𑁮', '𑁯');
    }

    public function number()
    {
        return '300';
    }

    public function unicodeName()
    {
        return 'Brahmi';
    }
}
