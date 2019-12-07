<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

/**
 * Controller for the fan chart
 */
class FanchartController extends ChartController
{
    /** @var int Style of fanchart */
    public $fan_style;

    /** @var int Width of fanchart (a percentage)  */
    public $fan_width;

    /** @var int Number of generations to display */
    public $generations;

    /**
     * Create the controller
     */
    public function __construct()
    {
        global $WT_TREE;

        parent::__construct();

        $default_generations = $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS');

        // Extract the request parameters
        $this->fan_style   = Filter::getInteger('fan_style', 2, 4, 3);
        $this->fan_width   = Filter::getInteger('fan_width', 50, 500, 100);
        $this->generations = Filter::getInteger('generations', 2, 9, $default_generations);

        if ($this->root && $this->root->canShowName()) {
            $this->setPageTitle(
                /* I18N: http://en.wikipedia.org/wiki/Family_tree#Fan_chart - %s is an individual’s name */
                I18N::translate('Fan chart of %s', $this->root->getFullName())
            );
        } else {
            $this->setPageTitle(I18N::translate('Fan chart'));
        }
    }

    /**
     * A list of options for the chart style.
     *
     * @return string[]
     */
    public function getFanStyles()
    {
        return array(
            2 => /* I18N: layout option for the fan chart */ I18N::translate('half circle'),
            3 => /* I18N: layout option for the fan chart */ I18N::translate('three-quarter circle'),
            4 => /* I18N: layout option for the fan chart */ I18N::translate('full circle'),
        );
    }

    /**
     * split and center text by lines
     *
     * @param string $data input string
     * @param int    $maxlen max length of each line
     *
     * @return string $text output string
     */
    public function splitAlignText($data, $maxlen)
    {
        $RTLOrd = array(215, 216, 217, 218, 219);

        $lines = explode("\n", $data);
        // more than 1 line : recursive calls
        if (count($lines) > 1) {
            $text = '';
            foreach ($lines as $line) {
                $text .= $this->splitAlignText($line, $maxlen) . "\n";
            }

            return $text;
        }
        // process current line word by word
        $split = explode(' ', $data);
        $text  = '';
        $line  = '';

        // do not split hebrew line
        $found = false;
        foreach ($RTLOrd as $ord) {
            if (strpos($data, chr($ord)) !== false) {
                $found = true;
            }
        }
        if ($found) {
            $line = $data;
        } else {
            foreach ($split as $word) {
                $len  = strlen($line);
                $wlen = strlen($word);
                if (($len + $wlen) < $maxlen) {
                    if (!empty($line)) {
                        $line .= ' ';
                    }
                    $line .= "$word";
                } else {
                    $p = max(0, (int) (($maxlen - $len) / 2));
                    if (!empty($line)) {
                        $line = str_repeat(' ', $p) . $line; // center alignment using spaces
                        $text .= $line . "\n";
                    }
                    $line = $word;
                }
            }
        }
        // last line
        if (!empty($line)) {
            $len = strlen($line);
            if (in_array(ord($line[0]), $RTLOrd)) {
                $len /= 2;
            }
            $p    = max(0, (int) (($maxlen - $len) / 2));
            $line = str_repeat(' ', $p) . $line; // center alignment using spaces
            $text .= $line;
        }

        return $text;
    }

