BUILD_DIR=build
BUILD_NUMBER=$(shell git log --oneline | wc -l)
BUILD_VERSION=$(if $(WT_RELEASE),$(BUILD_NUMBER),$(WT_VERSION)$(WT_RELEASE))
GIT_BRANCH=$(shell git symbolic-ref --short -q HEAD)
LANGUAGE_DIR=language
LANGUAGE_SRC=$(shell git grep -I --name-only --fixed-strings -e WT_I18N:: -- "*.php" "*.xml")
MO_FILES=$(patsubst %.po,%.mo,$(PO_FILES))
PO_FILES=$(wildcard $(LANGUAGE_DIR)/*.po $(LANGUAGE_DIR)/extra/*.po)
SHELL=bash
WT_VERSION=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4)
WT_RELEASE=$(shell grep "'WT_VERSION_RELEASE'" includes/session.php | cut -d "'" -f 4)

# Location of minification tools
CLOSURE_JS=$(BUILD_DIR)/compiler-20121212.jar
CLOSURE_CSS=$(BUILD_DIR)/closure-stylesheets-20111230.jar
YUI_COMPRESSOR=$(BUILD_DIR)/yuicompressor-2.4.7.jar
HTML_COMPRESSION=$(BUILD_DIR)/htmlcompressor-1.5.3.jar

# Files to minify
CSS_FILES=$(shell find $(BUILD_DIR) -name "*.css")
JS_FILES=$(shell find $(BUILD_DIR) -name "*.js")

# Use maximum compression
GZIP=gzip -9

.PHONY: clean update build/webtrees

################################################################################
# Update 
################################################################################
update: $(MO_FILES) $(CSS_RTL_FILES)
	# Set file permissions for a typical server
	chmod -R go-w .
	chmod go+w data
	# Check for PHP syntax errors
	if find . -name '*.php' -not -path './library/Zend/*' -exec php -l {} \; | grep -v "No syntax errors"; then false; else true; fi

################################################################################
# Create a release from this GIT branch
################################################################################
build/webtrees: clean update
	# Check there are no local modifications - only build from a clean working copy
	#git diff-index --quiet HEAD
	# Extract from the repository (not the working copy)
	git archive --prefix=$@/ $(GIT_BRANCH) | tar -x
	# Embed the build number in the code (for DEV builds only)
	sed -i "s/define('WT_RELEASE', 'dev')/define('WT_RELEASE', 'dev-$(BUILD_NUMBER)')/" $@/includes/session.php
	# Add language files
	cp -R $(LANGUAGE_DIR)/*.mo       $@/$(LANGUAGE_DIR)/
	cp -R $(LANGUAGE_DIR)/extra/*.mo $@/$(LANGUAGE_DIR)/extra/
	# Minification
	if [ -z "$(WT_RELEASE)" ]; then find $@ -name "*.js" -exec java -jar $(CLOSURE_JS) --js "{}" --js_output_file "{}.tmp" \; -exec mv "{}.tmp" "{}" \; ; fi
	if [ -z "$(WT_RELEASE)" ]; then find $@ -name "*.css" -exec java -jar $(CLOSURE_CSS) --output-file "{}.tmp" "{}" \; -exec mv "{}.tmp" "{}" \; ; fi
	if [ -z "$(WT_RELEASE)" ]; then find $@ -name "*.js"  -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \; ; fi
	if [ -z "$(WT_RELEASE)" ]; then find $@ -name "*.css" -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \; ; fi
	# Zip up the release files
	cd $(@D) && zip -qr $(@F)-$(BUILD_VERSION).zip $(@F)
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig $@-$(BUILD_VERSION).zip; fi
	# If we have a public html area, publish the files
	if test -d ~/public_html/build; then cp $@-$(BUILD_VERSION).zip ~/public_html/build/; fi
	if test -d ~/public_html/build; then echo ${BUILD_VERSION} > ~/public_html/build/latest-dev.txt; fi
	rm -Rf $@
	# Done!
	ls -l $@-$(BUILD_VERSION).zip*
	false
	find webtrees/modules_v3 -name "*.xml" | while read file; do sed -e 's~\(WT_I18N::[^)]*[)]\)~<?php echo \1; ?>~g' $$file > webtrees_tmp/$$(echo $$file.php | cut -c 10- | tr / _); done

################################################################################
# Remove temporary and intermediate files
################################################################################
clean:
	rm -Rf build/webtrees*

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
CSS_LTR_FILES=$(shell find build/webtrees -name "*-ltr.css")
CSS_RTL_FILES=$(patsubst %-ltr.css,%-rtl.css,$(CSS_LTR_FILES))

%-rtl.css: %-ltr.css
	java -jar $(CLOSURE_STYLESHEETS) --output-orientation RTL -o $@ $<
