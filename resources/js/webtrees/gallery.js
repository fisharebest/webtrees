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

import { createDialogModal } from './modal';
import { i18n } from './i18n';

const GALLERY_ATTRIBUTE = 'data-wt-gallery';
const GALLERY_TITLE_ATTRIBUTE = 'data-wt-gallery-title';
const GALLERY_TITLE_URL_ATTRIBUTE = 'data-wt-gallery-title-url';
const GALLERY_SOURCE_ATTRIBUTE = 'data-wt-gallery-src';
const MIN_ZOOM = 1;
const MAX_ZOOM = 8;

let gallery_modal = null;
let initialized = false;

/**
 * @typedef {{source: string, title: string, title_url: string}} GalleryItem
 */

/**
 * @param {Element} element
 * @returns {boolean}
 */
function hasGalleryAttributes(element) {

  return element.hasAttribute(GALLERY_ATTRIBUTE);
}

/**
 * @param {Element} element
 * @returns {string|null}
 */
function extractImageSource(element) {
  const source_attribute = element.getAttribute(GALLERY_SOURCE_ATTRIBUTE);

  if (source_attribute !== null && source_attribute !== '') {
    return source_attribute;
  }

  if (element instanceof HTMLImageElement && element.currentSrc !== '') {
    return element.currentSrc;
  }

  if (element instanceof HTMLImageElement && element.src !== '') {
    return element.src;
  }

  if (element instanceof HTMLAnchorElement && element.href !== '') {
    return element.href;
  }

  const image = element.querySelector('img');

  if (image instanceof HTMLImageElement && image.currentSrc !== '') {
    return image.currentSrc;
  }

  if (image instanceof HTMLImageElement && image.src !== '') {
    return image.src;
  }

  return null;
}

/**
 * @param {string} source
 * @returns {string}
 */
function normalizeSource(source) {
  return new URL(source, document.baseURI).href;
}


/**
 * @param {Element} element
 * @returns {{title: string, title_url: string}}
 */
function extractMetadata(element) {
  const title = element.getAttribute(GALLERY_TITLE_ATTRIBUTE) ?? element.dataset.title ?? '';
  const title_url = element.getAttribute(GALLERY_TITLE_URL_ATTRIBUTE) ?? '';

  return { title, title_url };
}

/**
 * @param {GalleryItem} target_item
 * @param {{title: string, title_url: string}} metadata
 * @returns {void}
 */
function mergeMetadata(target_item, metadata) {
  if (target_item.title === '' && metadata.title !== '') {
    target_item.title = metadata.title;
  }

  if (target_item.title_url === '' && metadata.title_url !== '') {
    target_item.title_url = metadata.title_url;
  }
}

/**
 * @returns {Element[]}
 */
function findGalleryElements() {
  const elements = [];
  const tree_walker = document.createTreeWalker(document.body, NodeFilter.SHOW_ELEMENT);

  let node = tree_walker.nextNode();

  while (node !== null) {
    if (node instanceof Element && hasGalleryAttributes(node)) {
      elements.push(node);
    }

    node = tree_walker.nextNode();
  }

  return elements;
}

/**
 * @param {Element} element
 * @returns {string}
 */
function describeElement(element) {
  const id = element.id !== '' ? '#' + element.id : '';
  return '<' + element.tagName.toLowerCase() + id + '>';
}

/**
 * @returns {{items: GalleryItem[], element_to_index: Map<Element, number>}}
 */
function collectGalleryItems() {
  const unique_sources = new Map();
  const items = [];
  const element_to_index = new Map();

  for (const element of findGalleryElements()) {
    const source = extractImageSource(element);

    if (source === null) {
      throw new Error('Gallery element ' + describeElement(element) + ' does not provide an image source.');
    }

    const normalized_source = normalizeSource(source);

    if (unique_sources.has(normalized_source)) {
      const existing_index = unique_sources.get(normalized_source);
      element_to_index.set(element, existing_index);
      mergeMetadata(items[existing_index], extractMetadata(element));
      continue;
    }

    const metadata = extractMetadata(element);
    const item = {
      source: normalized_source,
      title: metadata.title,
      title_url: metadata.title_url,
    };

    unique_sources.set(normalized_source, items.length);
    element_to_index.set(element, items.length);
    items.push(item);
  }

  return { items, element_to_index };
}

/**
 * @param {EventTarget|null} target
 * @returns {Element|null}
 */
