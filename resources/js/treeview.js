/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

'use strict';

let observerInitialized = false;
const COMPACT_COOKIE_NAME = 'wt_interactive_tree_compact';

/**
 * @param {Event} event
 * @returns {{pageX: number, pageY: number}}
 */
function getPointerPosition(event) {
  if (event instanceof TouchEvent && event.touches.length > 0) {
    return {
      pageX: event.touches[0].pageX,
      pageY: event.touches[0].pageY,
    };
  }

  if (event instanceof TouchEvent && event.changedTouches.length > 0) {
    return {
      pageX: event.changedTouches[0].pageX,
      pageY: event.changedTouches[0].pageY,
    };
  }

  if (!(event instanceof MouseEvent)) {
    return { pageX: 0, pageY: 0 };
  }

  return {
    pageX: event.pageX,
    pageY: event.pageY,
  };
}

/**
 * @param {HTMLElement} element
 * @returns {{left: number, top: number}}
 */
function getElementOffset(element) {
  const rect = element.getBoundingClientRect();

  return {
    left: rect.left + window.scrollX,
    top: rect.top + window.scrollY,
  };
}

/**
 * @param {HTMLElement} element
 * @param {{left: number, top: number}} coordinates
 */
function setElementOffset(element, coordinates) {
  const offsetParent = element.offsetParent instanceof HTMLElement
    ? element.offsetParent
    : document.documentElement;
  const parentOffset = getElementOffset(offsetParent);

  element.style.left = coordinates.left - parentOffset.left + 'px';
  element.style.top = coordinates.top - parentOffset.top + 'px';
}

export class TreeViewHandler {
  /**
   * @param {HTMLElement} containerElement
   */
  constructor(containerElement) {
    this.containerElement = containerElement;
    this.container = containerElement;
    this.treeview = containerElement.querySelector('[data-wt-interactive-tree-canvas]');
    this.loadingImage = containerElement.querySelector('[data-wt-interactive-tree-loading]');
    this.toolbox = containerElement.querySelector('[data-wt-interactive-tree-tools]');
    this.zoom = 100;
    this.boxWidth = 180;
    this.boxExpandedWidth = 250;
    this.cookieDays = 3;
    this.ajaxDetails = containerElement.dataset.wtInteractiveTreeDetailsUrl;
    this.ajaxPersons = containerElement.dataset.wtInteractiveTreeIndividualsUrl;
    this.autoBoxWidth = false;
    this.updating = false;

    if (!(this.treeview instanceof HTMLElement) || !(this.loadingImage instanceof HTMLElement) || !(this.toolbox instanceof HTMLElement) || !this.ajaxDetails || !this.ajaxPersons) {
      throw new Error('Interactive tree container is missing required elements or data attributes.');
    }

    if (readCookie(COMPACT_COOKIE_NAME) === 'true') {
      this.compact();
    }

    this.bindDragHandlers();
    this.bindControlHandlers();

    this.treeview.addEventListener('click', (event) => {
      const target = event.target instanceof Element ? event.target.closest('[data-wt-interactive-tree-box]') : null;

      if (!(target instanceof HTMLElement) || !this.treeview.contains(target)) {
        return;
      }

      this.expandBox(target, event);
    });

    this.centerOnRoot();
  }

  bindDragHandlers() {
    let dragging = false;
    let isDown = false;
    let dragStartX = 0;
    let dragStartY = 0;

    const startDrag = (event) => {
      const pointer = getPointerPosition(event);
      const treeOffset = getElementOffset(this.treeview);
      dragStartX = treeOffset.left - pointer.pageX;
      dragStartY = treeOffset.top - pointer.pageY;
      isDown = true;
      dragging = false;

      const moveDrag = (moveEvent) => {
        if (!isDown) {
          return;
        }

        if (moveEvent.cancelable) {
          moveEvent.preventDefault();
        }

        dragging = true;

        const movePointer = getPointerPosition(moveEvent);
        setElementOffset(this.treeview, {
          left: movePointer.pageX + dragStartX,
          top: movePointer.pageY + dragStartY,
        });
      };

      const endDrag = (endEvent) => {
        isDown = false;
        document.removeEventListener('mousemove', moveDrag);
        document.removeEventListener('touchmove', moveDrag);
        document.removeEventListener('mouseup', endDrag);
        document.removeEventListener('touchend', endDrag);
        document.removeEventListener('touchcancel', endDrag);

        if (!dragging) {
          return;
        }

        if (endEvent.cancelable) {
          endEvent.preventDefault();
        }

        dragging = false;
        this.updateTree();
      };

      document.addEventListener('mousemove', moveDrag);
      document.addEventListener('touchmove', moveDrag, { passive: false });
      document.addEventListener('mouseup', endDrag);
      document.addEventListener('touchend', endDrag);
      document.addEventListener('touchcancel', endDrag);
    };

    this.treeview.addEventListener('mousedown', startDrag);
    this.treeview.addEventListener('touchstart', startDrag, { passive: true });
  }

