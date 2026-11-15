import {
  initializePasswordToggles,
  initializeSetupBaseUrlFields,
  initializeSetupMysqlConnection,
} from './webtrees/forms/misc';

initializePasswordToggles(document);
initializeSetupBaseUrlFields(document);
initializeSetupMysqlConnection(document);
