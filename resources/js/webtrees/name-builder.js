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

const lang = document.documentElement.lang;

// Identify the script used by some text.
const scriptRegexes = {
  Han: /[\u3400-\u9FCC]/,
  Grek: /[\u0370-\u03FF]/,
  Cyrl: /[\u0400-\u04FF]/,
  Hebr: /[\u0590-\u05FF]/,
  Arab: /[\u0600-\u06FF]/
};

/**
 * Tidy the whitespace in a string.
 * @param {string} str
 * @returns {string}
 */
function trim(str) {
  return str.replace(/\s+/g, ' ').trim();
}

/**
 * Look for non-latin characters in a string.
 * @param {string} str
 * @returns {string}
 */
export function detectScript(str) {
  for (const script in scriptRegexes) {
    if (str.match(scriptRegexes[script])) {
      return script;
    }
  }

  return 'Latn';
}

/**
 * In some languages, the SURN uses a male/default form, but NAME uses a gender-inflected form.
 * @param {string} surname
 * @param {string} sex
 * @returns {string}
 */
function inflectSurname(surname, sex) {
  if (lang === 'pl' && sex === 'F') {
    return surname
      .replace(/ski$/, 'ska')
      .replace(/cki$/, 'cka')
      .replace(/dzki$/, 'dzka')
      .replace(/żki$/, 'żka');
  }

  return surname;
}

/**
 * Build a NAME from a NPFX, GIVN, SPFX, SURN and NSFX parts.
 * Assumes the language of the document is the same as the language of the name.
 * @param {string} npfx
 * @param {string} givn
 * @param {string} spfx
 * @param {string} surn
 * @param {string} nsfx
 * @param {string} sex
 * @returns {string}
 */
export function buildNameFromParts(npfx, givn, spfx, surn, nsfx, sex) {
  const usesCJK = detectScript(npfx + givn + spfx + givn + surn + nsfx) === 'Han';
  const separator = usesCJK ? '' : ' ';
  const surnameFirst = usesCJK || ['hu', 'jp', 'ko', 'vi', 'zh-Hans', 'zh-Hant'].indexOf(lang) !== -1;
  const patronym = ['is'].indexOf(lang) !== -1;
  const slash = patronym ? '' : '/';

  // GIVN and SURN may be a comma-separated lists.
  npfx = trim(npfx);
  givn = trim(givn.replace(/,/g, separator));
  spfx = trim(spfx);
  surn = inflectSurname(trim(surn.replace(/,/g, separator)), sex);
  nsfx = trim(nsfx);

  const surname_separator = spfx.endsWith('\'') || spfx.endsWith('\u2019') ? '' : ' ';

  const surname = trim(spfx + surname_separator + surn);

  const name = surnameFirst ? slash + surname + slash + separator + givn : givn + separator + slash + surname + slash;

  return trim(npfx + separator + name + separator + nsfx);
}

