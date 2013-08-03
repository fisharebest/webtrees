LANGUAGES=ar bg bs ca cs da de el en_GB en_US es et fa fi fr he hr hu is it lt nb nl nn pl pt pt_BR ru sk sl sv tr tt uk vi zh_CN
LANGUAGE_DIR=language
LANGUAGE_SRC=$(shell git grep -I --name-only --fixed-strings -e WT_I18N:: -- "*.php" "*.xml")
PO_FILES=$(wildcard $(LANGUAGE_DIR)/*.po $(LANGUAGE_DIR)/extra/*.po)
MO_FILES=$(patsubst %.po,%.mo,$(PO_FILES))
MO_FILES_DIST=$(patsubst %,$(LANGUAGE_DIR)/%.mo,$(LANGUAGES))
SHELL=bash
BUILD_NUMBER=$(shell git log --oneline | wc -l)
GIT_BRANCH=$(shell git symbolic-ref --short -q HEAD)
WT_VERSION=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4)
WT_VERSION_RELEASE=$(shell grep "'WT_VERSION_RELEASE'" includes/session.php | cut -d "'" -f 4)
BUILD_VERSION=$(if $(WT_VERSION_RELEASE),$(BUILD_NUMBER),$(WT_VERSION)$(WT_VERSION_RELEASE))

BUILD_DIR=build

# Location of minification tools
CLOSURE_JS=$(BUILD_DIR)/compiler-20121212.jar
CLOSURE_CSS=$(BUILD_DIR)/closure-stylesheets-20111230.jar
YUI_COMPRESSOR=$(BUILD_DIR)/yuicompressor-2.4.7.jar
HTML_COMPRESSION=$(BUILD_DIR)/htmlcompressor-1.5.3.jar

# Use maximum compression
GZIP=gzip -9

.PHONY: clean default build/webtrees

################################################################################
# Create a release from this SVN working copy
################################################################################
default: build/webtrees
	# Package up the extra language files
	zip -qr language-extra.zip $(MO_EXTRA)
	rm -f $(MO_EXTRA)
	zip -qr language.zip $^/language
	zip -qr webtrees-$(BUILD_VERSION).zip $^
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig webtrees-$(BUILD_VERSION).zip; fi
	# If we have a public html area, publish the files
	if test -d ~/public_html/build; then cp language*.zip webtrees*.zip ~/public_html/build/ && echo ${BUILD_VERSION} > ~/public_html/build/latest-dev.txt; fi
	false
	# Create an updated message catalog
	mkdir -p webtrees_tmp
	rm -Rf webtrees_tmp/*
	find webtrees/modules_v3 -name "*.xml" | while read file; do sed -e 's~\(WT_I18N::[^)]*[)]\)~<?php echo \1; ?>~g' $$file > webtrees_tmp/$$(echo $$file.php | cut -c 10- | tr / _); done
	find webtrees webtrees_tmp -name "*.php" | xargs xgettext --package-name=webtrees --package-version=1.0 --msgid-bugs-address=i18n@webtrees.net --output=webtrees.pot --no-wrap --language=PHP --add-comments=I18N --from-code=utf-8 --keyword --keyword=translate:1 --keyword=translate_c:1c,2 --keyword=plural:1,2 --keyword=noop:1
	rm -Rf webtrees_tmp webtrees

################################################################################
# Remove temporary and intermediate files
################################################################################
clean:
	rm -Rf build/webtrees*

################################################################################
# Create a release from this GIT branch
################################################################################
build/webtrees: $(MO_FILES)
	rm -Rf $@
	# Remove the group-write permission - some hosts don't like it
	chmod -R go-w .
	# Hosts running PHP as an Apache module may need this
	chmod go+w data
	# Check there are no local modifications - only build from a clean working copy
	#git diff-index --quiet HEAD
	# Extract from the repository (not the working copy)
	git archive --prefix=$@/ $(GIT_BRANCH) | tar -x
	# Embed the build number in the code (for DEV builds only)
	sed -i "s/define('WT_VERSION_RELEASE', 'dev')/define('WT_VERSION_RELEASE', 'dev-$(BUILD_NUMBER)')/" $@/includes/session.php
	# Check for syntax errors
	if find $@ -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"; then false; else true; fi
	# Add language files
	cp -R $(LANGUAGE_DIR)/* $@/language/
	# Minification - first pass - using closure tools
	#find $@ -name "*.js" -exec java -jar $(CLOSURE_JS) --js "{}" --js_output_file "{}.tmp" \; -exec mv "{}.tmp" "{}" \;
	#find $@ -name "*.css" -exec java -jar $(CLOSURE_CSS) --output-file "{}.tmp" "{}" \; -exec mv "{}.tmp" "{}" \;
	# Minification - second pass - using YUI tools
	#find $@ -name "*.js"  -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;
	#find $@ -name "*.css" -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;

################################################################################
# Gettext template (.POT) file
################################################################################
language/webtrees.pot: $(LANGUAGE_SRC)
	echo $^ | xargs xgettext --package-name=webtrees --package-version=1.0 --msgid-bugs-address=i18n@webtrees.net --output=$@ --no-wrap --language=PHP --add-comments=I18N --from-code=utf-8 --keyword --keyword=translate:1 --keyword=translate_c:1c,2 --keyword=plural:1,2 --keyword=noop:1

################################################################################
# Gettext catalog (.PO) files
################################################################################
$(PO_FILES): language/webtrees.pot
	msgmerge --no-wrap --sort-output --output=$@ $@ $<

################################################################################
# Gettext translation (.MO) files
################################################################################
%.mo: %.po
	msgfmt --output=$@ $<

################################################################################
# Automatically generate RTL stylesheets from LTR stylesheets
################################################################################
CSS_LTR_FILES=$(shell find .. -name "*-ltr.css")
CSS_RTL_FILES=$(patsubst %-ltr.css,%-rtl.css,$(CSS_LTR_FILES))

%-rtl.css: %-ltr.css
	java -jar $(CLOSURE_STYLESHEETS) --output-orientation RTL -o $@ $<

css-rtl: $(CSS_RTL_FILES)

################################################################################
# Minify static resources
################################################################################
CSS_FILES=$(shell find .. -name "*.css")
JS_FILES=$(shell find .. -name "*.js")

minify: clean css-rtl
	# Minification - first pass - using closure tools
	find .. -name "*.js" -exec java -jar $(CLOSURE_JS) --js "{}" --js_output_file "{}.tmp" \; -exec mv "{}.tmp" "{}" \;
	find .. -name "*.css" -exec java -jar $(CLOSURE_CSS) --output-file "{}.tmp" "{}" \; -exec mv "{}.tmp" "{}" \;
	# Minification - second pass - using YUI tools
	find .. -name "*.js"  -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;
	find .. -name "*.css" -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;
