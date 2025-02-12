<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function array_filter;
use function array_map;
use function cos;
use function deg2rad;
use function e;
use function gd_info;
use function hexdec;
use function imagecolorallocate;
use function imagecolortransparent;
use function imagecreate;
use function imagedestroy;
use function imagefilledarc;
use function imagefilledrectangle;
use function imagepng;
use function imagettfbbox;
use function imagettftext;
use function implode;
use function intdiv;
use function mb_substr;
use function ob_get_clean;
use function ob_start;
use function redirect;
use function response;
use function round;
use function route;
use function rtrim;
use function sin;
use function sqrt;
use function strip_tags;
use function substr;
use function view;

use const IMG_ARC_PIE;

/**
 * Class FanChartModule
 */
class FanChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL = '/tree/{tree}/fan-chart-{style}-{generations}-{width}/{xref}';

    // Chart styles
    private const STYLE_HALF_CIRCLE          = 2;
    private const STYLE_THREE_QUARTER_CIRCLE = 3;
    private const STYLE_FULL_CIRCLE          = 4;

    // Defaults
    public const    DEFAULT_STYLE       = self::STYLE_THREE_QUARTER_CIRCLE;
    public const    DEFAULT_GENERATIONS = 4;
    public const    DEFAULT_WIDTH       = 100;
    protected const DEFAULT_PARAMETERS  = [
        'style'       => self::DEFAULT_STYLE,
        'generations' => self::DEFAULT_GENERATIONS,
        'width'       => self::DEFAULT_WIDTH,
    ];

    // Limits
    private const MINIMUM_GENERATIONS = 2;
    private const MAXIMUM_GENERATIONS = 9;
    private const MINIMUM_WIDTH       = 50;
    private const MAXIMUM_WIDTH       = 500;

    // Chart layout parameters
    private const FONT               = Webtrees::ROOT_DIR . 'resources/fonts/DejaVuSans.ttf';
    private const CHART_WIDTH_PIXELS = 800;
    private const TEXT_SIZE_POINTS   = self::CHART_WIDTH_PIXELS / 120.0;
    private const GAP_BETWEEN_RINGS  = 2;

    private ChartService $chart_service;

    /**
     * @param ChartService $chart_service
     */
    public function __construct(ChartService $chart_service)
    {
        $this->chart_service = $chart_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this)
            ->allows(RequestMethodInterface::METHOD_POST);
    }

    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Fan chart');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Fan Chart” module */
        return I18N::translate('A fan chart of an individual’s ancestors.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-fanchart';
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        /* I18N: https://en.wikipedia.org/wiki/Family_tree#Fan_chart - %s is an individual’s name */
        return I18N::translate('Fan chart of %s', $individual->fullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param Individual                                $individual
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'xref' => $individual->xref(),
                'tree' => $individual->tree()->name(),
            ] + $parameters + self::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $user        = Validator::attributes($request)->user();
        $xref        = Validator::attributes($request)->isXref()->string('xref');
        $style       = Validator::attributes($request)->isInArrayKeys($this->styles())->integer('style');
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $width       = Validator::attributes($request)->isBetween(self::MINIMUM_WIDTH, self::MAXIMUM_WIDTH)->integer('width');
        $ajax        = Validator::queryParams($request)->boolean('ajax', false);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'generations' => Validator::parsedBody($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations'),
                'style'       => Validator::parsedBody($request)->isInArrayKeys($this->styles())->integer('style'),
                'width'       => Validator::parsedBody($request)->isBetween(self::MINIMUM_WIDTH, self::MAXIMUM_WIDTH)->integer('width'),
                'xref'        => Validator::parsedBody($request)->isXref()->string('xref'),
             ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        if ($ajax) {
            return $this->chart($individual, $style, $width, $generations);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'style'       => $style,
            'width'       => $width,
        ]);

        return $this->viewResponse('modules/fanchart/page', [
            'ajax_url'            => $ajax_url,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'maximum_width'       => self::MAXIMUM_WIDTH,
            'minimum_width'       => self::MINIMUM_WIDTH,
            'module'              => $this->name(),
            'style'               => $style,
            'styles'              => $this->styles(),
            'title'               => $this->chartTitle($individual),
            'tree'                => $tree,
            'width'               => $width,
        ]);
    }

    /**
     * Generate both the HTML and PNG components of the fan chart
     *
     * @param Individual $individual
     * @param int        $style
     * @param int        $width
     * @param int        $generations
     *
     * @return ResponseInterface
     */
    protected function chart(Individual $individual, int $style, int $width, int $generations): ResponseInterface
    {
        $ancestors = $this->chart_service->sosaStradonitzAncestors($individual, $generations);

        $width = intdiv(self::CHART_WIDTH_PIXELS * $width, 100);

        switch ($style) {
            case self::STYLE_HALF_CIRCLE:
                $chart_start_angle = 180;
                $chart_end_angle   = 360;
                $height            = intdiv($width, 2);
                break;

            case self::STYLE_THREE_QUARTER_CIRCLE:
                $chart_start_angle = 135;
                $chart_end_angle   = 405;
                $height            = intdiv($width * 86, 100);
                break;

            case self::STYLE_FULL_CIRCLE:
            default:
                $chart_start_angle = 90;
                $chart_end_angle   = 450;
                $height            = $width;
                break;
        }

        // Start with a transparent image.
        $image       = imagecreate($width, $height);
        $transparent = imagecolorallocate($image, 0, 0, 0);
        imagecolortransparent($image, $transparent);
        imagefilledrectangle($image, 0, 0, $width, $height, $transparent);

        // Use theme-specified colors.
        /** @var ModuleThemeInterface $theme */
        $theme       = app(ModuleThemeInterface::class);
        $text_color  = $this->imageColor($image, '000000');
        $backgrounds = [
            'M' => $this->imageColor($image, 'b1cff0'),
            'F' => $this->imageColor($image, 'e9daf1'),
            'U' => $this->imageColor($image, 'eeeeee'),
        ];

        // Co-ordinates are measured from the top-left corner.
        $center_x  = intdiv($width, 2);
        $center_y  = $center_x;
        $arc_width = $width / $generations / 2.0;

        // Popup menus for each ancestor.
        $html = '';

        // Areas for the image map.
        $areas = '';

        for ($generation = $generations; $generation >= 1; $generation--) {
            // Which ancestors to include in this ring. 1, 2-3, 4-7, 8-15, 16-31, etc.
            // The end of the range is also the number of ancestors in the ring.
            $sosa_start = 2 ** $generation - 1;
            $sosa_end   = 2 ** ($generation - 1);

            $arc_diameter = intdiv($width * $generation, $generations);
            $arc_radius = $arc_diameter / 2;

            // Draw an empty background, for missing ancestors.
            imagefilledarc(
                $image,
                $center_x,
                $center_y,
                $arc_diameter,
                $arc_diameter,
                $chart_start_angle,
                $chart_end_angle,
                $backgrounds['U'],
                IMG_ARC_PIE
            );

            $arc_diameter -= 2 * self::GAP_BETWEEN_RINGS;

            for ($sosa = $sosa_start; $sosa >= $sosa_end; $sosa--) {
                if ($ancestors->has($sosa)) {
                    $individual = $ancestors->get($sosa);

                    $chart_angle = $chart_end_angle - $chart_start_angle;
                    $start_angle = $chart_start_angle + intdiv($chart_angle * ($sosa - $sosa_end), $sosa_end);
                    $end_angle   = $chart_start_angle + intdiv($chart_angle * ($sosa - $sosa_end + 1), $sosa_end);
                    $angle       = $end_angle - $start_angle;

                    imagefilledarc(
                        $image,
                        $center_x,
                        $center_y,
                        $arc_diameter,
                        $arc_diameter,
                        $start_angle,
                        $end_angle,
                        $backgrounds[$individual->sex()] ?? $backgrounds['U'],
                        IMG_ARC_PIE
                    );

                    // Text is written at a tangent to the arc.
                    $text_angle = 270.0 - ($start_angle + $end_angle) / 2.0;

                    $text_radius = $arc_diameter / 2.0 - $arc_width * 0.25;

                    // Don't draw text right up to the edge of the arc.
                    if ($angle === 360) {
                        $delta = 90;
                    } elseif ($angle === 180) {
                        if ($generation === 1) {
                            $delta = 20;
                        } else {
                            $delta = 60;
                        }
                    } elseif ($angle > 120) {
                        $delta = 45;
                    } elseif ($angle > 60) {
                        $delta = 15;
                    } else {
                        $delta = 1;
                    }

                    $tx_start = $center_x + $text_radius * cos(deg2rad($start_angle + $delta));
                    $ty_start = $center_y + $text_radius * sin(deg2rad($start_angle + $delta));
                    $tx_end   = $center_x + $text_radius * cos(deg2rad($end_angle - $delta));
                    $ty_end   = $center_y + $text_radius * sin(deg2rad($end_angle - $delta));

                    $max_text_length = (int) sqrt(($tx_end - $tx_start) ** 2 + ($ty_end - $ty_start) ** 2);

                    $text_lines = array_filter([
                        I18N::reverseText($individual->fullName()),
                        I18N::reverseText($individual->alternateName() ?? ''),
                        I18N::reverseText($individual->lifespan()),
                    ]);

                    $text_lines = array_map(
                        fn (string $line): string => $this->fitTextToPixelWidth($line, $max_text_length),
                        $text_lines
                    );

                    $text = implode("\n", $text_lines);

                    if ($generation === 1) {
                        $ty_start -= $text_radius / 2;
                    }

                    // If PHP is compiled with --enable-gd-jis-conv, then the function
                    // imagettftext() is modified to expect EUC-JP encoding instead of UTF-8.
                    // Attempt to detect and convert...
                    if (gd_info()['JIS-mapped Japanese Font Support'] ?? false) {
                        $text = mb_convert_encoding($text, 'EUC-JP', 'UTF-8');
                    }

                    imagettftext(
                        $image,
                        self::TEXT_SIZE_POINTS,
                        $text_angle,
                        (int) $tx_start,
                        (int) $ty_start,
                        $text_color,
                        self::FONT,
                        $text
                    );
                    // Debug text positions by underlining first line of text
                    //imageline($image, (int) $tx_start, (int) $ty_start, (int) $tx_end, (int) $ty_end, $backgrounds['U']);

                    $areas .= '<area shape="poly" coords="';
                    for ($deg = $start_angle; $deg <= $end_angle; $deg++) {
                        $rad = deg2rad($deg);
                        $areas .= round($center_x + $arc_radius * cos($rad), 1) . ',';
                        $areas .= round($center_y + $arc_radius * sin($rad), 1) . ',';
                    }
                    for ($deg = $end_angle; $deg >= $start_angle; $deg--) {
                        $rad = deg2rad($deg);
                        $areas .= round($center_x + ($arc_radius - $arc_width) * cos($rad), 1) . ',';
                        $areas .= round($center_y + ($arc_radius - $arc_width) * sin($rad), 1) . ',';
                    }
                    $rad = deg2rad($start_angle);
                    $areas .= round($center_x + $arc_radius * cos($rad), 1) . ',';
                    $areas .= round($center_y + $arc_radius * sin($rad), 1) . '"';

                    $areas .= ' href="#' . e($individual->xref()) . '"';
                    $areas .= ' alt="' . strip_tags($individual->fullName()) . '"';
                    $areas .= ' title="' . strip_tags($individual->fullName()) . '">';

                    $html  .= '<div id="' . $individual->xref() . '" class="fan_chart_menu">';
                    $html  .= '<a href="' . e($individual->url()) . '" class="dropdown-item p-1">';
                    $html  .= $individual->fullName();
                    $html  .= '</a>';

                    foreach ($theme->individualBoxMenu($individual) as $menu) {
                        $link  = $menu->getLink();
                        $class = $menu->getClass();
                        $html .= '<a href="' . e($link) . '" class="dropdown-item p-1 ' . e($class) . '">';
                        $html .= $menu->getLabel();
                        $html .= '</a>';
                    }

                    $html .= '</div>';
                }
            }
        }

        ob_start();
        imagepng($image);
        imagedestroy($image);
        $png = ob_get_clean();

        return response(view('modules/fanchart/chart', [
            'fanh'  => $height,
            'fanw'  => $width,
            'html'  => $html,
            'areas' => $areas,
            'png'   => $png,
            'title' => $this->chartTitle($individual),
        ]));
    }

    /**
     * Convert a CSS color into a GD color.
     *
     * @param resource $image
     * @param string   $css_color
     *
     * @return int
     */
    protected function imageColor($image, string $css_color): int
    {
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
     * @return array<string>
     */
    protected function styles(): array
    {
        return [
            /* I18N: layout option for the fan chart */
            self::STYLE_HALF_CIRCLE          => I18N::translate('half circle'),
            /* I18N: layout option for the fan chart */
            self::STYLE_THREE_QUARTER_CIRCLE => I18N::translate('three-quarter circle'),
            /* I18N: layout option for the fan chart */
            self::STYLE_FULL_CIRCLE          => I18N::translate('full circle'),
        ];
    }

    /**
     * Fit text to a given number of pixels by either cropping to fit,
     * or adding spaces to center.
     *
     * @param string $text
     * @param int    $pixels
     *
     * @return string
     */
    protected function fitTextToPixelWidth(string $text, int $pixels): string
    {
        while ($this->textWidthInPixels($text) > $pixels) {
            $text = mb_substr($text, 0, -1);
        }

        while ($this->textWidthInPixels(' ' . $text . ' ') < $pixels) {
            $text = ' ' . $text . ' ';
        }

        // We only need the leading spaces.
        return rtrim($text);
    }

    /**
     * @param string $text
     *
     * @return int
     */
    protected function textWidthInPixels(string $text): int
    {
        $bounding_box = imagettfbbox(self::TEXT_SIZE_POINTS, 0, self::FONT, $text);

        return $bounding_box[4] - $bounding_box[0];
    }
}
