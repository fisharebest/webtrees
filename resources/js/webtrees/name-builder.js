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
// Order matters: Jpan and Kore come before Han, since Japanese and Korean also use Han characters.
// Latn comes last, since other texts often contain some Latin characters.
const scriptRegexes = [
  ['Arab', /\p{Script=Arabic}/u],
  ['Armn', /\p{Script=Armenian}/u],
  ['Cyrl', /\p{Script=Cyrillic}/u],
  ['Deva', /\p{Script=Devanagari}/u],
  ['Geor', /\p{Script=Georgian}/u],
  ['Grek', /\p{Script=Greek}/u],
  ['Jpan', /[\p{Script=Hiragana}\p{Script=Katakana}]/u],
  ['Kore', /\p{Script=Hangul}/u],
  ['Han', /\p{Script=Han}/u],
  ['Hebr', /\p{Script=Hebrew}/u],
  ['Java', /\p{Script=Javanese}/u],
  ['Sund', /\p{Script=Sundanese}/u],
  ['Taml', /\p{Script=Tamil}/u],
  ['Thaa', /\p{Script=Thaana}/u],
  ['Thai', /\p{Script=Thai}/u],
  ['Latn', /\p{Script=Latin}/u],
];

/**
 * Tidy the whitespace in a string.
 * @param {string} str
 * @returns {string}
 */
function trim(str) {
  return str.replace(/\s+/g, ' ').trim();
}

/**
 * Detect the script used by some text.
 * @param {string} str
 * @returns {string}
 */
export function detectScript(str) {
  for (const [script, regex] of scriptRegexes) {
    if (regex.test(str)) {
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
  const script = detectScript(npfx + givn + spfx + surn + nsfx);
  const usesEastAsian = ['Han', 'Jpan', 'Kore'].includes(script);
  const separator = usesEastAsian ? '' : ' ';
  const surnameFirst = usesEastAsian || ['hu', 'ja', 'ko', 'vi', 'zh-Hans', 'zh-Hant'].includes(lang);
  const patronym = lang === 'is';
  const slash = patronym ? '' : '/';

  // GIVN and SURN may be comma-separated lists.
  npfx = trim(npfx);
  givn = trim(givn.replace(/,/g, separator));
  spfx = trim(spfx);
  surn = inflectSurname(trim(surn.replace(/,/g, separator)), sex);
  nsfx = trim(nsfx);

  const surname_separator = spfx.endsWith('\'') || spfx.endsWith('\u2019') ? '' : ' ';

  const surname = trim(spfx + surname_separator + surn);

  const name = surnameFirst ?
    slash + surname + slash + separator + givn :
    givn + separator + slash + surname + slash;

  return trim(npfx + separator + name + separator + nsfx);
}
