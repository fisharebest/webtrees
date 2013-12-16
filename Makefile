BUILD_DIR=build
BUILD_NUMBER=$(shell git log --oneline | wc -l)
BUILD_VERSION=$(if $(WT_RELEASE),$(BUILD_NUMBER),$(WT_VERSION)$(WT_RELEASE))
GIT_BRANCH=$(git symbolic-ref -q HEAD || git describe --tags --exact-match)
LANGUAGE_DIR=language
LANGUAGE_SRC=$(shell git grep -I --name-only --fixed-strings -e WT_I18N:: -- "*.php" "*.xml")
MO_FILES=$(patsubst %.po,%.mo,$(PO_FILES))
PO_FILES=$(wildcard $(LANGUAGE_DIR)/*.po $(LANGUAGE_DIR)/extra/*.po)
SHELL=bash
WT_VERSION=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4 | awk -F - '{print $$1}')
WT_RELEASE=$(shell grep "'WT_VERSION'" includes/session.php | cut -d "'" -f 4 | awk -F - '{print $$2}')

# Location of minification tools
CLOSURE_JS=$(BUILD_DIR)/compiler-20121212.jar
CLOSURE_CSS=$(BUILD_DIR)/closure-stylesheets-20111230.jar
YUI_COMPRESSOR=$(BUILD_DIR)/yuicompressor-2.4.7.jar
HTML_COMPRESSION=$(BUILD_DIR)/htmlcompressor-1.5.3.jar

# Files to minify
CSS_FILES=$(shell find $(BUILD_DIR) -name "*.css")
JS_FILES=$(shell find $(BUILD_DIR) -name "*.js")
# Files to mirror
CSS_LTR_FILES=$(shell find . -name "*-ltr.css")
CSS_RTL_FILES=$(patsubst %-ltr.css,%-rtl.css,$(CSS_LTR_FILES))
PNG_LTR_FILES=$(shell find . -name "*-ltr.png")
PNG_RTL_FILES=$(patsubst %-ltr.css,%-rtl.png,$(PNG_LTR_FILES))

# Use maximum compression
GZIP=gzip -9

.PHONY: clean update check build/webtrees

################################################################################
# Update 
################################################################################
update: $(MO_FILES) $(CSS_RTL_FILES) $(PNG_RTL_FILES)

################################################################################
# Check for PHP syntax errors
################################################################################
check:
	if find . -name '*.php' -not -path './library/Zend/*' -exec php -l {} \; | grep -v "No syntax errors"; then false; else true; fi

################################################################################
# Create a release from this GIT branch
################################################################################
build/webtrees: clean update
	# Extract from the repository, to filter files using .gitattributes
	git archive --prefix=$@/ $(GIT_BRANCH) | tar -x
	# Embed the build number in the code (for DEV builds only)
	sed -i "s/define('WT_RELEASE', '$(WT_VERSION)-dev')/define('WT_RELEASE', '$(WT_VERSION)-dev+$(BUILD_NUMBER)')/" $@/includes/session.php
	# Add language files
	cp -R $(LANGUAGE_DIR)/*.mo       $@/$(LANGUAGE_DIR)/
	cp -R $(LANGUAGE_DIR)/extra/*.mo $@/$(LANGUAGE_DIR)/extra/
	# Minification
	find $@ -name "*.js" -exec java -jar $(CLOSURE_JS) --js "{}" --js_output_file "{}.tmp" \; -exec mv "{}.tmp" "{}" \;
	find $@ -name "*.css" -exec java -jar $(CLOSURE_CSS) --output-file "{}.tmp" "{}" \; -exec mv "{}.tmp" "{}" \;
	find $@ -name "*.js"  -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;
	find $@ -name "*.css" -exec java -jar $(YUI_COMPRESSOR) -o "{}" "{}" \;
	# Zip up the release files
	cd $(@D) && zip -qr $(@F)-$(BUILD_VERSION).zip $(@F)
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig $@-$(BUILD_VERSION).zip; fi
	rm -Rf $@
	# Done!
	ls -l $@-$(BUILD_VERSION).zip*

################################################################################
# Remove temporary and intermediate files
################################################################################
clean:
	rm -Rf build/webtrees*
	find language -name "*.mo" -not -path "language/en_US.mo" -delete

################################################################################
# Gettext template (.POT) file
################################################################################
language/webtrees.pot: $(LANGUAGE_SRC)
	# Modify the .XML report files so that xgettext can scan them
	find modules*/ -name "*.xml" -exec cp -p {} {}.bak \;
	sed -i -e 's~\(WT_I18N::[^)]*[)]\)~<?php echo \1; ?>~g' modules*/*/*.xml
	echo $^ | xargs xgettext --package-name=webtrees --package-version=1.0 --msgid-bugs-address=i18n@webtrees.net --output=$@ --no-wrap --language=PHP --add-comments=I18N --from-code=utf-8 --keyword --keyword=translate:1 --keyword=translate_c:1c,2 --keyword=plural:1,2 --keyword=noop:1
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

################################################################################
# Automatically generate RTL stylesheets from LTR stylesheets
################################################################################
%-rtl.css: %-ltr.css
	java -jar $(CLOSURE_CSS) --output-orientation RTL --pretty-print -o $@ $<

################################################################################
# Automatically generate RTL images from LTR images
################################################################################
%-rtl.png: %-ltr.png
	convert $< -flop $@
