LANGUAGES_BUILD=ca cs da de en_GB en_US es et fa fi fr he hr hu it lt nb nl pl pt ru sk sl sv tr vi
LANGUAGES_EXTRA=ar dv el en_AU fo id nn oc ro sr sr@Latn ta zh
PO_DIR=po
MO_DIR=mo
MO_FILES=$(patsubst %,$(MO_DIR)/%.mo,$(LANGUAGES_BUILD) $(LANGUAGES_EXTRA))
MO_EXTRA=$(patsubst %,webtrees/language/%.mo,$(LANGUAGES_EXTRA))
SHELL=bash
SVNVERSION=$(shell svnversion .. | tr -d 'M')

################################################################################
# Create a release from this SVN working copy
################################################################################
default: clean webtrees
	# Package up the extra language files
	zip -qr language-extra.zip $(MO_EXTRA)
	rm -f $(MO_EXTRA)
	zip -qr language.zip webtrees/language
	zip -qr webtrees-$(SVNVERSION).zip webtrees
	# If we have a GNU private key, sign the file with it
	if test -d ~/.gnupg; then gpg --armor --sign --detach-sig webtrees-$(SVNVERSION).zip; fi
	# If we have a public html area, publish the files
	if test -d ~/public_html; then mkdir -p ~/public_html/build && cp language*.zip webtrees*.zip ~/public_html/build/; fi
	# If we have a public html area, clean up old files
	if test -d ~/public_html/build; then find ~/public_html/build -ctime +7 -exec rm -f {} \; ; fi
	# Create an updated message catalog
	mkdir -p webtrees_tmp
	rm -Rf webtrees_tmp/*
	find webtrees/modules -name "*.xml" | while read file; do sed -e 's~\(WT_I18N::[^)]*[)]\)~<?php echo \1; ?>~g' $$file > webtrees_tmp/$$(echo $$file.php | cut -c 10- | tr / _); done
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
	bzr update $(PO_DIR)
	make $(MO_FILES)
	# Embed the SVN version number in the code (for SVN builds only)
	sed -i "s/define('WT_VERSION_RELEASE', 'svn')/define('WT_VERSION_RELEASE', 'svn$(SVNVERSION)')/" $@/includes/session.php
	# Exclude unused Zend Framework components
	rm -R $@/library/Zend/{Acl,Amf,Application,Auth,Barcode,Captcha,CodeGenerator,Console,Controller,Config,Crypt,Currency,Date,Db,Debug,Dojo,Dom,Feed,File,Filter,Form,Gdata,Http,InfoCard,Json,Layout,Ldap,Log,Mail,Markup,Measure,Memory,Mime,Navigation,Oauth,OpenId,Paginator,Pdf,ProgressBar,Queue,Reflection,Rest,Search,Serializer,Service,Soap,Test,Text,TimeSync,Tool,Uri,Validate,Version,View,Wildfire,XmlRpc}*
	# Exclude unused locale data
	rm -f $@/library/Zend/Locale/Data/{aa,af,ak,am,ar_,as,az,b,ca_,cch,cop,cs_CZ,cy,da_,de_,dv_,dz,ee,el_,en_{AS,B,C,D,GU,H,I,J,M,N,P,S,T,UM,US_,VI,ZA,ZW},eo,es_,eu,et_,fa_,fil,fo_,fr_,fur,g,ha,haw,he_,hi,hr_,hu_,hy,ia,id_,ig,ii,in,is,it_,iu,iw,j,k,ln,lo,lt_,lv,m{k,l,n,o,r,t,y},nb_,nd,ne,nl_,no,nn_,nso,nr,ny,oc_,om,or,pa,pl_,ps,pt_,ro_,ru_,rw,sa,se,sh,si,sk_,sl_,so,sq,sr_B,sr_C,sr_Latn_,sr_ME,sr_RS,sr_YU,ss,st,sv_,sw,syr,ta_,te,tg,th,ti,tl,tn,to,tr_,trv,ts,tt,u,ve,vi_,w,x,y,zh_H,zh_{MO,SG,TW},zu}*.xml
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
	mkdir -p $(MO_DIR)

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
