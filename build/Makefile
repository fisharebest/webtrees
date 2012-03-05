LANGUAGES_BUILD=ar bg bs ca cs da de en_GB en_US es et fa fi fr he hr hu is it lt nb nl pl pt ru sk sl sv tr tt vi
LANGUAGES_EXTRA=af dv el en_AU fo fr_CA gl id ja nn oc pt_BR ro sr sr@Latn ta zh
PO_DIR=po
MO_DIR=mo
MO_FILES=$(patsubst %,$(MO_DIR)/%.mo,$(LANGUAGES_BUILD) $(LANGUAGES_EXTRA))
MO_EXTRA=$(patsubst %,webtrees/language/%.mo,$(LANGUAGES_EXTRA))
SHELL=bash
SVN_VERSION=$(shell svnversion .. | tr -d 'M')
WT_VERSION=$(shell grep "'WT_VERSION'" ../includes/session.php | cut -d "'" -f 4)
WT_VERSION_RELEASE=$(shell grep "'WT_VERSION_RELEASE'" ../includes/session.php | cut -d "'" -f 4)
BUILD_VERSION=$(if $(WT_VERSION_RELEASE),$(SVN_VERSION),$(WT_VERSION)$(WT_VERSION_RELEASE))

################################################################################
# Create a release from this SVN working copy
################################################################################
default: clean webtrees
	# Package up the extra language files
	zip -qr language-extra.zip $(MO_EXTRA)
	rm -f $(MO_EXTRA)
	zip -qr language.zip webtrees/language
	zip -qr webtrees-$(BUILD_VERSION).zip webtrees
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig webtrees-$(BUILD_VERSION).zip; fi
	# If we have a public html area, publish the files
	if test -d ~/public_html/build; then cp language*.zip webtrees*.zip ~/public_html/build/ && echo ${BUILD_VERSION} > ~/public_html/build/latest-svn.txt; fi
	# If we have a public html area, clean up old files
	if test -d ~/public_html/build; then find ~/public_html/build -ctime +7 -exec rm -f {} \; ; fi
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
	svn export .. $@
	bzr revert $(PO_DIR)
	bzr update $(PO_DIR)
	make $(MO_FILES)
	# Embed the SVN version number in the code (for SVN builds only)
	sed -i "s/define('WT_VERSION_RELEASE', 'svn')/define('WT_VERSION_RELEASE', 'svn$(SVN_VERSION)')/" $@/includes/session.php
	# Exclude unused Zend Framework components
	rm -R $@/library/Zend/{Acl,Amf,Application,Auth,Barcode,Captcha,CodeGenerator,Console,Config,Crypt,Currency,Date,Db,Debug,Dojo,Dom,Feed,File,Form,Gdata,Http,InfoCard,Json,Layout,Ldap,Log,Mail,Markup,Measure,Memory,Mime,Navigation,Oauth,OpenId,Paginator,Pdf,ProgressBar,Queue,Reflection,Rest,Search,Serializer,Service,Soap,Test,Text,TimeSync,Tool,Uri,Validate,Version,View,Wildfire,XmlRpc}*
	# Exclude unused locale data
	rm -f $@/library/Zend/Locale/Data/{aa,ak,am,as,az,be,bn,bo,byn,cch,cop,cy,dz,ee,eo,eu,fil,fur,ga,gaa,gez,gsw,gu,gv,ha,haw,hi,hy,ia,ig,ii,in,iu,iw,k,ln,lo,lv,mk,ml,mn,mo,mr,mt,my,nd,ne,no,nso,nr,ny,om,or,pa,ps,rw,sa,se,sh,si,so,sq,ss,st,sw,syr,te,tg,th,ti,tl,tn,to,trv,ts,u,ve,w,x,y,zu}*.xml
	rm -Rf $@/build
	# Check for syntax errors
	if find $@ -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"; then false; else true; fi
	# Add standard language files
	cp $(MO_FILES) $@/language/

################################################################################
# Zip a directory to create an archive
################################################################################
%.zip: %
	zip -qrTm $@ $<

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
# Extract the static files for use on a content-delivery-network.
# Minify the CSS and JS files, if a suitable tool is available.
################################################################################
webtrees-static-$(WT_VERSION):
	rm -Rf $@
	mkdir $@
	cd .. && find . "(" -name "*.css" -o -name "*.gif" -o -name "*.html" -o -name "*.ico" -o -name "*.jpg" -o -name "*.js" -o -name "*.mp3" -o -name "*.png" -o -name "*.swf" ")" -print0 | xargs -0 tar cf - | (cd build/$@ && tar xfp -)
	# Minify CSS using the YUI compressor
	if [ -f ~/bin/yuicompressor-2.4.6.jar ]; then find $@ -name "*.css" | while read FILE; do java -jar ~/bin/yuicompressor-2.4.6.jar --charset UTF-8 --verbose -o tmp.css "$$FILE" && mv tmp.css "$$FILE"; done; fi
	# Minify JS using Google's closure compiler
	if [ -f ~/bin/compiler.jar ]; then find $@ -name "*.js" | while read FILE; do java -jar ~/bin/compiler.jar --js "$$FILE" --js_output_file tmp.js && mv tmp.js "$$FILE"; done; fi

################################################################################
# Create a second copy of the static files, with compressed CSS and JS,
# for servers that require two versions, such as Amazon S3
################################################################################
webtrees-static-$(WT_VERSION)z: webtrees-static-$(WT_VERSION)
	cp -pR $< $@
	find $@ "(" -name "*.js" -o -name "*.css" ")" -exec gzip {} \; -exec mv {}.gz {} \;

################################################################################
# Create .ZIP archives of the static files, for use with a CDN.
################################################################################
static: webtrees-static-$(WT_VERSION)z.zip webtrees-static-$(WT_VERSION).zip

################################################################################
# Upload the static files to an S3 bucket.
# Explicitly set the mime type for files that we have compressed.
# Set the bucket using, for example, "export S3_BUCKET=my_bucket".
################################################################################
static-S3: webtrees-static-$(WT_VERSION) webtrees-static-$(WT_VERSION)z
	s3cmd sync --acl-private --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --guess-mime-type webtrees-static-$(WT_VERSION) s3://$(S3_BUCKET)/
	s3cmd sync --acl-private --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --guess-mime-type "--rexclude=(css|js)$$" webtrees-static-$(WT_VERSION)z s3://$(S3_BUCKET)/
	s3cmd sync --acl-private --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --mime-type=text/css --add-header=Content-Encoding:gzip "--rinclude=css$$" webtrees-static-$(WT_VERSION)z s3://$(S3_BUCKET)/
	s3cmd sync --acl-private --no-delete-removed --add-header=Cache-Control:public,max-age=864000 --mime-type=application/javascript --add-header=Content-Encoding:gzip "--rinclude=js$$" webtrees-static-$(WT_VERSION)z s3://$(S3_BUCKET)/
