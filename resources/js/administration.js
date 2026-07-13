'use strict';

// This is a separate webpack entry point from webtrees.js.  Admin page modules
// use `window.webtrees.*` for shared functionality (httpPost, load,
// initializeTomSelect, etc.) rather than importing directly from ../webtrees/,
// because direct imports would duplicate the shared code into both bundles.

import {
  initializeComponentsPage,
  initializeDataFixPage,
  initializeFixLevel0MediaPage,
  initializeLocationEditPage,
  initializeMapImportFormPage,
  initializeMediaPage,
  initializeMergeRecordsStep1Page,
  initializeTreesCheckPage,
  initializeTreesExportPage,
  initializeTreesImportPage,
  initializeTreesPrivacyPage,
  initializeTreesPage,
  initializeUpgradeWizardPage,
  initializeUsersEditPage,
} from './admin/pages';
import { onDocumentReady } from './webtrees/dom';

/**
 * Initialize administration-only page behavior.
 *
 * Keep admin-specific enhancements in this bundle so they do not load on public pages.
 */
function initializeAdministrationPage () {
  initializeComponentsPage();
  initializeDataFixPage();
  initializeFixLevel0MediaPage();
  initializeLocationEditPage();
  initializeMapImportFormPage();
  initializeMediaPage();
  initializeMergeRecordsStep1Page();
  initializeTreesCheckPage();
  initializeTreesExportPage();
  initializeTreesImportPage();
  initializeTreesPrivacyPage();
  initializeTreesPage();
  initializeUpgradeWizardPage();
  initializeUsersEditPage();
}

onDocumentReady(initializeAdministrationPage);

