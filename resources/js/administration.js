'use strict';

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

