<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A chart of direct-line ancestors in a circular layour.
 */
class FanChartController extends AbstractChartController {
	// Chart styles
	const STYLE_HALF_CIRCLE          = 2;
	const STYLE_THREE_QUARTER_CIRCLE = 3;
	const STYLE_FULL_CIRCLE          = 4;

	// Limits
	const MINIMUM_GENERATIONS = 2;
	const MAXIMUM_GENERATIONS = 9;
	const MINIMUM_WIDTH       = 50;
	const MAXIMUM_WIDTH       = 500;

	// Defaults
	const DEFAULT_STYLE       = self::STYLE_THREE_QUARTER_CIRCLE;
	const DEFAULT_GENERATIONS = 4;
	const DEFAULT_WIDTH = 100;

	/**
	 * A form to request the chart parameters.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function page(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'fan_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$chart_style = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$fan_width   = (int) $request->get('fan_width', self::DEFAULT_WIDTH);
		$generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

		$fan_width = min($fan_width, self::MAXIMUM_WIDTH);
		$fan_width = max($fan_width, self::MINIMUM_WIDTH);

		$generations = min($generations, self::MAXIMUM_GENERATIONS);
		$generations = max($generations, self::MINIMUM_GENERATIONS);

		$title = /* I18N: http://en.wikipedia.org/wiki/Family_tree#Fan_chart - %s is an individual’s name */ I18N::translate('Fan chart of %s', $individual->getFullName());

