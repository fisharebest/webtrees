L10NVERSION=$(shell date +%Y-%m-%d -r $(LANGUAGE_ARCHIVE))
LABEL=$(PACKAGE)-$(SVNVERSION)
LANGUAGE_ARCHIVE=launchpad-export.tar.gz
LANGUAGES=en_US $(shell tar tf $(LANGUAGE_ARCHIVE) | grep /LC_MESSAGES/webtrees.mo | grep -v ^en/ | cut -d / -f 1)
LANGUAGES_RELEASE=ca cs da de en_GB en_US es et fi fr he hr hu it lt nb nl pl pt ru sk sl sv tr vi
MO=$(patsubst %,$(LABEL)/language/%.mo,$(LANGUAGES))
PACKAGE=webtrees
POT=$(PACKAGE).pot
PHP=$(shell find .. -path ../library -prune -o -path ../build -prune -o -name '*.php' -print)
SHELL=bash
SVNVERSION=$(shell svnversion .. | tr -d 'M')

################################################################################
# Default action is to build everything
################################################################################
default: clean $(LABEL).zip webtrees.pot
	@echo
	@echo "Build successful. Now upload the files to launchpad.net:"
	@echo
	@echo "$(LABEL).zip => https://launchpad.net/webtrees/trunk/snapshot"
	@echo "language.zip => https://webtrees.net/, addons"
	@echo "language-all.zip => https://webtrees.net/, addons"
	@echo "webtrees.pot => https://translations.launchpad.net/webtrees/trunk/+translations-upload"
	@echo

clean:
	rm -Rf webtrees* language* tmp_reports

################################################################################
# Create a ZIP archives
################################################################################
$(LABEL).zip: $(LABEL) $(MO)
	zip -q -j language-all.zip $(LABEL)/language/*.mo
	for file in `find $(LABEL)/language -name "*.mo"`; do echo $(LANGUAGES_RELEASE) | grep -q `basename $$file .mo` || rm $$file; done
	zip -q -j language.zip $(LABEL)/language/*.mo
	zip -qr $@ $(LABEL)
	rm -R $(LABEL)

################################################################################
# Extract code from the current SVN working copy and strip unused libraries
################################################################################
$(LABEL): $(PHP)
	svn export .. $(LABEL)
	sed -i "s/define('WT_VERSION_RELEASE', 'svn')/define('WT_VERSION_RELEASE', 'svn$(SVNVERSION)')/" $(LABEL)/includes/session.php
	rm -R $(LABEL)/library/Zend/{Acl,Amf,Application,Auth,Barcode,Captcha,CodeGenerator,Console,Controller,Config,Crypt,Currency,Date,Db,Debug,Dojo,Dom,Feed,File,Filter,Form,Gdata,Http,InfoCard,Json,Layout,Ldap,Log,Mail,Markup,Measure,Memory,Mime,Navigation,Oauth,OpenId,Paginator,Pdf,ProgressBar,Queue,Reflection,Rest,Search,Serializer,Service,Soap,Test,Text,TimeSync,Tool,Uri,Validate,Version,View,Wildfire,XmlRpc}*
	rm -f $(LABEL)/library/Zend/Locale/Data/{aa,af,ak,am,ar_,as,az,b,ca_,cch,cop,cs_CZ,cy,da_,de_,dv_,dz,ee,el_,en_{AS,B,C,D,GU,H,I,J,M,N,P,S,T,UM,US_,VI,ZA,ZW},eo,es_,eu,et_,fa_,fil,fo_,fr_,fur,g,ha,haw,he_,hi,hr_,hu_,hy,ia,id_,ig,ii,in,is,it_,iu,iw,j,k,ln,lo,lt_,lv,m{k,l,n,o,r,t,y},nb_,nd,ne,nl_,no,nn_,nso,nr,ny,oc_,om,or,pa,pl_,ps,pt_,ro_,ru_,rw,sa,se,sh,si,sk_,sl_,so,sq,sr_B,sr_C,sr_Latn_,sr_ME,sr_RS,sr_YU,ss,st,sv_,sw,syr,ta_,te,tg,th,ti,tl,tn,to,tr_,trv,ts,tt,u,ve,vi_,w,x,y,zh_H,zh_{MO,SG,TW},zu}*.xml
	rm -R $(LABEL)/build
	rm -Rf $(LABEL)/language/*
	for file in `find . -path ./library -prune -o -name '*.php' -print`; do php -l $$file; done

################################################################################
# Extract a language file from the downloaded archive file, and rename it
################################################################################
%.mo: $(LANGUAGE_ARCHIVE)
	# en_US is maintained locally - others are maintained in launchpad
	if [ $(@F) = en_US.mo ]; then msgfmt -o $(LABEL)/language/en_US.mo en_US.po; else tar xfzO $(LANGUAGE_ARCHIVE) $(*F)/LC_MESSAGES/webtrees.mo > $@; fi

################################################################################
# We cannot make this file - we download it from launchpad.net
################################################################################
$(LANGUAGE_ARCHIVE):
	@echo
	@echo "------------------------------------------------------------------"
	@echo "You must download the compiled language files (in .MO format) from"
	@echo "https://translations.launchpad.net/webtrees/trunk/+export"
	@echo "Save the file ($(@)) to the build directory."
	@echo "Then run make again."
	@echo "------------------------------------------------------------------"
	@echo
	@false

################################################################################
# Update the PO Template file from the sources.
################################################################################
$(POT): $(PHP)
	rm -Rf tmp_reports
	mkdir tmp_reports
	for file in ../modules/*/*.xml; do cat $$file | php -r "echo preg_replace('/(i18n::[^)]+\))/', '<'.'?php echo \\1; ?>', file_get_contents('php://stdin'));" > tmp_reports/$$(echo $$file.php | cut -c 12- | tr / _); done
	@xgettext --package-name=$(PACKAGE) --package-version=1.0 --msgid-bugs-address=i18n@webtrees.net --output=$@ --no-wrap --language=PHP --add-comments=I18N --from-code=utf-8 --keyword --keyword=translate:1 --keyword=translate_c:1c,2 --keyword=plural:1,2 --keyword=noop:1 $(PHP) tmp_reports/*.php
	rm -Rf tmp_reports
