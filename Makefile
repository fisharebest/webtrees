# webtrees: online genealogy
# Copyright (C) 2019 webtrees development team
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.

BUILD_DIR=build
#BUILD_VERSION=$(WT_VERSION)$(WT_RELEASE)
BUILD_VERSION=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4)
GIT_BRANCH=$(shell git symbolic-ref -q HEAD || git describe --tags --exact-match)
LANGUAGE_DIR=language
LANGUAGE_SRC=$(shell git grep -I --name-only --fixed-strings -e I18N:: -- "*.php" "*.xml")
MO_FILES=$(patsubst %.po,%.mo,$(PO_FILES))
PO_FILES=$(wildcard $(LANGUAGE_DIR)/*.po)
SHELL=bash
WT_VERSION=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4 | awk -F - '{print $$1}')
WT_RELEASE=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4 | awk -F - '{print $$2}')

.PHONY: clean webtrees

################################################################################
# Create a release from this GIT branch
################################################################################
webtrees: clean $(MO_FILES)
	mkdir -p $@
	# Compiling the language files causes comment/whitespace changes in the .PO files
	git checkout language
	# Extract from the repository, to filter files using .gitattributes
	git archive --prefix=$@/ $(GIT_BRANCH) | tar -x
	# Add language files
	cp -R $(LANGUAGE_DIR)/*.mo $@/$(LANGUAGE_DIR)/
	# Zip up the release files
	zip -qr9 $@-$(BUILD_VERSION).zip $@
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then rm -f $@-$(BUILD_VERSION).zip.asc && gpg --armor --sign --detach-sig $@-$(BUILD_VERSION).zip; fi
	rm -Rf $@
	# Done!
	ls -l $@-$(BUILD_VERSION).zip{,.asc}

################################################################################
# Remove temporary and intermediate files
################################################################################
clean:
	rm -Rf build language/webtrees.pot
	find language -name "*.mo" -not -path "language/en-US.mo" -delete

################################################################################
# Gettext template (.POT) file
################################################################################
language/webtrees.pot: $(LANGUAGE_SRC)
	# Modify the .XML report files so that xgettext can scan them
	find modules*/ -name "*.xml" -exec cp -p {} {}.bak \;
	sed -i .bak -e 's~\(I18N::[^)]*[)]\)~<?= \1 ?>~g' modules*/*/*.xml
	echo $^ | xargs xgettext --package-name=webtrees --package-version=1.0 --msgid-bugs-address=i18n@webtrees.net --output=$@ --no-wrap --language=PHP --add-comments=I18N --from-code=utf-8 --keyword --keyword=translate:1 --keyword=translateContext:1c,2 --keyword=plural:1,2 --keyword=noop:1
	# Restore the .XML files
	find modules*/ -name "*.xml" -exec mv {}.bak {} \;

################################################################################
# Gettext catalog (.PO) files
################################################################################
$(PO_FILES): language/webtrees.pot
	msgmerge --no-wrap --sort-output --no-fuzzy-matching --output=$@ $@ $<

################################################################################
# Gettext translation (.MO) files
################################################################################
%.mo: %.po
	msgfmt --output=$@ $<
