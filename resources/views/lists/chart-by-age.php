<?php use Fisharebest\Webtrees\I18N; ?>

<?php
$count  = 0;
$agemax = 0;
$vmax   = 0;
$avg    = 0;
foreach ($data as $age => $v) {
	$n      = strlen($v);
	$vmax   = max($vmax, $n);
	$agemax = max($agemax, $age);
	$count += $n;
	$avg += $age * $n;
}
if ($count < 1) {
	return '';
}
$avg       = round($avg / $count);
$chart_url = 'https://chart.googleapis.com/chart?cht=bvs'; // chart type
$chart_url .= '&amp;chs=725x150'; // size
$chart_url .= '&amp;chbh=3,2,2'; // bvg : 4,1,2
$chart_url .= '&amp;chf=bg,s,FFFFFF99'; //background color
$chart_url .= '&amp;chco=0000FF,FFA0CB,FF0000'; // bar color
$chart_url .= '&amp;chdl=' . rawurlencode(I18N::translate('Males')) . '|' . rawurlencode(I18N::translate('Females')) . '|' . rawurlencode(I18N::translate('Average age') . ': ' . $avg); // legend & average age
$chart_url .= '&amp;chtt=' . rawurlencode($title); // title
$chart_url .= '&amp;chxt=x,y,r'; // axis labels specification
$chart_url .= '&amp;chm=V,FF0000,0,' . ($avg - 0.3) . ',1'; // average age line marker
$chart_url .= '&amp;chxl=0:|'; // label
for ($age = 0; $age <= $agemax; $age += 5) {
	$chart_url .= $age . '|||||'; // x axis
}
$chart_url .= '|1:||' . rawurlencode(I18N::percentage($vmax / $count)); // y axis
$chart_url .= '|2:||';
$step = $vmax;
for ($d = $vmax; $d > 0; $d--) {
	if ($vmax < ($d * 10 + 1) && ($vmax % $d) == 0) {
		$step = $d;
	}
}
if ($step == $vmax) {
	for ($d = $vmax - 1; $d > 0; $d--) {
		if (($vmax - 1) < ($d * 10 + 1) && (($vmax - 1) % $d) == 0) {
			$step = $d;
		}
	}
}
for ($n = $step; $n < $vmax; $n += $step) {
	$chart_url .= $n . '|';
}
$chart_url .= rawurlencode($vmax . ' / ' . $count); // r axis
$chart_url .= '&amp;chg=100,' . round(100 * $step / $vmax, 1) . ',1,5'; // grid
$chart_url .= '&amp;chd=s:'; // data : simple encoding from A=0 to 9=61
$CHART_ENCODING61 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
for ($age = 0; $age <= $agemax; $age++) {
	$chart_url .= $CHART_ENCODING61[(int) (substr_count($data[$age], 'M') * 61 / $vmax)];
}
$chart_url .= ',';
for ($age = 0; $age <= $agemax; $age++) {
	$chart_url .= $CHART_ENCODING61[(int) (substr_count($data[$age], 'F') * 61 / $vmax)];
}
$html = '<img src="' . $chart_url . '" alt="' . $title . '" title="' . $title . '" class="gchart">';

echo $html;
