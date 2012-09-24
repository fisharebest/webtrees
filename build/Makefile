LANGUAGES_BUILD=ar bg bs ca cs da de en_GB en_US es et fa fi fr he hr hu is it lt nb nl nn pl pt pt_BR ru sk sl sv tr tt uk vi
LANGUAGES_EXTRA=af dv el en_AU fo fr_CA gl id ja ko lv ka mr ms ne oc ro sr sr@Latn ta zh
# Note that we also have translations for mi (maori) and yi (yiddish), although Zend Framework v1 does not include support for these.
PO_DIR=po
MO_DIR=mo
MO_FILES=$(patsubst %,$(MO_DIR)/%.mo,$(LANGUAGES_BUILD) $(LANGUAGES_EXTRA))
MO_EXTRA=$(patsubst %,webtrees/language/%.mo,$(LANGUAGES_EXTRA))
SHELL=bash
SVN_VERSION=$(shell svnversion .. | tr -d 'M')
WT_VERSION=$(shell grep "'WT_VERSION'" ../includes/session.php | cut -d "'" -f 4)
WT_VERSION_RELEASE=$(shell grep "'WT_VERSION_RELEASE'" ../includes/session.php | cut -d "'" -f 4)
BUILD_VERSION=$(if $(WT_VERSION_RELEASE),$(SVN_VERSION),$(WT_VERSION)$(WT_VERSION_RELEASE))

# Location of google's "closure" tools for compressing CSS and JS
CLOSURE_JS=~/bin/compiler.jar
CLOSURE_CSS=~/bin/closure-stylesheets.jar

# Use maximum compression
GZIP=gzip -9

#.PHONY: clean dist-clean default webtrees webtrees-static-$(WT_VERSION)

################################################################################
# Create a release from this SVN working copy
################################################################################
default: clean webtrees
	# Remove the group-write permission - some badly configured hosts don't like it.
	chmod -R go-w webtrees
	# Hosts running PHP as an Apache module may need this
	chmod go+w webtrees/media webtrees/media/thumbs webtrees/data
	# Package up the extra language files
	zip -qr language-extra.zip $(MO_EXTRA)
	rm -f $(MO_EXTRA)
	zip -qr language.zip webtrees/language
	zip -qr webtrees-$(BUILD_VERSION).zip webtrees
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig webtrees-$(BUILD_VERSION).zip; fi
	# If we have a public html area, publish the files
	if test -d ~/public_html/build; then cp language*.zip webtrees*.zip ~/public_html/build/ && echo ${BUILD_VERSION} > ~/public_html/build/latest-svn.txt; fi
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
	rm -Rf webtrees* mo language.zip language-extra.zip

################################################################################
# Remove temporary and intermediate files
################################################################################
dist-clean: clean
	rm -Rf po

################################################################################
# Create a release from this SVN working copy
################################################################################
webtrees: $(PO_DIR) $(MO_DIR)
	svn export --quiet .. $@
	bzr revert $(PO_DIR)
	bzr update $(PO_DIR)
	make $(MO_FILES)
	# Embed the SVN version number in the code (for SVN builds only)
	sed -i "s/define('WT_VERSION_RELEASE', 'svn')/define('WT_VERSION_RELEASE', 'svn$(SVN_VERSION)')/" $@/includes/session.php
	# Exclude some unused Zend Framework components to reduce the size of the distribution
	rm -R $@/library/Zend/{Acl,Amf,Application,Auth,Barcode,Captcha,CodeGenerator,Console,Config,Crypt,Currency,Date,Db,Debug,Dojo,Dom,Feed,File,Form,Gdata,Http,InfoCard,Json,Layout,Ldap,Log,Mail,Markup,Measure,Memory,Mime,Navigation,Oauth,OpenId,Paginator,Pdf,ProgressBar,Queue,Reflection,Rest,Search,Serializer,Service,Soap,Test,Text,TimeSync,Tool,Uri,Validate,Version,View,Wildfire,XmlRpc}*
	# Exclude the build scripts from the distribution
	rm -Rf $@/build
	# Check for syntax errors
	if find $@ -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"; then false; else true; fi
	# Add standard language files
	cp $(MO_FILES) $@/language/

################################################################################
# PO files are stored in launchpad.net
# NOTE: due to a bug in launchpad, translations can only be exported to branches
# owned by individuals (fisharebest), rather than teams (webtrees-team)
################################################################################
$(PO_DIR):
	mkdir -p $@
	bzr checkout lp:~fisharebest/webtrees/translations $@

$(MO_DIR):
	mkdir -p $@

################################################################################
# Compile PO files to make MO files.  Most MO files correspond exactly to their
# PO file, but there are some special cases.
################################################################################
$(MO_DIR)/sr@Latn.mo: $(PO_DIR)/sr@latin.po
	msgfmt --output=$@ $<

$(MO_DIR)/zh.mo: $(PO_DIR)/zh_CN.po
	msgfmt --output=$@ $<

$(MO_DIR)/en_US.mo: en_US.po
	msgfmt --output=$@ $<

$(MO_DIR)/%.mo: $(PO_DIR)/%.po
	msgfmt --output=$@ $<

################################################################################
# Extract and minify the static files for use on a content-delivery-network.
################################################################################
webtrees-static-$(WT_VERSION): clean
	mkdir $@
	cd .. && find . "(" -name "*.css" -o -name "*.gif" -o -name "*.html" -o -name "*.ico" -o -name "*.jpg" -o -name "*.js" -o -name "*.mp3" -o -name "*.png" -o -name "*.swf" ")" -print0 | xargs -0 tar cf - | (cd build/$@ && tar xfp -)
	# Minify CSS
	find $@ -name "*.css" -exec java -jar $(CLOSURE_CSS) --output-file "{}.tmp" "{}" \; -exec mv "{}.tmp" "{}" \;
	# Minify JS
	find $@ -name "*.js" -exec java -jar $(CLOSURE_JS) --js "{}" --js_output_file "{}.tmp" \; -exec mv "{}.tmp" "{}" \;

################################################################################
# Upload the static files to an S3 bucket.
# Pre-compress CSS and JS files using GZIP, and set the Content-Encoding header.
# Sprecify the bucket using "S3_BUCKET=<<my_bucket>> make static-S3".
################################################################################
static-S3: webtrees-static-$(WT_VERSION)
	# Compress CSS and JS files - but keep the names the same
	find $< "(" -name "*.js" -o -name "*.css" ")" -exec $(GZIP) {} \; -exec mv {}.gz {} \;
	# Upload uncompressed files
	s3cmd sync --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --guess-mime-type "--rexclude=(css|js)$$" $< s3://$(S3_BUCKET)/
	# Upload compressed files
	s3cmd sync --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --guess-mime-type "--rinclude=(css|js)$$" --add-header=Content-Encoding:gzip $< s3://$(S3_BUCKET)/
