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

import { requireDatasetValue, requireElement } from '../dom';

/**
 * Text areas do not support the pattern attribute, so apply it via data-wt-pattern.
 *
 * @param {HTMLFormElement} form
 */
export function textareaPatterns(form) {
  form.addEventListener('submit', function (event) {
    event.target.querySelectorAll('textarea[data-wt-pattern]').forEach(function (element) {
      const pattern = new RegExp('^' + element.dataset.wtPattern + '$');

      if (!element.readOnly && element.value !== '' && !pattern.test(element.value)) {
        event.preventDefault();
        event.stopPropagation();
        element.classList.add('is-invalid');
        element.scrollIntoView();
      } else {
        element.classList.remove('is-invalid');
      }
    });
  });
}

/**
 * Initialize forms that use textarea data-wt-pattern validation.
 *
 * @param {ParentNode} root
 */
export function initializeTextareaPatternForms(root) {
  root.querySelectorAll('form[data-wt-textarea-pattern-form]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      throw new Error('Textarea pattern container must be a form element.');
    }

    if (form.dataset.wtTextareaPatternFormInitialized === '1') {
      return;
    }

    form.dataset.wtTextareaPatternFormInitialized = '1';
    textareaPatterns(form);
  });
}

/**
 * Initialize hidden captcha field pairs.
 *
 * @param {ParentNode} root
 */
export function initializeCaptchaFields(root) {
  root.querySelectorAll('[data-wt-captcha]').forEach((container) => {
    const xField = requireElement(container, '[data-wt-captcha-field="x"]', HTMLInputElement, 'captcha x field');
    const yField = requireElement(container, '[data-wt-captcha-field="y"]', HTMLInputElement, 'captcha y field');

    xField.value = yField.value;
  });
}

/**
 * Initialize password visibility toggles.
 *
 * @param {ParentNode} root
 */
export function initializePasswordToggles(root) {
  root.querySelectorAll('[data-wt-password-toggle]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Password toggle control must be a button element.');
    }

    if (button.dataset.wtPasswordToggleInitialized === '1') {
      return;
    }

    button.dataset.wtPasswordToggleInitialized = '1';

    const targetId = requireDatasetValue(button, 'wtPasswordTarget', 'password input ID');
    const passwordField = document.getElementById(targetId);

    if (!(passwordField instanceof HTMLInputElement)) {
      throw new Error('Password input not found for ID: ' + targetId);
    }

    button.addEventListener('click', () => {
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        button.title = requireDatasetValue(button, 'wtHidePasswordTitle', 'hide password title');
        button.textContent = requireDatasetValue(button, 'wtHidePasswordText', 'hide password text');
      } else {
        passwordField.type = 'password';
        button.title = requireDatasetValue(button, 'wtShowPasswordTitle', 'show password title');
        button.textContent = requireDatasetValue(button, 'wtShowPasswordText', 'show password text');
      }

      passwordField.focus();
    });
  });
}

/**
 * Initialize generated-name edit addons.
 *
 * @param {ParentNode} root
 */
