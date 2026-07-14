'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize interactive timeline chart controls.
 */
export function initializeTimelineChartPage (root) {
  root.querySelectorAll('[data-wt-timeline-chart]').forEach((chart) => {
      if (!(chart instanceof HTMLElement)) {
        throw new Error('Timeline chart container must be an HTML element.');
      }

      if (chart.dataset.wtTimelineInitialized === '1') {
        return;
      }

      chart.dataset.wtTimelineInitialized = '1';

      const overlay = requireElement(chart, '[data-wt-timeline-overlay]', SVGSVGElement, 'timeline SVG overlay');
      const axisOffset = Number(requireDatasetValue(chart, 'wtTimelineAxisOffset', 'timeline axis offset'));
      const boxHeight = Number(requireDatasetValue(chart, 'wtTimelineBoxHeight', 'timeline box height'));
      const scale = Number(requireDatasetValue(chart, 'wtTimelineScale', 'timeline scale'));
      const baseYearOffset = Number(requireDatasetValue(chart, 'wtTimelineBaseYearOffset', 'timeline base year offset'));
      const bottomY = Number(requireDatasetValue(chart, 'wtTimelineBottomY', 'timeline bottom limit'));
      const yearAbbrev = requireDatasetValue(chart, 'wtTimelineYearAbbrev', 'timeline year abbreviation');
      const monthAbbrev = requireDatasetValue(chart, 'wtTimelineMonthAbbrev', 'timeline month abbreviation');
      const dayAbbrev = requireDatasetValue(chart, 'wtTimelineDayAbbrev', 'timeline day abbreviation');
      const locale = document.documentElement.lang || undefined;

      const createDateFormatter = () => {
        try {
          return new Intl.DateTimeFormat(locale, { dateStyle: 'medium', timeZone: 'UTC' });
        } catch {
          return new Intl.DateTimeFormat(locale, { year: 'numeric', month: 'short', day: 'numeric', timeZone: 'UTC' });
        }
      };

      const dateFormatter = createDateFormatter();

      if (!Number.isFinite(axisOffset) || !Number.isFinite(boxHeight) || !Number.isFinite(scale) || !Number.isFinite(baseYearOffset) || !Number.isFinite(bottomY)) {
        throw new Error('Timeline chart dataset values must be numeric where expected.');
      }

      /** @type {Record<string, number|null>} */
      const birthYears = JSON.parse(requireDatasetValue(chart, 'wtTimelineBirthYears', 'timeline birth years'));
      /** @type {Record<string, number|null>} */
      const birthMonths = JSON.parse(requireDatasetValue(chart, 'wtTimelineBirthMonths', 'timeline birth months'));
      /** @type {Record<string, number|null>} */
      const birthDays = JSON.parse(requireDatasetValue(chart, 'wtTimelineBirthDays', 'timeline birth days'));

      const isRtl = chart.dataset.wtTimelineDirection === 'rtl';
      const factBoxes = Array.from(chart.querySelectorAll('[data-wt-timeline-fact]'));
      const ageBoxes = Array.from(chart.querySelectorAll('[data-wt-timeline-agebox]'));

      factBoxes.forEach((fact) => {
        if (!(fact instanceof HTMLElement)) {
          throw new Error('Timeline fact box must be an HTML element.');
        }
      });

      ageBoxes.forEach((ageBox) => {
        if (!(ageBox instanceof HTMLElement)) {
          throw new Error('Timeline age cursor must be an HTML element.');
        }
      });

      const parseTop = (element) => {
        const top = Number.parseFloat(element.style.top);

        if (!Number.isFinite(top)) {
          throw new Error('Timeline element is missing numeric top position.');
        }

        return top;
      };

      const chartPoint = (event) => {
        const rect = chart.getBoundingClientRect();

        return {
          x: event.clientX - rect.left,
          y: event.clientY - rect.top,
        };
      };

      const elementLeftInChart = (element) => {
        const chartRect = chart.getBoundingClientRect();
        const elementRect = element.getBoundingClientRect();

        return elementRect.left - chartRect.left;
      };

      const axisX = () => isRtl ? chart.clientWidth - axisOffset : axisOffset;

      const factAnchorX = (fact) => isRtl ? fact.offsetLeft + fact.offsetWidth : fact.offsetLeft;
      const ageAnchorX = (ageBox) => isRtl ? ageBox.offsetLeft + ageBox.offsetWidth : ageBox.offsetLeft;

      const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

      const createLine = (x1, y1, x2, y2, cssClass) => {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', String(x1));
        line.setAttribute('y1', String(y1));
        line.setAttribute('x2', String(x2));
        line.setAttribute('y2', String(y2));
        line.setAttribute('class', cssClass);
        return line;
      };

      const formatLocalDate = (year, month, day) => {
        const safeMonth = clamp(month, 0, 11);
        const safeDay = clamp(day, 1, 31);
        const date = new Date(Date.UTC(0, 0, 1));
        date.setUTCFullYear(year, safeMonth, safeDay);

        if (Number.isNaN(date.getTime())) {
          return year + ' ' + monthAbbrev + ' ' + safeMonth + ' ' + dayAbbrev + ' ' + safeDay;
        }

        return dateFormatter.format(date);
      };

      const redrawOverlay = () => {
        const width = chart.clientWidth;
        const height = chart.scrollHeight;

        overlay.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
        overlay.setAttribute('width', String(width));
        overlay.setAttribute('height', String(height));

        const elements = [createLine(axisX(), 0, axisX(), height, 'wt-timeline-axis-line')];

        factBoxes.forEach((fact) => {
          const meanY = Number(requireDatasetValue(fact, 'wtTimelineMeanY', 'timeline fact mean position')) + boxHeight / 2;
          const top = parseTop(fact) + boxHeight / 2;

          elements.push(createLine(axisX(), meanY, factAnchorX(fact), top, 'wt-timeline-fact-line'));
        });

        ageBoxes.forEach((ageBox) => {
          if (getComputedStyle(ageBox).display === 'none') {
            return;
          }

          const top = parseTop(ageBox) + boxHeight / 2;
          elements.push(createLine(axisX(), top, ageAnchorX(ageBox), top, 'wt-timeline-age-line'));
        });

        overlay.replaceChildren(...elements);
      };

      const formatAgeData = (personIndex, yPosition) => {
        const personKey = String(personIndex);
        const birthYear = birthYears[personKey];
        const birthMonth = birthMonths[personKey];
        const birthDay = birthDays[personKey];

        if (!Number.isFinite(birthYear) || !Number.isFinite(birthMonth) || !Number.isFinite(birthDay)) {
          return;
        }

        const tyear = (yPosition + boxHeight - 4 + scale) / scale + baseYearOffset;
        const year = Math.floor(tyear);
        const month = Math.floor(tyear * 12 - year * 12);
        const day = Math.floor(tyear * 365 - year * 365 - month * 30);
        const markerStamp = year * 365 + month * 30 + day;
        const birthStamp = birthYear * 365 + birthMonth * 30 + birthDay;

        let delta = markerStamp - birthStamp;
        let sign = 1;

        if (delta < 0) {
          sign = -1;
          delta = birthStamp - markerStamp;
        }

        let yearAge = Math.floor(delta / 365);
        let monthAge = Math.floor((delta - yearAge * 365) / 30);
        let dayAge = Math.floor(delta - yearAge * 365 - monthAge * 30);

        if (dayAge < 0) {
          monthAge -= 1;
        }

        if (dayAge < -30) {
          dayAge = 30 + dayAge;
        }

        if (monthAge < 0) {
          yearAge -= 1;
        }

        if (monthAge < -11) {
          monthAge = 12 + monthAge;
        }

        const yearForm = document.getElementById('yearform' + personIndex);
        const ageForm = document.getElementById('ageform' + personIndex);

        if (!(yearForm instanceof HTMLElement) || !(ageForm instanceof HTMLElement)) {
          return;
        }

        yearForm.textContent = formatLocalDate(year, month, day);

        ageForm.textContent =
            (sign * yearAge) + yearAbbrev + ' ' +
            (sign * monthAge) + monthAbbrev + ' ' +
            (sign * dayAge) + dayAbbrev;
      };

      const updateFactPosition = (fact, yPosition) => {
        const meanY = Number(requireDatasetValue(fact, 'wtTimelineMeanY', 'timeline fact mean position'));
        const top = clamp(yPosition, clamp(meanY - 175, 0, bottomY), clamp(meanY + 175, 0, bottomY));
        const x = axisOffset + Math.abs(top - meanY);

        fact.style.top = top + 'px';

        if (isRtl) {
          fact.style.right = x + 'px';
        } else {
          fact.style.left = x + 'px';
        }
      };

      const updateAgePosition = (ageBox, yPosition, personIndex, xPosition = null) => {
        const top = clamp(yPosition, -boxHeight / 2, bottomY);
        ageBox.style.top = top + 'px';

        if (xPosition !== null) {
          const width = ageBox.offsetWidth;
          const minLeft = isRtl ? 2 : axisX() + 2;
          const maxLeft = isRtl ? axisX() - width - 2 : chart.clientWidth - width - 2;
          const clampedLeft = clamp(xPosition, Math.min(minLeft, maxLeft), Math.max(minLeft, maxLeft));

          if (isRtl) {
            ageBox.style.right = (chart.clientWidth - clampedLeft - width) + 'px';
          } else {
            ageBox.style.left = clampedLeft + 'px';
          }
        }

        formatAgeData(personIndex, top);
      };

      /** @type {{type: 'fact'|'age', element: HTMLElement, pointerOffsetY: number, pointerOffsetX: number|null, personIndex: number}|null} */
      let dragState = null;

      chart.addEventListener('mousedown', (event) => {
        const target = event.target instanceof Element ? event.target.closest('[data-wt-timeline-fact], [data-wt-timeline-agebox]') : null;

        if (!(target instanceof HTMLElement)) {
          return;
        }

        const point = chartPoint(event);
        const top = parseTop(target);

        if (target.matches('[data-wt-timeline-fact]')) {
          const factIndex = Number(requireDatasetValue(target, 'wtTimelineFactIndex', 'timeline fact index'));

          dragState = {
            type: 'fact',
            element: target,
            pointerOffsetY: point.y - top,
            pointerOffsetX: null,
            personIndex: factIndex,
          };
        } else {
          const personIndex = Number(requireDatasetValue(target, 'wtTimelinePersonIndex', 'timeline age cursor index'));

          if (!Number.isInteger(personIndex)) {
            throw new Error('Timeline age cursor index must be an integer.');
          }

          dragState = {
            type: 'age',
            element: target,
            pointerOffsetY: point.y - top,
            pointerOffsetX: point.x - elementLeftInChart(target),
            personIndex,
          };
        }

        event.preventDefault();
      });

      document.addEventListener('mousemove', (event) => {
        if (dragState === null) {
          return;
        }

        const point = chartPoint(event);
        const targetY = point.y - dragState.pointerOffsetY;

        if (dragState.type === 'fact') {
          updateFactPosition(dragState.element, targetY);
        } else {
          const offsetX = dragState.pointerOffsetX;

          if (offsetX === null) {
            throw new Error('Timeline age cursor drag state is missing horizontal offset.');
          }

          const targetX = point.x - offsetX;
          updateAgePosition(dragState.element, targetY, dragState.personIndex, targetX);
        }

        redrawOverlay();
      });

      document.addEventListener('mouseup', () => {
        dragState = null;
      });

      document.querySelectorAll('input[data-wt-toggle-target]').forEach((control) => {
        if (!(control instanceof HTMLInputElement)) {
          return;
        }

        const targetSelector = control.dataset.wtToggleTarget;

        if (targetSelector === undefined) {
          return;
        }

        const target = document.querySelector(targetSelector);

        if (!ageBoxes.includes(target)) {
          return;
        }

        control.addEventListener('change', () => {
          requestAnimationFrame(redrawOverlay);
        });
      });

      window.addEventListener('resize', redrawOverlay);

      redrawOverlay();
      requestAnimationFrame(redrawOverlay);
    });
}