		return $this->viewResponse('fan-page', [
			'chart_style'         => $chart_style,
			'chart_styles'        => $this->chartStyles(),
			'fan_width'           => $fan_width,
			'generations'         => $generations,
			'individual'          => $individual,
			'maximum_generations' => self::MAXIMUM_GENERATIONS,
			'minimum_generations' => self::MINIMUM_GENERATIONS,
			'maximum_width'       => self::MAXIMUM_WIDTH,
			'minimum_width'       => self::MINIMUM_WIDTH,
			'title'               => $title,
		]);
	}

	/**
	 * Generate both the HTML and PNG components of the fan chart
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function chart(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$this->checkModuleIsActive($tree, 'fan_chart');

		$xref       = $request->get('xref');
		$individual = Individual::getInstance($xref, $tree);

		$this->checkIndividualAccess($individual);

		$chart_style = (int) $request->get('chart_style', self::DEFAULT_STYLE);
		$fan_width   = (int) $request->get('fan_width', self::DEFAULT_WIDTH);
		$generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

		$fan_width = min($fan_width, self::MAXIMUM_WIDTH);
		$fan_width = max($fan_width, self::MINIMUM_WIDTH);

		$generations = min($generations, self::MAXIMUM_GENERATIONS);
		$generations = max($generations, self::MINIMUM_GENERATIONS);

		$ancestors = $this->sosaStradonitzAncestors($individual, $generations);

		$gen  = $generations - 1;
		$sosa = count($ancestors);

		// fan size
		$fanw   = 640 * $fan_width / 100;
		$cx     = $fanw / 2 - 1; // center x
		$cy     = $cx; // center y
		$rx     = $fanw - 1;
		$rw     = $fanw / ($gen + 1);
		$fanh   = $fanw; // fan height
		if ($chart_style === self::STYLE_HALF_CIRCLE) {
			$fanh = $fanh * ($gen + 1) / ($gen * 2);
		}
		if ($chart_style === self::STYLE_THREE_QUARTER_CIRCLE) {
			$fanh = $fanh * 0.86;
		}
		$scale = $fanw / 640;

		// Create the image
		$image = imagecreate((int) $fanw, (int) $fanh);

		// Create colors
		$transparent = imagecolorallocate($image, 0, 0, 0);
		imagecolortransparent($image, $transparent);

		$theme = Theme::theme();

		$foreground  = $this->imageColor($image, $theme->parameter('chart-font-color'));

		$backgrounds = [
			'M' => $this->imageColor($image, $theme->parameter('chart-background-m')),
			'F' => $this->imageColor($image, $theme->parameter('chart-background-f')),
			'U' => $this->imageColor($image, $theme->parameter('chart-background-u')),
		];


		imagefilledrectangle($image, 0, 0, (int) $fanw, (int) $fanh, $transparent);

		$fandeg = 90 * $chart_style;

		// Popup menus for each ancestor
		$html   = '';

		// Areas for the imagemap
		$areas = '';

		// loop to create fan cells
		while ($gen >= 0) {
			// clean current generation area
			$deg2 = 360 + ($fandeg - 180) / 2;
			$deg1 = $deg2 - $fandeg;
			imagefilledarc($image, (int) $cx, (int) $cy, (int) $rx, (int) $rx, (int) $deg1, (int) $deg2, $backgrounds['U'], IMG_ARC_PIE);
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
				$person = $ancestors[$sosa];
				if ($person) {
					$name    = $person->getFullName();
					$addname = $person->getAddName();

					$text = I18N::reverseText($name);
					if ($addname) {
						$text .= "\n" . I18N::reverseText($addname);
					}

					$text .= "\n" . I18N::reverseText($person->getLifeSpan());

					$background = $backgrounds[$person->getSex()];

					imagefilledarc($image, (int) $cx, (int) $cy, (int) $rx, (int) $rx, (int) $deg1, (int) $deg2, $background, IMG_ARC_PIE);

					// split and center text by lines
					$wmax = (int) ($angle * 7 / 7 * $scale);
					$wmax = min($wmax, 35 * $scale);
					if ($gen == 0) {
						$wmax = min($wmax, 17 * $scale);
					}
					$text = $this->splitAlignText($text, (int) $wmax);

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
					$ty = $cy + $mr * sin($rad);
					if ($sosa == 1) {
						$ty -= $mr / 2;
					}

					// print text
					imagettftext(
						$image,
						7,
						$tangle, (int) $tx, (int) $ty,
						$foreground,
						WT_ROOT . 'resources/fonts/DejaVuSans.ttf',
						$text
					);

					$areas .= '<area shape="poly" coords="';
					// plot upper points
					$mr  = $rx / 2;
					$deg = $deg1;
					while ($deg <= $deg2) {
						$rad = deg2rad($deg);
						$tx  = round($cx + $mr * cos($rad));
						$ty  = round($cy + $mr * sin($rad));
						$areas .= "$tx,$ty,";
						$deg += ($deg2 - $deg1) / 6;
					}
					// plot lower points
					$mr  = ($rx - $rw) / 2;
					$deg = $deg2;
					while ($deg >= $deg1) {
						$rad = deg2rad($deg);
						$tx  = round($cx + $mr * cos($rad));
						$ty  = round($cy + $mr * sin($rad));
						$areas .= "$tx,$ty,";
						$deg -= ($deg2 - $deg1) / 6;
					}
					// join first point
					$mr  = $rx / 2;
					$deg = $deg1;
					$rad = deg2rad($deg);
					$tx  = round($cx + $mr * cos($rad));
					$ty  = round($cy + $mr * sin($rad));
					$areas .= "$tx,$ty";
					// add action url
					$areas .= '" href="#' . $person->getXref() . '"';
					$html .= '<div id="' . $person->getXref() . '" class="fan_chart_menu">';
					$html .= '<div class="person_box"><div class="details1">';
					$html .= '<a href="' . e($person->url()) . '" class="name1">' . $name;
					if ($addname) {
						$html .= $addname;
					}
					$html .= '</a>';
					$html .= '<ul class="charts">';
					foreach ($theme->individualBoxMenu($person) as $menu) {
						$html .= $menu->getMenuAsList();
					}
					$html .= '</ul>';
					$html .= '</div></div>';
					$html .= '</div>';
					$areas .= ' alt="' . strip_tags($person->getFullName()) . '" title="' . strip_tags($person->getFullName()) . '">';
				}
				$deg1 -= $angle;
				$deg2 -= $angle;
				$sosa--;
			}
			$rx -= $rw;
			$gen--;
		}

		ob_start();
		imagepng($image);
		imagedestroy($image);
		$png = ob_get_clean();

		$title = /* I18N: http://en.wikipedia.org/wiki/Family_tree#Fan_chart - %s is an individual’s name */ I18N::translate('Fan chart of %s', $individual->getFullName());

		return new Response(view('fan-chart', [
			'fanh'     => $fanh,
			'fanw'     => $fanw,
			'html'     => $html,
			'areas' => $areas,
			'png'      => $png,
			'title'    => $title,
		]));
	}

	/**
	 * split and center text by lines
	 *
	 * @param string $data input string
	 * @param int    $maxlen max length of each line
	 *
	 * @return string $text output string
	 */
	private function splitAlignText(string $data, int $maxlen): string {
		$RTLOrd = [215, 216, 217, 218, 219];

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
			if (in_array(ord($line{0}), $RTLOrd)) {
				$len /= 2;
			}
			$p    = max(0, (int) (($maxlen - $len) / 2));
			$line = str_repeat(' ', $p) . $line; // center alignment using spaces
			$text .= $line;
		}

		return $text;
	}

	/**
	 * Convert a CSS color into a GD color.
	 *
	 * @param resource $image
	 * @param string   $css_color
	 *
	 * @return int
	 */
	private function imageColor($image, string $css_color): int {
		return imagecolorallocate(
			$image,
			(int) hexdec(substr($css_color, 0, 2)),
			(int) hexdec(substr($css_color, 2, 2)),
			(int) hexdec(substr($css_color, 4, 2))
		);
	}

	/**
	 * This chart can display its output in a number of styles
	 *
	 * @return array
	 */
	private function chartStyles(): array {
		return [
			self::STYLE_HALF_CIRCLE          => /* I18N: layout option for the fan chart */ I18N::translate('half circle'),
			self::STYLE_THREE_QUARTER_CIRCLE => /* I18N: layout option for the fan chart */ I18N::translate('three-quarter circle'),
			self::STYLE_FULL_CIRCLE          => /* I18N: layout option for the fan chart */ I18N::translate('full circle'),
		];
	}
}