export function initializeEditNameAddons(root) {
  root.querySelectorAll('[data-wt-edit-name-addon]').forEach((link) => {
    if (!(link instanceof HTMLAnchorElement)) {
      throw new Error('Edit-name addon control must be a link element.');
    }

    if (link.dataset.wtEditNameAddonInitialized === '1') {
      return;
    }

    link.dataset.wtEditNameAddonInitialized = '1';

    const nameInputId = requireDatasetValue(link, 'wtNameInputId', 'name input ID');
    const nameInput = document.getElementById(nameInputId);
    const disabledInput = document.getElementById(nameInputId + '-disabled');

    if (!(nameInput instanceof HTMLInputElement) || !(disabledInput instanceof HTMLInputElement)) {
      throw new Error('Name edit inputs not found for ID: ' + nameInputId);
    }

    const setNameValue = (value) => {
      nameInput.value = value;
      disabledInput.value = value;
    };

    const revealEditor = () => {
      disabledInput.classList.add('d-none');
      nameInput.classList.remove('d-none');
      nameInput.focus();

      const addon = link.closest('.input-group-text');

      if (addon instanceof Element) {
        addon.remove();
      }
    };

    link.addEventListener('click', (event) => {
      event.preventDefault();
      revealEditor();
    });

    const inputGroup = nameInput.closest('.input-group');

    if (!(inputGroup instanceof Element)) {
      throw new Error('Name input must be inside an input-group.');
    }

    const formGroup = inputGroup.closest('.mb-3');

    if (!(formGroup instanceof Element)) {
      throw new Error('Name input-group must be inside a .mb-3 container.');
    }

    // NAME has sibling parts (NPFX/GIVN/SPFX/SURN/NSFX); ROMN/FONE store parts in the following collapsible panel.
    const container = nameInput.id.endsWith('-INDI-NAME')
      ? formGroup.parentElement
      : formGroup.nextElementSibling;

    if (!(container instanceof Element)) {
      throw new Error('Name parts container not found for input: ' + nameInput.id);
    }

    const npfx = container.querySelector('[id$="-NPFX"]');
    const givn = container.querySelector('[id$="-GIVN"]');
    const spfx = container.querySelector('[id$="-SPFX"]');
    const surn = container.querySelector('[id$="-SURN"]');
    const nsfx = container.querySelector('[id$="-NSFX"]');

    const generatedName = () => window.webtrees.buildNameFromParts(
      npfx instanceof HTMLInputElement ? npfx.value : '',
      givn instanceof HTMLInputElement ? givn.value : '',
      spfx instanceof HTMLInputElement ? spfx.value : '',
      surn instanceof HTMLInputElement ? surn.value : '',
      nsfx instanceof HTMLInputElement ? nsfx.value : '',
      'U',
    );

    const initialName = generatedName();

    if (nameInput.value === '') {
      setNameValue(initialName);
    }

    if (nameInput.value !== initialName) {
      revealEditor();
      return;
    }

    const syncIfHidden = () => {
      if (nameInput.classList.contains('d-none')) {
        setNameValue(generatedName());
      }
    };

    if (npfx instanceof HTMLInputElement) npfx.addEventListener('input', syncIfHidden);
    if (givn instanceof HTMLInputElement) givn.addEventListener('input', syncIfHidden);
    if (spfx instanceof HTMLInputElement) spfx.addEventListener('input', syncIfHidden);
    if (surn instanceof HTMLInputElement) {
      surn.addEventListener('input', syncIfHidden);
      surn.addEventListener('blur', syncIfHidden);
    }
    if (nsfx instanceof HTMLInputElement) nsfx.addEventListener('input', syncIfHidden);
  });
}

/**
 * Initialize copy-to-clipboard buttons.
 *
 * @param {ParentNode} root
 */
export function initializeCopyButtons(root) {
  root.querySelectorAll('[data-wt-copy-button]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Copy control must be a button element.');
    }

    if (button.dataset.wtCopyButtonInitialized === '1') {
      return;
    }

    button.dataset.wtCopyButtonInitialized = '1';

    button.addEventListener('click', () => {
      const targetSelector = requireDatasetValue(button, 'wtCopyTarget', 'copy target selector');
      const message = requireDatasetValue(button, 'wtCopyMessage', 'copy confirmation message');
      const input = requireElement(document, targetSelector, HTMLInputElement, 'copy target input');

      if (navigator.clipboard) {
        navigator.clipboard.writeText(input.value);
      } else {
        input.select();
        document.execCommand('copy');
      }

      alert(message);
    });
  });
}

/**
 * Initialize generic "show more" buttons that reveal hidden rows in a block.
 *
 * @param {ParentNode} root
 */
export function initializeShowMoreButtons(root) {
  root.querySelectorAll('[data-wt-show-more-button]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Show-more control must be a button element.');
    }

    if (button.dataset.wtShowMoreInitialized === '1') {
      return;
    }

    button.dataset.wtShowMoreInitialized = '1';

    button.addEventListener('click', () => {
      const blockSelector = requireDatasetValue(button, 'wtShowMoreBlock', 'show-more block selector');
      const block = requireElement(document, blockSelector, HTMLElement, 'show-more block');
      const hiddenRows = block.querySelectorAll('.d-none');

      if (hiddenRows.length === 0) {
        throw new Error('Expected hidden rows in show-more block: ' + blockSelector);
      }

      hiddenRows.forEach((row) => row.classList.remove('d-none'));
      button.remove();
    });
  });
}