  bindControlHandlers() {
    this.toolbox.querySelectorAll('[data-wt-interactive-tree-compact]').forEach((button) => {
      button.addEventListener('click', () => this.compact());
    });

  }

  setLoading() {
    this.treeview.style.cursor = 'wait';
    this.loadingImage.style.display = 'block';
  }

  setComplete() {
    this.treeview.style.cursor = 'move';
    this.loadingImage.style.display = 'none';
  }

  getSize() {
    const container = this.container.parentElement ?? this.container;
    const offset = getElementOffset(container);

    this.leftMin = offset.left;
    this.leftMax = this.leftMin + container.clientWidth;
    this.topMin = offset.top;
    this.topMax = this.topMin + container.clientHeight;
  }

  updateTree(center, button) {
    const toLoad = [];
    const elements = [];
    this.getSize();

    this.treeview.querySelectorAll('td[data-wt-interactive-tree-request]').forEach((td) => {
      const pos = getElementOffset(td);

      if (pos.left >= this.leftMin && pos.left <= this.leftMax && pos.top >= this.topMin && pos.top <= this.topMax) {
        toLoad.push(td.getAttribute('data-wt-interactive-tree-request'));
        elements.push(td);
      }
    });

    if (toLoad.length === 0) {
      if (button) {
        button.classList.remove('tvPressed');
      }

      this.setComplete();
      return false;
    }

    this.updating = true;
    this.setLoading();

    const finalize = () => {
      if (this.autoBoxWidth) {
        this.treeview.querySelectorAll('.tv_box').forEach((box) => {
          box.style.width = 'auto';
        });
      }

      if (center) {
        this.centerOnRoot();
      }

      if (button) {
        button.classList.remove('tvPressed');
      }

      this.setComplete();
      this.updating = false;
    };

    const personsUrl = new URL(this.ajaxPersons, window.location.href);
    personsUrl.searchParams.set('q', toLoad.join(';'));

    fetch(personsUrl.toString(), {
      headers: new Headers({
        'accept': 'application/json',
        'x-requested-with': 'XMLHttpRequest',
      }),
    }).then((response) => {
      if (!response.ok) {
        throw new Error('Failed to load interactive tree branches.');
      }

      return response.json();
    }).then((response) => {
      for (let i = 0; i < elements.length; i += 1) {
        elements[i].removeAttribute('data-wt-interactive-tree-request');
        elements[i].innerHTML = response[i];
      }

      this.getSize();

      if (this.treeview.querySelector('td[data-wt-interactive-tree-request]')) {
        this.updateTree(center, button);
        return;
      }

      finalize();
    }).catch(() => {
      finalize();
    });

    return false;
  }

  compact() {
    const button = this.toolbox.querySelector('[data-wt-interactive-tree-compact]');
    this.setLoading();

    if (this.autoBoxWidth) {
      const width = this.boxWidth * (this.zoom / 100) + 'px';
      const expandedWidth = this.boxExpandedWidth * (this.zoom / 100) + 'px';

      this.treeview.querySelectorAll('.tv_box:not(.boxExpanded)').forEach((box) => {
        box.style.width = width;
      });
      this.treeview.querySelectorAll('.boxExpanded').forEach((box) => {
        box.style.width = expandedWidth;
      });
      this.autoBoxWidth = false;

      if (readCookie(COMPACT_COOKIE_NAME)) {
        createCookie(COMPACT_COOKIE_NAME, false, this.cookieDays);
      }

      if (button instanceof HTMLElement) {
        button.classList.remove('tvPressed');
      }
    } else {
      this.treeview.querySelectorAll('.tv_box').forEach((box) => {
        box.style.width = 'auto';
      });
      this.autoBoxWidth = true;

      if (!readCookie(COMPACT_COOKIE_NAME)) {
        createCookie(COMPACT_COOKIE_NAME, true, this.cookieDays);
      }

      if (!this.updating) {
        this.updateTree();
      }

      if (button instanceof HTMLElement) {
        button.classList.add('tvPressed');
      }
    }

    this.setComplete();
    return false;
  }

  centerOnRoot() {
    const rootBox = this.treeview.querySelector('.rootPerson');

    if (!(rootBox instanceof HTMLElement)) {
      return false;
    }

    const treeOffset = getElementOffset(this.treeview);
    const containerOffset = getElementOffset(this.container);
    const rootOffset = getElementOffset(rootBox);
    const rootRect = rootBox.getBoundingClientRect();

    const rootCenterX = rootOffset.left + (rootRect.width / 2);
    const rootCenterY = rootOffset.top + (rootRect.height / 2);
    const viewportCenterX = containerOffset.left + (this.container.clientWidth / 2);
    const viewportCenterY = containerOffset.top + (this.container.clientHeight / 2);

    setElementOffset(this.treeview, {
      left: treeOffset.left + (viewportCenterX - rootCenterX),
      top: treeOffset.top + (viewportCenterY - rootCenterY),
    });

    if (!this.updating) {
      this.setComplete();
    }

    return false;
  }

