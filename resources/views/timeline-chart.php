<?php use Fisharebest\Webtrees\Date; ?>
<?php use Fisharebest\Webtrees\Family; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsDate; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<script>
  var bottomy     = <?= ($topyear - $baseyear) * $scale - 5 ?>;
  var topy        = 0;
  var baseyear    = <?= $baseyear - (25 / $scale) ?>;
  var birthyears  = [];
  var birthmonths = [];
  var birthdays   = [];
	<?php
	foreach ($individuals as $c => $indi) {
		if (!empty($birthyears[$indi->getXref()])) {
			echo 'birthyears[' . $c . ']=' . $birthyears[$indi->getXref()] . ';';
		}
		if (!empty($birthmonths[$indi->getXref()])) {
			echo 'birthmonths[' . $c . ']=' . $birthmonths[$indi->getXref()] . ';';
		}
		if (!empty($birthdays[$indi->getXref()])) {
			echo 'birthdays[' . $c . ']=' . $birthdays[$indi->getXref()] . ';';
		}
	}
	?>

  var bheight = <?= $bheight ?>;
  var scale   = <?= $scale ?>;

  timeline_chart_div              = document.getElementById("timeline_chart");
  timeline_chart_div.style.height = '<?= 0 + ($topyear - $baseyear) * $scale * 1.1 ?>px';

  /**
   * Find the position of an event, relative to an element.
   *
   * @param event
   * @param element
   */
  function clickPosition(event, element) {
    var xpos = event.pageX;
    var ypos = event.pageY;

    if (element.offsetParent) {
      do {
        xpos -= element.offsetLeft;
        ypos -= element.offsetTop;
      } while (element = element.offsetParent);
    }

    return {x: xpos, y: ypos}
  }

  var ob        = null;
  var Y         = 0;
  var X         = 0;
  var oldx      = 0;
  var oldlinew  = 0;
  var personnum = 0;
  var type      = 0;
  var boxmean   = 0;

  function ageCursorMouseDown(divbox, num) {
    ob        = divbox;
    personnum = num;
    type      = 0;
    X         = ob.offsetLeft;
    Y         = ob.offsetTop;
  }

  function factMouseDown(divbox, num, mean) {
    ob        = divbox;
    personnum = num;
    boxmean   = mean;
    type      = 1;
    oldx      = ob.offsetLeft;
    oldlinew  = 0;
  }

  document.onmousemove = function (e) {
    var textDirection = document.documentElement.dir;

    if (ob === null) {
      return true;
    }
    var newx = 0;
    var newy = 0;
    if (type === 0) {
      // age boxes
      newPosition = clickPosition(e, document.getElementById("timeline_chart"));
      newx        = newPosition.x;
      newy        = newPosition.y;

      if (oldx === 0) {
        oldx = newx;
      }
      if (newy < topy - bheight / 2) {
        newy = topy - bheight / 2;
      }
      if (newy > bottomy) {
        newy = bottomy - 1;
      }
      ob.style.top = newy + "px";
      var tyear    = (newy + bheight - 4 - topy + scale) / scale + baseyear;
      var year     = Math.floor(tyear);
      var month    = Math.floor(tyear * 12 - year * 12);
      var day      = Math.floor(tyear * 365 - year * 365 - month * 30);
      var mstamp   = year * 365 + month * 30 + day;
      var bdstamp  = birthyears[personnum] * 365 + birthmonths[personnum] * 30 + birthdays[personnum];
      var daydiff  = mstamp - bdstamp;
      var ba       = 1;
      if (daydiff < 0) {
        ba      = -1;
        daydiff = (bdstamp - mstamp);
      }
      var yage = Math.floor(daydiff / 365);
      var mage = Math.floor((daydiff - yage * 365) / 30);
      var dage = Math.floor(daydiff - yage * 365 - mage * 30);
      if (dage < 0) {
        mage = mage - 1;
      }
      if (dage < -30) {
        dage = 30 + dage;
      }
      if (mage < 0) {
        yage = yage - 1;
      }
      if (mage < -11) {
        mage = 12 + mage;
      }
      var yearform       = document.getElementById('yearform' + personnum);
      var ageform        = document.getElementById('ageform' + personnum);
      yearform.innerHTML = year + "      " + month + " <?= mb_substr(I18N::translate('Month:'), 0, 1) ?>   " + day + " <?= mb_substr(I18N::translate('Day:'), 0, 1) ?>";
      if (ba * yage > 1 || ba * yage < -1 || ba * yage === 0) {
        ageform.innerHTML = (ba * yage) + " <?= mb_substr(I18N::translate('years'), 0, 1) ?>   " + (ba * mage) + " <?= mb_substr(I18N::translate('Month:'), 0, 1) ?>   " + (ba * dage) + " <?= mb_substr(I18N::translate('Day:'), 0, 1) ?>";
      } else {
        ageform.innerHTML = (ba * yage) + " <?= mb_substr(I18N::translate('Year:'), 0, 1) ?>   " + (ba * mage) + " <?= mb_substr(I18N::translate('Month:'), 0, 1) ?>   " + (ba * dage) + " <?= mb_substr(I18N::translate('Day:'), 0, 1) ?>";
      }
      var line = document.getElementById('ageline' + personnum);
      var temp = newx - oldx;

      if (textDirection === 'rtl') {
        temp = temp * -1;
      }
      line.style.width = (line.width + temp) + "px";
      oldx             = newx;
      return false;
    } else {
      // fact boxes
      var linewidth;
      newPosition = clickPosition(e, document.getElementById("timeline_chart"));
      newx        = newPosition.x;
      newy        = newPosition.y;
      if (oldx === 0) {
        oldx = newx;
      }
      linewidth = e.pageX;

      // get diagnal line box
      var dbox = document.getElementById('dbox' + personnum);
      var etopy;
      var ebottomy;
      // set up limits
      if (boxmean - 175 < topy) {
        etopy = topy;
      } else {
        etopy = boxmean - 175;
      }
      if (boxmean + 175 > bottomy) {
        ebottomy = bottomy;
      } else {
        ebottomy = boxmean + 175;
      }
      // check if in the bounds of the limits
      if (newy < etopy) {
        newy = etopy;
      }
      if (newy > ebottomy) {
        newy = ebottomy;
      }
      // calculate the change in Y position
      var dy = newy - ob.offsetTop;
      // check if we are above the starting point and switch the background image

      if (newy < boxmean) {
        if (textDirection === 'rtl') {
          dbox.style.backgroundImage    = "url('<?= Theme::theme()->parameter('image-dline2') ?>')";
          dbox.style.backgroundPosition = "0% 0%";
        } else {
          dbox.style.backgroundImage    = "url('<?= Theme::theme()->parameter('image-dline') ?>')";
          dbox.style.backgroundPosition = "0% 100%";
        }
        dy             = -dy;
        dbox.style.top = (newy + bheight / 3) + "px";
      } else {
        if (textDirection === 'rtl') {
          dbox.style.backgroundImage    = "url('<?= Theme::theme()->parameter('image-dline') ?>')";
          dbox.style.backgroundPosition = "0% 100%";
        } else {
          dbox.style.backgroundImage    = "url('<?= Theme::theme()->parameter('image-dline2') ?>')";
          dbox.style.backgroundPosition = "0% 0%";
        }

        dbox.style.top = (boxmean + bheight / 3) + "px";
      }
      // the new X posistion moves the same as the y position
      if (textDirection === 'rtl') {
        newx = dbox.offsetRight + Math.abs(newy - boxmean);
      } else {
        newx = dbox.offsetLeft + Math.abs(newy - boxmean);
      }
      // set the X position of the box
      if (textDirection === 'rtl') {
        ob.style.right = newx + "px";
      } else {
        ob.style.left = newx + "px";
      }
      // set new top positions
      ob.style.top     = newy + "px";
      // get the width for the diagnal box
      var newwidth     = (ob.offsetLeft - dbox.offsetLeft);
      // set the width
      dbox.style.width = newwidth + "px";
      if (textDirection === 'rtl') {
        dbox.style.right = (dbox.offsetRight - newwidth) + 'px';
      }
      dbox.style.height = newwidth + "px";
      // change the line width to the change in the mouse X position
      line              = document.getElementById('boxline' + personnum);
      if (oldlinew !== 0) {
        line.width = line.width + (linewidth - oldlinew);
      }
      oldlinew = linewidth;
      oldx     = newx;
      return false;
    }
  };

  document.onmouseup = function () {
    ob   = null;
    oldx = 0;
  }