/**
 * Initialize per-message delete buttons that submit only one selected message.
 *
 * @param {ParentNode} root
 */
export function initializeSingleMessageDeleteButtons(root) {
  root.querySelectorAll('[data-wt-single-message-delete]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Single-message delete control must be a button element.');
    }

    if (button.dataset.wtSingleMessageDeleteInitialized === '1') {
      return;
    }

    button.dataset.wtSingleMessageDeleteInitialized = '1';

    button.addEventListener('click', async () => {
      const confirmed = await window.webtrees.confirmDialog(requireDatasetValue(button, 'wtConfirm', 'single-message delete confirmation'));

      if (!confirmed) {
        return;
      }

      const formSelector = requireDatasetValue(button, 'wtSingleMessageForm', 'single-message delete form selector');
      const form = requireElement(document, formSelector, HTMLFormElement, 'single-message delete form');
      const messageId = requireDatasetValue(button, 'wtMessageId', 'single-message ID');
      const messageCheckbox = requireElement(form, '#cb_message' + messageId, HTMLInputElement, 'single-message checkbox');

      form.querySelectorAll(':scope input[type="checkbox"]').forEach((checkbox) => {
        if (checkbox instanceof HTMLInputElement) {
          checkbox.checked = false;
        }
      });

      messageCheckbox.checked = true;
      form.submit();
    });
  });
}

/**
 * Initialize checkbox group action buttons.
 *
 * @param {ParentNode} root
 */
export function initializeCheckboxActionButtons(root) {
  root.querySelectorAll('[data-wt-checkbox-action]').forEach((button) => {
    if (!(button instanceof HTMLElement)) {
      throw new Error('Checkbox action control must be an HTML element.');
    }

    if (button.dataset.wtCheckboxActionInitialized === '1') {
      return;
    }

    button.dataset.wtCheckboxActionInitialized = '1';

    button.addEventListener('click', (event) => {
      event.preventDefault();

      const selector = requireDatasetValue(button, 'wtCheckboxTarget', 'checkbox target selector');
      const action = requireDatasetValue(button, 'wtCheckboxAction', 'checkbox action');
      const checkboxes = document.querySelectorAll(selector);

      if (checkboxes.length === 0) {
        throw new Error('No checkboxes found for selector: ' + selector);
      }

      checkboxes.forEach((checkbox) => {
        if (!(checkbox instanceof HTMLInputElement) || checkbox.type !== 'checkbox') {
          throw new Error('Checkbox target selector must match only checkbox inputs.');
        }

        if (action === 'all') {
          checkbox.checked = true;
          return;
        }

        if (action === 'none') {
          checkbox.checked = false;
          return;
        }

        if (action === 'invert') {
          checkbox.checked = !checkbox.checked;
          return;
        }

        throw new Error('Unsupported checkbox action: ' + action);
      });
    });
  });
}



/**
 * Initialize controls that toggle a target element's visibility.
 *
 * @param {ParentNode} root
 */
export function initializeToggleTargetControls(root) {
  root.querySelectorAll('[data-wt-toggle-target]').forEach((control) => {
    if (!(control instanceof HTMLInputElement)) {
      throw new Error('Toggle target control must be an input element.');
    }

    if (control.dataset.wtToggleTargetInitialized === '1') {
      return;
    }

    control.dataset.wtToggleTargetInitialized = '1';

    control.addEventListener('change', () => {
      const selector = requireDatasetValue(control, 'wtToggleTarget', 'toggle target selector');
      const mode = control.dataset.wtToggleMode || 'checked';
      const target = requireElement(document, selector, HTMLElement, 'toggle target element');

      if (mode === 'checked') {
        target.style.display = control.checked ? '' : 'none';
        return;
      }

      throw new Error('Unsupported toggle mode: ' + mode);
    });
  });
}

