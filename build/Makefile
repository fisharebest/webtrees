COMPRESSION=.zip # Choose one of: .zip .tar.gz .tar.bz2
L10NVERSION=$(shell date +%Y-%m-%d -r $(LANGUAGE_ARCHIVE))
LABEL=$(PACKAGE)-$(SVNVERSION)
LANGUAGE_ARCHIVE=launchpad-export.tar.gz
LANGUAGES=$(shell tar tf $(LANGUAGE_ARCHIVE) | grep /LC_MESSAGES/webtrees.mo | cut -d / -f 1)
MO=$(patsubst %,$(LABEL)/language/%.mo,$(LANGUAGES))
PACKAGE=webtrees
POT=$(PACKAGE).pot
PHP=$(shell find .. -path ../library -prune -o -path ../build -prune -o -name '*.php' -print)
SHELL=bash
SVNVERSION=$(shell svnversion .. | tr -d 'M')

################################################################################
# Default action is to build everything
################################################################################
default: clean $(LABEL)$(COMPRESSION) webtrees.pot
	@echo
	@echo "Build successful. Now upload the files to launchpad.net:"
	@echo
	@echo "language-$(L10NVERSION)$(COMPRESSION) => https://launchpad.net/webtrees/trunk/snapshot"
	@echo "$(LABEL)$(COMPRESSION) => https://launchpad.net/webtrees/trunk/snapshot"
	@echo "webtrees.pot => https://translations.launchpad.net/webtrees/trunk/+translations-upload"
	@echo

clean:
	rm -Rf webtrees* language*

################################################################################
# Create a ZIP archives
################################################################################
$(LABEL).zip: $(LABEL) $(MO)
	zip -qr language-$(L10NVERSION).zip $(LABEL)/language
	zip -qr $@ $(LABEL)
	rm -R $(LABEL)

################################################################################
# Create a TAR.GZ archives
################################################################################
$(LABEL).tar.gz: $(LABEL) $(MO)
	tar cfz language-$(L10NVERSION).tar $(LABEL)/language
	tar cfz $(LABEL).tar $(LABEL)
	rm -R $(LABEL)

################################################################################
# Create a TAR.BZ2 archives
################################################################################
$(LABEL).tar.bz2: $(LABEL) $(MO)
	tar cfg language-$(L10NVERSION).tar $(LABEL)/language
	tar cfg $(LABEL).tar $(LABEL)
	rm -R $(LABEL)

################################################################################
# Extract code from the current SVN working copy and strip unused libraries
################################################################################
$(LABEL): $(PHP)
	svn export .. $(LABEL)
	rm -R $(LABEL)/library/Zend/{Acl,Amf,Application,Auth,Barcode,Captcha,CodeGenerator,Console,Controller,Config,Crypt,Currency,Date,Db,Debug,Dojo,Dom,Feed,File,Filter,Form,Gdata,Http,InfoCard,Json,Layout,Ldap,Log,Mail,Markup,Measure,Memory,Mime,Navigation,Oauth,OpenId,Paginator,Pdf,ProgressBar,Queue,Reflection,Rest,Search,Serializer,Service,Session,Soap,Tag,Test,Text,TimeSync,Tool,Uri,Validate,Version,View,Wildfire,XmlRpc}*
	rm -R $(LABEL)/build
	rm -Rf $(LABEL)/language/*
	for file in `find . -path ./library -prune -o -name '*.php' -print`; do php -l $$file; done

################################################################################
# Extract a language file from the downloaded archive file, and rename it
################################################################################
%.mo: $(LANGUAGE_ARCHIVE)
	tar xfzO $(LANGUAGE_ARCHIVE) $(*F)/LC_MESSAGES/webtrees.mo > $@

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
