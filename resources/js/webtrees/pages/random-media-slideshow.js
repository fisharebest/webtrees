'use strict';

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Initialize random media slideshow controls.
 */
export function initializeRandomMediaSlideshow (root) {
  root.querySelectorAll('[data-wt-random-media-slideshow]').forEach((element) => {
    if (!(element instanceof HTMLElement)) {
      throw new Error('Random media slideshow container must be an HTML element.');
    }

    const blockId = requireDatasetValue(element, 'wtRandomMediaBlockId', 'random media block ID');
    const block = requireElement(document, '#' + CSS.escape(blockId), HTMLElement, 'random media block');

    if (!(block.parentElement instanceof HTMLElement)) {
      throw new Error('Random media block must have a parent container.');
    }

    const ajaxUrl = requireDatasetValue(block.parentElement, 'wtAjaxUrl', 'random media AJAX URL');
    const delay = Number(requireDatasetValue(element, 'wtRandomMediaDelayMs', 'random media delay'));
    const hasControls = requireDatasetValue(element, 'wtRandomMediaControls', 'random media controls flag') === '1';
    if (!Number.isFinite(delay) || delay < 0) {
      throw new Error('Random media delay must be a non-negative number.');
    }

    let play = requireDatasetValue(element, 'wtRandomMediaStart', 'random media autoplay flag') === '1';
    let timeout = null;

    const slideShowReload = () => {
      if (timeout !== null) {
        clearTimeout(timeout);
      }

      if (document.hidden) {
        timeout = setTimeout(slideShowReload, 1000);
      } else {
        window.webtrees.load(block.parentElement, ajaxUrl + '&start=' + (play ? '1' : '0')).catch((error) => {
          console.error('Failed to reload random media slideshow', { ajaxUrl, error });
        });
      }
    };

    if (play) {
      timeout = setTimeout(slideShowReload, delay);
    }

    if (!hasControls) {
      return;
    }

    const playControl = requireElement(block, '.wt-icon-media-play', HTMLElement, 'random media play icon').parentElement;
    const stopControl = requireElement(block, '.wt-icon-media-stop', HTMLElement, 'random media stop icon').parentElement;
    const nextControl = requireElement(block, '.wt-icon-media-next', HTMLElement, 'random media next icon').parentElement;

    if (!(playControl instanceof HTMLElement) || !(stopControl instanceof HTMLElement) || !(nextControl instanceof HTMLElement)) {
      throw new Error('Random media controls must be HTML elements.');
    }

    playControl.addEventListener('click', (event) => {
      event.preventDefault();
      playControl.hidden = true;
      stopControl.hidden = false;
      play = true;
      slideShowReload();
    });

    stopControl.addEventListener('click', (event) => {
      event.preventDefault();
      stopControl.hidden = true;
      playControl.hidden = false;
      play = false;

      if (timeout !== null) {
        clearTimeout(timeout);
      }
    });

    nextControl.addEventListener('click', (event) => {
      event.preventDefault();
      slideShowReload();
    });
  });
}