</script>

<div id="timeline_chart">
	<!-- print the timeline line image -->
	<div id="line" style="position:absolute; <?= I18N::direction() === 'ltr' ? 'left:22px;' : 'right:22px;' ?> top:0;">
		<img src="<?= Theme::theme()->parameter('image-vline') ?>" width="3" height="<?= 0 + ($topyear - $baseyear) * $scale ?>">
	</div>

	<!-- print divs for the grid -->
	<div id="scale<?= e($baseyear) ?>" style="position:absolute; <?= I18N::direction() === 'ltr' ? 'left' : 'right' ?>:0; top:-5px; font-size: 7pt; text-align: <?= I18N::direction() === 'ltr' ? 'left' : 'right' ?>;">
		<?= $baseyear ?>
	</div>
	<?php
	// at a scale of 25 or higher, show every year
	$mod = 25 / $scale;
	if ($mod < 1) {
		$mod = 1;
	}
	for ($i = $baseyear + 1; $i < $topyear; $i++) {
		if ($i % $mod === 0) {
			echo '<div id="scale' . $i . '" style="position:absolute; ' . (I18N::direction() === 'ltr' ? 'left:0;' : 'right:0;') . ' top:' . ((($i - $baseyear) * $scale) - $scale / 2) . 'px; font-size: 7pt; text-align:' . (I18N::direction() === 'ltr' ? 'left' : 'right') . ';">';
			echo $i;
			echo '</div>';
		}
	}
	echo '';
	?>
	<div id="scale<?= e($topyear) ?>" style="position:absolute; <?= I18N::direction() === 'ltr' ? 'left' : 'right' ?>:0; top:<?= ($topyear - $baseyear) * $scale ?>px; font-size: 7pt; text-align:<?= I18N::direction() === 'ltr' ? 'left' : 'right' ?>;">
		<?= e($topyear) ?>
	</div>

	<?php foreach ($indifacts as $factcount => $event): ?>
		<?php
		$desc     = $event->getValue();
		$gdate    = $event->getDate();
		$date     = $gdate->minimumDate();
		$date     = $date->convertToCalendar('gregorian');
		$year     = $date->y;
		$month    = max(1, $date->m);
		$day      = max(1, $date->d);
		$xoffset  = 0 + 22;
		$yoffset  = 0 + (($year - $baseyear) * $scale) - ($scale);
		$yoffset  = $yoffset + (($month / 12) * $scale);
		$yoffset  = $yoffset + (($day / 30) * ($scale / 12));
		$yoffset  = (int) ($yoffset);
		$place    = (int) ($yoffset / $bheight);
		$i        = 1;
		$j        = 0;
		$tyoffset = 0;
		while (isset($placements[$place])) {
			if ($i === $j) {
				$tyoffset = $bheight * $i;
				$i++;
			} else {
				$tyoffset = -1 * $bheight * $j;
				$j++;
			}
			$place = (int) (($yoffset + $tyoffset) / $bheight);
		}
		$yoffset            += $tyoffset;
		$xoffset            += abs($tyoffset);
		$placements[$place] = $yoffset;

		echo "<div id=\"fact$factcount\" style=\"position:absolute; " . (I18N::direction() === 'ltr' ? 'left: ' . ($xoffset) : 'right: ' . ($xoffset)) . 'px; top:' . ($yoffset) . 'px; font-size: 8pt; height: ' . ($bheight) . "px;\" onmousedown=\"factMouseDown(this, '" . $factcount . "', " . ($yoffset - $tyoffset) . ');">';
		echo '<table cellspacing="0" cellpadding="0" border="0" style="cursor: hand;"><tr><td>';
		echo '<img src="' . Theme::theme()->parameter('image-hline') . '" name="boxline' . $factcount . '" id="boxline' . $factcount . '" height="3" width="10" style="padding-';
		if (I18N::direction() === 'ltr') {
			echo 'left: 3px;">';
		} else {
			echo 'right: 3px;">';
		}

		$col = array_search($event->getParent(), $individuals);
		if ($col === false) {
			// Marriage event - use the color of the husband
			$col = array_search($event->getParent()->getHusband(), $individuals);
		}
		if ($col === false) {
			// Marriage event - use the color of the wife
			$col = array_search($event->getParent()->getWife(), $individuals);
		}
		$col = $col % 6;
		echo '</td><td class="person' . $col . '">';
		if (count($individuals) > 6) {
			// We only have six colours, so show naes if more than this number
			echo $event->getParent()->getFullName() . ' — ';
		}
		$record = $event->getParent();
		echo $event->getLabel();
		echo ' — ';
		if ($record instanceof Individual) {
			echo FunctionsPrint::formatFactDate($event, $record, false, false);
		} elseif ($record instanceof Family) {
			echo $gdate->display();
			if ($record->getHusband() && $record->getHusband()->getBirthDate()->isOK()) {
				$ageh = FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($record->getHusband()->getBirthDate(), $gdate));
			} else {
				$ageh = null;
			}
			if ($record->getWife() && $record->getWife()->getBirthDate()->isOK()) {
				$agew = FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($record->getWife()->getBirthDate(), $gdate));
			} else {
				$agew = null;
			}
			if ($ageh && $agew) {
				echo '<span class="age"> ', I18N::translate('Husband’s age'), ' ', $ageh, ' ', I18N::translate('Wife’s age'), ' ', $agew, '</span>';
			} elseif ($ageh) {
				echo '<span class="age"> ', I18N::translate('Age'), ' ', $ageh, '</span>';
			} elseif ($agew) {
				echo '<span class="age"> ', I18N::translate('Age'), ' ', $ageh, '</span>';
			}
		}
		echo ' ' . e($desc);
		if (!$event->getPlace()->isEmpty()) {
			echo ' — ' . $event->getPlace()->getShortName();
		}
		// Print spouses names for family events
		if ($event->getParent() instanceof Family) {
			echo ' — <a href="', e($event->getParent()->url()), '">', $event->getParent()->getFullName(), '</a>';
		}
		echo '</td></tr></table>';
		echo '</div>';
		if (I18N::direction() === 'ltr') {
			$img  = 'image-dline2';
			$ypos = '0%';
		} else {
			$img  = 'image-dline';
			$ypos = '100%';
		}
		$dyoffset = ($yoffset - $tyoffset) + $bheight / 3;
		if ($tyoffset < 0) {
			$dyoffset = $yoffset + $bheight / 3;
			if (I18N::direction() === 'ltr') {
				$img  = 'image-dline';
				$ypos = '100%';
			} else {
				$img  = 'image-dline2';
				$ypos = '0%';
			}
		}
		?>

		<!-- diagonal line -->
		<div id="dbox<?= $factcount ?>" style="position:absolute; <?= (I18N::direction() === 'ltr' ? 'left: ' . (0 + 25) : 'right: ' . (0 + 25)) ?>px; top:<?= ($dyoffset) ?>px; font-size: 8pt; height: <?= abs($tyoffset) ?>px; width: <?= abs($tyoffset) ?>px; background-image: url('<?= Theme::theme()->parameter($img) ?>'); background-position: 0% <?= $ypos ?>;">
		</div>
	<?php endforeach ?>

	<!-- age cursors -->
	<?php foreach ($individuals as $p => $indi): ?>
		<?php $ageyoffset = 0 + ($bheight * $p); ?>
		<div id="agebox<?= $p ?>" style="cursor:move; position:absolute; <?= I18N::direction() === 'ltr' ? 'left:20px;' : 'right:20px;' ?> top:<?= $ageyoffset ?>px; height:<?= $bheight ?>px; display:none;" onmousedown="ageCursorMouseDown(this, <?= $p ?>);">
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<img src="<?= Theme::theme()->parameter('image-hline') ?>" name="ageline<?= $p ?>" id="ageline<?= $p ?>" width="25" height="3">
					</td>
					<td>
						<?php if (!empty($birthyears[$indi->getXref()])): ?>
							<?php $tyear = round(($ageyoffset + ($bheight / 2)) / $scale) + $baseyear; ?>
							<table class="person<?= $p % 6 ?>" style="cursor: hand;">
								<tr>
									<td>
										<?= I18N::translate('Year:') ?>
										<span id="yearform<?= $p ?>" class="field">
											<?= $tyear ?>
										</span>
									</td>
									<td>
										(<?= I18N::translate('Age') ?> <span id="ageform<?= $p ?>" class="field"><?= $tyear - $birthyears[$indi->getXref()] ?></span>)
									</td>
								</tr>
							</table>
						<?php endif ?>
					</td>
				</tr>
			</table>
			<br>
			<br>
			<br>
		</div>
		<br>
		<br>
		<br>
		<br>
	<?php endforeach ?>
</div>