    /**
     * Generate both the HTML and PNG components of the fan chart
     *
     * The HTML and PNG components both require the same co-ordinate calculations,
     * so we generate them using the same code, but we send them in separate
     * HTTP requests.
     *
     * @param string $what "png" or "html"
     *
     * @return string
     */
    public function generateFanChart($what)
    {
        $treeid = $this->sosaAncestors($this->generations);
        $fanw   = 640 * $this->fan_width / 100;
        $fandeg = 90 * $this->fan_style;
        $html   = '';

        $treesize = count($treeid) + 1;

        // generations count
        $gen  = log($treesize) / log(2) - 1;
        $sosa = $treesize - 1;

        // fan size
        if ($fandeg == 0) {
            $fandeg = 360;
        }
        $fandeg = min($fandeg, 360);
        $fandeg = max($fandeg, 90);
        $cx     = $fanw / 2 - 1; // center x
        $cy     = $cx; // center y
        $rx     = $fanw - 1;
        $rw     = $fanw / ($gen + 1);
        $fanh   = $fanw; // fan height
        if ($fandeg == 180) {
            $fanh = round($fanh * ($gen + 1) / ($gen * 2));
        }
        if ($fandeg == 270) {
            $fanh = round($fanh * 0.86);
        }
        $scale = $fanw / 640;

        // image init
        $image = imagecreate($fanw, $fanh);
        $white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        imagefilledrectangle($image, 0, 0, $fanw, $fanh, $white);
        imagecolortransparent($image, $white);

        $color = imagecolorallocate(
            $image,
            hexdec(substr(Theme::theme()->parameter('chart-font-color'), 0, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-font-color'), 2, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-font-color'), 4, 2)));
        $bgcolor = imagecolorallocate(
            $image,
            hexdec(substr(Theme::theme()->parameter('chart-background-u'), 0, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-u'), 2, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-u'), 4, 2))
        );
        $bgcolorM = imagecolorallocate(
            $image,
            hexdec(substr(Theme::theme()->parameter('chart-background-m'), 0, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-m'), 2, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-m'), 4, 2))
        );
        $bgcolorF = imagecolorallocate(
            $image,
            hexdec(substr(Theme::theme()->parameter('chart-background-f'), 0, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-f'), 2, 2)),
            hexdec(substr(Theme::theme()->parameter('chart-background-f'), 4, 2))
        );

        // imagemap
        $imagemap = '<map id="fanmap" name="fanmap">';

        // loop to create fan cells
        while ($gen >= 0) {
            // clean current generation area
            $deg2 = 360 + ($fandeg - 180) / 2;
            $deg1 = $deg2 - $fandeg;
            imagefilledarc($image, $cx, $cy, $rx, $rx, $deg1, $deg2, $bgcolor, IMG_ARC_PIE);
            $rx -= 3;

            // calculate new angle
            $p2    = pow(2, $gen);
            $angle = $fandeg / $p2;
            $deg2  = 360 + ($fandeg - 180) / 2;
            $deg1  = $deg2 - $angle;
            // special case for rootid cell
            if ($gen == 0) {
                $deg1 = 90;
                $deg2 = 360 + $deg1;
            }

            // draw each cell
            while ($sosa >= $p2) {
                $person = $treeid[$sosa];
                if ($person) {
                    $name    = $person->getFullName();
                    $addname = $person->getAddName();

                    $text = I18N::reverseText($name);
                    if ($addname) {
                        $text .= "\n" . I18N::reverseText($addname);
                    }

                    $text .= "\n" . I18N::reverseText($person->getLifeSpan());

                    switch ($person->getSex()) {
                        case 'M':
                            $bg = $bgcolorM;
                            break;
                        case 'F':
                            $bg = $bgcolorF;
                            break;
                        default:
                            $bg = $bgcolor;
                            break;
                    }

                    imagefilledarc($image, $cx, $cy, $rx, $rx, $deg1, $deg2, $bg, IMG_ARC_PIE);

                    // split and center text by lines
                    $wmax = (int) ($angle * 7 / Theme::theme()->parameter('chart-font-size') * $scale);
                    $wmax = min($wmax, 35 * $scale);
                    if ($gen == 0) {
                        $wmax = min($wmax, 17 * $scale);
                    }
                    $text = $this->splitAlignText($text, $wmax);

                    // text angle
                    $tangle = 270 - ($deg1 + $angle / 2);
                    if ($gen == 0) {
                        $tangle = 0;
                    }

                    // calculate text position
                    $deg = $deg1 + 0.44;
                    if ($deg2 - $deg1 > 40) {
                        $deg = $deg1 + ($deg2 - $deg1) / 11;
                    }
                    if ($deg2 - $deg1 > 80) {
                        $deg = $deg1 + ($deg2 - $deg1) / 7;
                    }
                    if ($deg2 - $deg1 > 140) {
                        $deg = $deg1 + ($deg2 - $deg1) / 4;
                    }
                    if ($gen == 0) {
                        $deg = 180;
                    }
                    $rad = deg2rad($deg);
                    $mr  = ($rx - $rw / 4) / 2;
                    if ($gen > 0 && $deg2 - $deg1 > 80) {
                        $mr = $rx / 2;
                    }
                    $tx = $cx + $mr * cos($rad);
                    $ty = $cy - $mr * -sin($rad);
                    if ($sosa == 1) {
                        $ty -= $mr / 2;
                    }

                    // print text
                    imagettftext(
                        $image,
                        Theme::theme()->parameter('chart-font-size'),
                        $tangle, $tx, $ty,
                        $color, Theme::theme()->parameter('chart-font-name'),
                        $text
                    );

                    $imagemap .= '<area shape="poly" coords="';
                    // plot upper points
                    $mr  = $rx / 2;
                    $deg = $deg1;
                    while ($deg <= $deg2) {
                        $rad = deg2rad($deg);
                        $tx  = round($cx + $mr * cos($rad));
                        $ty  = round($cy - $mr * -sin($rad));
                        $imagemap .= "$tx,$ty,";
                        $deg += ($deg2 - $deg1) / 6;
                    }
                    // plot lower points
                    $mr  = ($rx - $rw) / 2;
                    $deg = $deg2;
                    while ($deg >= $deg1) {
                        $rad = deg2rad($deg);
                        $tx  = round($cx + $mr * cos($rad));
                        $ty  = round($cy - $mr * -sin($rad));
                        $imagemap .= "$tx,$ty,";
                        $deg -= ($deg2 - $deg1) / 6;
                    }
                    // join first point
                    $mr  = $rx / 2;
                    $deg = $deg1;
                    $rad = deg2rad($deg);
                    $tx  = round($cx + $mr * cos($rad));
                    $ty  = round($cy - $mr * -sin($rad));
                    $imagemap .= "$tx,$ty";
                    // add action url
                    $pid = $person->getXref();
                    $imagemap .= '" href="#' . $pid . '"';
                    $html .= '<div id="' . $pid . '" class="fan_chart_menu">';
                    $html .= '<div class="person_box"><div class="details1">';
                    $html .= '<a href="' . $person->getHtmlUrl() . '" class="name1">' . $name;
                    if ($addname) {
                        $html .= $addname;
                    }
                    $html .= '</a>';
                    $html .= '<ul class="charts">';
                    foreach (Theme::theme()->individualBoxMenu($person) as $menu) {
                        $html .= $menu->getMenuAsList();
                    }
                    $html .= '</ul>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $imagemap .= ' alt="' . strip_tags($person->getFullName()) . '" title="' . strip_tags($person->getFullName()) . '">';
                }
                $deg1 -= $angle;
                $deg2 -= $angle;
                $sosa--;
            }
            $rx -= $rw;
            $gen--;
        }

        $imagemap .= '</map>';

        switch ($what) {
            case 'html':
                return $html . $imagemap . '<div id="fan_chart_img"><img src="' . WT_SCRIPT_NAME . '?rootid=' . $this->root->getXref() . '&amp;fan_style=' . $this->fan_style . '&amp;generations=' . $this->generations . '&amp;fan_width=' . $this->fan_width . '&amp;img=1" width="' . $fanw . '" height="' . $fanh . '" alt="' . strip_tags($this->getPageTitle()) . '" usemap="#fanmap"></div>';

            case 'png':
                imagestringup($image, 1, $fanw - 10, $fanh / 3, WT_BASE_URL, $color);
                ob_start();
                imagepng($image);
                imagedestroy($image);

                return ob_get_clean();

            default:
                throw new \InvalidArgumentException(__METHOD__ . ' ' . $what);
        }
    }
}