function findGalleryTrigger(target) {
  let element = target instanceof Element ? target : null;

  while (element !== null) {
    if (hasGalleryAttributes(element)) {
      return element;
    }

    element = element.parentElement;
  }

  return null;
}

class GalleryModal {
  constructor() {
    const modal = createDialogModal({
      class_name: 'wt-gallery-dialog',
      action_attribute: 'data-wt-gallery-action',
      content_html: [
      '<div class="wt-gallery-shell">',
      '  <div class="wt-gallery-header">',
      '    <div class="wt-gallery-title"></div>',
      '    <button type="button" class="wt-gallery-button wt-gallery-button-close" data-wt-gallery-action="close" aria-label="' + i18n.gettext('close') + '"><i class="fa-solid fa-check"></i></button>',
      '  </div>',
      '  <div class="wt-gallery-stage">',
      '    <img class="wt-gallery-image">',
      '    <button type="button" class="wt-gallery-button wt-gallery-button-previous" data-wt-gallery-action="previous" aria-label="' + i18n.gettext('previous') + '">&lt;</button>',
      '    <button type="button" class="wt-gallery-button wt-gallery-button-next" data-wt-gallery-action="next" aria-label="' + i18n.gettext('next') + '">&gt;</button>',
      '  </div>',
      '</div>',
      ].join(''),
      on_close: () => this.resetState(),
    });

    this.modal = modal;
    this.dialog = modal.dialog;

    this.image = modal.findRequired('.wt-gallery-image');
    this.stage = modal.findRequired('.wt-gallery-stage');
    this.title = modal.findRequired('.wt-gallery-title');
    this.previous_button = modal.findRequired('[data-wt-gallery-action="previous"]');
    this.next_button = modal.findRequired('[data-wt-gallery-action="next"]');
    this.close_button = modal.findRequired('[data-wt-gallery-action="close"]');

    this.close_button.innerHTML = '<i class="fa-solid fa-times"></i>';
    this.previous_button.innerHTML = '<i class="fa-solid fa-arrow-left wt-icon-flip-rtl"></i>';
    this.next_button.innerHTML = '<i class="fa-solid fa-arrow-right wt-icon-flip-rtl"></i>';

    this.items = [];
    this.current_index = 0;
    this.zoom_scale = MIN_ZOOM;
    this.offset_x = 0;
    this.offset_y = 0;
    this.pointers = new Map();
    this.pan_pointer_id = null;
    this.pan_last_x = 0;
    this.pan_last_y = 0;
    this.pinch_start_distance = 0;
    this.pinch_start_scale = MIN_ZOOM;

    modal.addActionListener('previous', () => this.previous());
    modal.addActionListener('next', () => this.next());
    modal.addActionListener('close', () => this.close());

    this.dialog.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft') {
        this.previous();
      }