/**
 * Initialize buttons that write a generated value into a target input.
 *
 * @param {ParentNode} root
 */
export function initializeGeneratedValueButtons(root) {
  root.querySelectorAll('button[data-wt-generated-value-target][data-wt-generated-value]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Generated-value control must be a button element.');
    }

    if (button.dataset.wtGeneratedValueInitialized === '1') {
      return;
    }

    button.dataset.wtGeneratedValueInitialized = '1';

    const targetId = requireDatasetValue(button, 'wtGeneratedValueTarget', 'generated-value target ID');
    const target = document.getElementById(targetId);

    if (!(target instanceof HTMLInputElement)) {
      throw new Error('Generated-value target not found for ID: ' + targetId);
    }

    button.addEventListener('click', () => {
      target.value = requireDatasetValue(button, 'wtGeneratedValue', 'generated value');
    });
  });
}





/**
 * Initialize relationships chart swap-individuals buttons.
 *
 * @param {ParentNode} root
 */
export function initializeSwapIndividualsButtons(root) {
  root.querySelectorAll('[data-wt-swap-individuals-button]').forEach((button) => {
    if (!(button instanceof HTMLButtonElement)) {
      throw new Error('Swap-individuals control must be a button element.');
    }

    if (button.dataset.wtSwapIndividualsInitialized === '1') {
      return;
    }

    button.dataset.wtSwapIndividualsInitialized = '1';

    button.addEventListener('click', () => {
      const first = requireElement(document, requireDatasetValue(button, 'wtSwapFirst', 'first individual selector'), HTMLInputElement, 'first individual input');
      const second = requireElement(document, requireDatasetValue(button, 'wtSwapSecond', 'second individual selector'), HTMLInputElement, 'second individual input');
      const form = requireElement(document, requireDatasetValue(button, 'wtSwapForm', 'swap form selector'), HTMLFormElement, 'relationships form');

      const firstName = first.name;
      first.name = second.name;
      second.name = firstName;

      form.submit();
    });
  });
}

/**
 * Initialize setup step 4 MySQL connection controls.
 *
 * @param {ParentNode} root
 */
export function initializeSetupMysqlConnection(root) {
  root.querySelectorAll('form[data-wt-setup-mysql-connection]').forEach((form) => {
    if (!(form instanceof HTMLFormElement)) {
      throw new Error('MySQL setup container must be a form element.');
    }

    if (form.dataset.wtSetupMysqlConnectionInitialized === '1') {
      return;
    }

    form.dataset.wtSetupMysqlConnectionInitialized = '1';

    const inputDbHost = requireElement(form, '#dbhost', HTMLInputElement, 'database host input');
    const localType = requireElement(form, '#mysql-type-local', HTMLInputElement, 'local mysql type radio');
    const networkType = requireElement(form, '#mysql-type-network', HTMLInputElement, 'network mysql type radio');

    localType.addEventListener('click', () => {
      inputDbHost.value = '';
    });

    inputDbHost.addEventListener('change', () => {
      if (inputDbHost.value === 'localhost') {
        inputDbHost.value = '';
      }

      if (inputDbHost.value === '') {
        localType.click();
      } else {
        networkType.click();
      }
    });

    form.addEventListener('submit', () => {
      if (inputDbHost.value === '') {
        inputDbHost.value = 'localhost';
      }
    });
  });
}

/**
 * Initialize setup base URL hidden fields.
 *
 * @param {ParentNode} root
 */
export function initializeSetupBaseUrlFields(root) {
  root.querySelectorAll('input[data-wt-base-url-field]').forEach((field) => {
    if (!(field instanceof HTMLInputElement)) {
      throw new Error('Base URL field must be an input element.');
    }

    if (field.dataset.wtBaseUrlFieldInitialized === '1') {
      return;
    }

    field.dataset.wtBaseUrlFieldInitialized = '1';
    field.value = decodeURI(location.href.split(/\?|#|index\.php/)[0].replace(/\/+$/, ''));
  });
}