  expandBox(box, event) {
    const eventTarget = event.target instanceof Element ? event.target : null;

    if (eventTarget?.closest('.tv_link')) {
      return false;
    }

    if (!(box instanceof HTMLElement) || !(box.parentElement instanceof HTMLElement)) {
      return false;
    }

    const expandedBox = box;
    const boxContainer = expandedBox.parentElement;
    const pid = expandedBox.dataset.wtInteractiveTreePersonId;

    let expanded;
    let collapsed;

    if (boxContainer.classList.contains('detailsLoaded')) {
      collapsed = boxContainer.querySelector('.collapsedContent');
      expanded = boxContainer.querySelector('.tv_box:not(.collapsedContent)');
    } else {
      expanded = expandedBox;
      collapsed = expandedBox.cloneNode(true);
      collapsed.classList.add('collapsedContent');
      collapsed.style.display = 'none';
      boxContainer.append(collapsed);

      const loadingTemplate = this.loadingImage.firstElementChild;
      const loadingImage = loadingTemplate instanceof HTMLElement
        ? loadingTemplate.cloneNode(true)
        : document.createElement('i');

      if (!(loadingImage instanceof HTMLElement)) {
        return false;
      }

      if (!(loadingTemplate instanceof HTMLElement)) {
        loadingImage.className = 'icon-loading-small';
      }

      loadingImage.classList.add('tv_box_loading');
      loadingImage.style.display = 'inline-block';

      expanded.prepend(loadingImage);
      this.updating = true;
      this.setLoading();

      const detailsUrl = new URL(this.ajaxDetails, window.location.href);
      detailsUrl.searchParams.set('pid', pid ?? '');

      const loader = typeof window.webtrees?.load === 'function'
        ? window.webtrees.load(expanded, detailsUrl.toString())
        : fetch(detailsUrl.toString(), {
          headers: new Headers({
            'accept': 'text/html',
            'x-requested-with': 'XMLHttpRequest',
          }),
        }).then((response) => response.text()).then((html) => {
          expanded.innerHTML = html;
        });

      loader.then(() => {

        expanded.style.width = this.boxExpandedWidth * (this.zoom / 100) + 'px';
        loadingImage.remove();
        boxContainer.classList.add('detailsLoaded');
        this.setComplete();
        this.updating = false;
      }).catch((error) => {
        console.error('Failed to load interactive tree details', { error });
        loadingImage.remove();
        this.setComplete();
        this.updating = false;
      });
    }

    if (!(expanded instanceof HTMLElement)) {
      return false;
    }

    if (expandedBox.classList.contains('boxExpanded')) {
      expanded.style.display = 'none';
      if (collapsed instanceof HTMLElement) {
        collapsed.style.display = 'block';
      }
      expandedBox.classList.remove('boxExpanded');
    } else {
      expanded.style.display = 'block';
      if (collapsed instanceof HTMLElement) {
        collapsed.style.display = 'none';
      }
      expanded.classList.add('boxExpanded');
    }

    this.getSize();
    return false;
  }
}

/**
 * @param {ParentNode} root
 */
function initializeInteractiveTree(root) {
  root.querySelectorAll('[data-wt-interactive-tree]').forEach((container) => {
    if (!(container instanceof HTMLElement)) {
      return;
    }

    if (container.dataset.wtInteractiveTreeInitialized === '1') {
      return;
    }

    container.dataset.wtInteractiveTreeInitialized = '1';
    new TreeViewHandler(container);
  });
}

function startInteractiveTree() {
  initializeInteractiveTree(document);

  if (observerInitialized) {
    return;
  }

  observerInitialized = true;

  document.addEventListener('wt-content-loaded', () => initializeInteractiveTree(document));
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startInteractiveTree, { once: true });
} else {
  startInteractiveTree();
}

/**
 * @param {string} name
 * @param {string|boolean} value
 * @param {number} days
 */
function createCookie(name, value, days) {
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + '; expires=' + date.toGMTString() + '; path=/';
  } else {
    document.cookie = name + '=' + value + '; path=/';
  }
}

/**
 * @param {string} name
 * @returns {string|null}
 */
function readCookie(name) {
  const nameEquals = name + '=';
  const cookies = document.cookie.split(';');

  for (let i = 0; i < cookies.length; i += 1) {
    let cookie = cookies[i];

    while (cookie.charAt(0) === ' ') {
      cookie = cookie.substring(1, cookie.length);
    }

    if (cookie.indexOf(nameEquals) === 0) {
      return cookie.substring(nameEquals.length, cookie.length);
    }
  }

  return null;
}