      if (event.key === 'ArrowRight') {
        this.next();
      }
    });

    this.image.addEventListener('load', () => this.resetZoom());

    this.stage.addEventListener('wheel', (event) => {
      event.preventDefault();
      this.setZoom(this.zoom_scale * Math.exp(-event.deltaY * 0.0015));
    }, { passive: false });

    this.stage.addEventListener('dblclick', () => {
      this.setZoom(this.zoom_scale === MIN_ZOOM ? 2 : MIN_ZOOM);
    });

    this.stage.addEventListener('pointerdown', (event) => this.onPointerDown(event));
    this.stage.addEventListener('pointermove', (event) => this.onPointerMove(event));
    this.stage.addEventListener('pointerup', (event) => this.onPointerUp(event));
    this.stage.addEventListener('pointercancel', (event) => this.onPointerUp(event));
  }

  /**
   * @param {GalleryItem[]} items
   * @param {number} index
   */
  open(items, index) {
    this.items = items;
    this.show(index);
    this.modal.open();
  }

  close() {
    this.modal.close();
  }

  resetState() {
    this.items = [];
    this.current_index = 0;
    this.resetZoom();
  }

  previous() {
    if (this.current_index > 0) {
      this.show(this.current_index - 1);
    }
  }

  next() {
    if (this.current_index < this.items.length - 1) {
      this.show(this.current_index + 1);
    }
  }

  /**
   * @param {number} index
   */
  show(index) {
    this.current_index = index;

    const item = this.items[this.current_index];

    if (item === undefined) {
      throw new Error('Gallery index ' + this.current_index + ' is out of range.');
    }

    this.image.src = item.source;
    this.image.alt = item.title;

    this.renderMetadata(item);

    this.previous_button.disabled = this.current_index === 0;
    this.next_button.disabled = this.current_index >= this.items.length - 1;
  }

  /**
   * @param {GalleryItem} item
   */
  renderMetadata(item) {
    if (item.title_url !== '') {
      const title_link = document.createElement('a');
      title_link.href = item.title_url;
      title_link.textContent = item.title;
      this.title.replaceChildren(title_link);
    } else {
      this.title.textContent = item.title;
    }

    this.title.hidden = item.title === '';

  }

  /**
   * @param {PointerEvent} event
   */
  onPointerDown(event) {
    if (event.target instanceof Element && event.target.closest('[data-wt-gallery-action]') !== null) {
      return;
    }

    this.stage.setPointerCapture(event.pointerId);
    this.pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });

    if (this.pointers.size === 1) {
      this.pan_pointer_id = event.pointerId;
      this.pan_last_x = event.clientX;
      this.pan_last_y = event.clientY;
    }

    if (this.pointers.size === 2) {
      const [first_pointer, second_pointer] = Array.from(this.pointers.values());
      this.pan_pointer_id = null;
      this.pinch_start_distance = Math.hypot(
        second_pointer.x - first_pointer.x,
        second_pointer.y - first_pointer.y,
      );
      this.pinch_start_scale = this.zoom_scale;
    }
  }

  /**
   * @param {PointerEvent} event
   */
  onPointerMove(event) {
    if (!this.pointers.has(event.pointerId)) {
      return;
    }

    this.pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });

    if (this.pointers.size === 2) {
      const [first_pointer, second_pointer] = Array.from(this.pointers.values());
      const current_distance = Math.hypot(
        second_pointer.x - first_pointer.x,
        second_pointer.y - first_pointer.y,
      );

      if (this.pinch_start_distance > 0) {
        this.setZoom(this.pinch_start_scale * (current_distance / this.pinch_start_distance));
      }

      return;
    }

    if (this.pan_pointer_id === event.pointerId && this.zoom_scale > MIN_ZOOM) {
      this.offset_x += event.clientX - this.pan_last_x;
      this.offset_y += event.clientY - this.pan_last_y;
      this.pan_last_x = event.clientX;
      this.pan_last_y = event.clientY;
      this.updateTransform();
    }
  }

  /**
   * @param {PointerEvent} event
   */
  onPointerUp(event) {
    if (this.stage.hasPointerCapture(event.pointerId)) {
      this.stage.releasePointerCapture(event.pointerId);
    }

    this.pointers.delete(event.pointerId);

    if (this.pointers.size === 1) {
      const [remaining_pointer_id, remaining_pointer] = Array.from(this.pointers.entries())[0];
      this.pan_pointer_id = remaining_pointer_id;
      this.pan_last_x = remaining_pointer.x;
      this.pan_last_y = remaining_pointer.y;
    }

    if (this.pointers.size === 0) {
      this.pan_pointer_id = null;
    }
  }

  resetZoom() {
    this.zoom_scale = MIN_ZOOM;
    this.offset_x = 0;
    this.offset_y = 0;
    this.updateTransform();
  }

  /**
   * @param {number} zoom_scale
   */
  setZoom(zoom_scale) {
    this.zoom_scale = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, zoom_scale));

    if (this.zoom_scale === MIN_ZOOM) {
      this.offset_x = 0;
      this.offset_y = 0;
    }

    this.updateTransform();
  }

  updateTransform() {
    this.image.style.transform = 'translate(' + this.offset_x + 'px, ' + this.offset_y + 'px) scale(' + this.zoom_scale + ')';
    this.stage.classList.toggle('wt-gallery-stage-zoomed', this.zoom_scale > MIN_ZOOM);
  }
}

export function initializeGallery() {
  if (initialized) {
    return;
  }

  initialized = true;

  document.addEventListener('click', (event) => {
    const trigger = findGalleryTrigger(event.target);

    if (trigger === null) {
      return;
    }

    const { items, element_to_index } = collectGalleryItems();

    if (items.length === 0) {
      return;
    }

    const index = element_to_index.get(trigger);

    if (index === undefined) {
      throw new Error('Gallery trigger ' + describeElement(trigger) + ' is not part of the gallery item list.');
    }

    event.preventDefault();

    if (gallery_modal === null) {
      gallery_modal = new GalleryModal();
    }

    gallery_modal.open(items, index);
  });
}
