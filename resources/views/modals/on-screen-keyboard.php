<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<div class="card wt-osk">
	<div class="card-header">
		<div class="card-title">
			<button type="button" class="btn btn-primary wt-osk-close">&times;</button>

			<button type="button" class="btn btn-secondary wt-osk-pin-button" data-toggle="button" aria-pressed="false"><?= FontAwesome::semanticIcon('pin', I18N::translate('Keep open')) ?></button>

			<button type="button" class="btn btn-secondary wt-osk-shift-button" data-toggle="button" aria-pressed="false">a &harr; A</button>

			<div class="btn-group" role="group" data-toggle="buttons">
				<button class="btn btn-secondary active" dir="ltr">
					<input type="radio" class="wt-osk-script-button" checked autocomplete="off" data-script="latn" name="osk-script"> Abcd
				</button>
				<button class="btn btn-secondary" dir="ltr">
					<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="cyrl" name="osk-script"> &Acy;&bcy;&gcy;&dcy;
				</button>
				<button class="btn btn-secondary" dir="ltr">
					<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="grek" name="osk-script"> &Alpha;&beta;&gamma;&delta;
				</button>
				<button class="btn btn-secondary" dir="rtl">
					<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="arab" name="osk-script"> &#x627;&#x628;&#x629;&#x62a;
				</button>
				<button class="btn btn-secondary" dir="rtl">
					<input type="radio" class="wt-osk-script-button" autocomplete="off" data-script="hebr" name="osk-script"> &#x5d0;&#x5d1;&#x5d2;&#x5d3;
				</button>
			</div>
		</div>
	</div>
	<div class="card-body wt-osk-keys">
		<!-- Quotation marks -->
		<div class="wt-osk-group">
			<span class="wt-osk-key">&lsquo;<sup class="wt-osk-key-shift">&ldquo;</sup></span>
			<span class="wt-osk-key">&rsquo;<sup class="wt-osk-key-shift">&rdquo;</sup></span>
			<span class="wt-osk-key">&lsaquo;<sup class="wt-osk-key-shift">&ldquo;</sup></span>
			<span class="wt-osk-key">&rsaquo;<sup class="wt-osk-key-shift">&raquo;</sup></span>
			<span class="wt-osk-key">&sbquo;<sup class="wt-osk-key-shift">&bdquo;</sup></span>
			<span class="wt-osk-key">&prime;<sup class="wt-osk-key-shift">&Prime;</sup></span>
		</div>
		<!-- Symbols and punctuation -->
		<div class="wt-osk-group">
			<span class="wt-osk-key">&copy;</span>
			<span class="wt-osk-key">&deg;</span>
			<span class="wt-osk-key">&hellip;</span>
			<span class="wt-osk-key">&middot;<sup class="wt-osk-key-shift">&bullet;</sup></span>
			<span class="wt-osk-key">&ndash;<sup class="wt-osk-key-shift">&mdash;</sup></span>
			<span class="wt-osk-key">&dagger;<sup class="wt-osk-key-shift">&ddagger;</sup></span>
			<span class="wt-osk-key">&sect;<sup class="wt-osk-key-shift">&para;</sup></span>
			<span class="wt-osk-key">&iquest;<sup class="wt-osk-key-shift">&iexcl;</sup></span>
		</div>
		<!-- Letter A with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&agrave;<sup class="wt-osk-key-shift">&Agrave;</sup></span>
			<span class="wt-osk-key">&aacute;<sup class="wt-osk-key-shift">&Aacute;</sup></span>
			<span class="wt-osk-key">&acirc;<sup class="wt-osk-key-shift">&Acirc;</sup></span>
			<span class="wt-osk-key">&atilde;<sup class="wt-osk-key-shift">&Atilde;</sup></span>
			<span class="wt-osk-key">&aring;<sup class="wt-osk-key-shift">&Aring;</sup></span>
			<span class="wt-osk-key">&aogon;<sup class="wt-osk-key-shift">&Aogon;</sup></span>
			<span class="wt-osk-key">&aelig;<sup class="wt-osk-key-shift">&AElig;</sup></span>
			<span class="wt-osk-key">&ordf;</span>
		</div>
		<!-- Letter C with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&ccedil;<sup class="wt-osk-key-shift">&Ccedil;</sup></span>
			<span class="wt-osk-key">&ccaron;<sup class="wt-osk-key-shift">&Ccaron;</sup></span>
		</div>
		<!-- Letter D with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&Dcaron;<sup class="wt-osk-key-shift">&Dcaron;</sup></span>
		</div>
		<!-- Letter E with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&egrave;<sup class="wt-osk-key-shift">&Egrave;</sup></span>
			<span class="wt-osk-key">&eacute;<sup class="wt-osk-key-shift">&Eacute;</sup></span>
			<span class="wt-osk-key">&ecirc;<sup class="wt-osk-key-shift">&Ecirc;</sup></span>
			<span class="wt-osk-key">&euml;<sup class="wt-osk-key-shift">&Euml;</sup></span>
			<span class="wt-osk-key">&eogon;<sup class="wt-osk-key-shift">&Eogon;</sup></span>
		</div>
		<!-- Letter G with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&gbreve;<sup class="wt-osk-key-shift">&Gbreve;</sup></span>
		</div>
		<!-- Letter I with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&igrave;<sup class="wt-osk-key-shift">&Igrave;</sup></span>
			<span class="wt-osk-key">&iacute;<sup class="wt-osk-key-shift">&Iacute;</sup></span>
			<span class="wt-osk-key">&icirc;<sup class="wt-osk-key-shift">&Icirc;</sup></span>
			<span class="wt-osk-key">&iuml;<sup class="wt-osk-key-shift">&Iuml;</sup></span>
			<span class="wt-osk-key">&iogon;<sup class="wt-osk-key-shift">&Iogon;</sup></span>
			<span class="wt-osk-key">&inodot;<sup class="wt-osk-key-shift">&Idot;</sup></span>
			<span class="wt-osk-key">&ijlig;<sup class="wt-osk-key-shift">&IJlig;</sup></span>
		</div>
		<!-- Letter L with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&lcaron;<sup class="wt-osk-key-shift">&Lcaron;</sup></span>
			<span class="wt-osk-key">&lacute;<sup class="wt-osk-key-shift">&Lacute;</sup></span>
			<span class="wt-osk-key">&lstrok;<sup class="wt-osk-key-shift">&Lstrok;</sup></span>
		</div>
		<!-- Letter N with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&napos;</span>
			<span class="wt-osk-key">&ntilde;<sup class="wt-osk-key-shift">&Ntilde;</sup></span>
			<span class="wt-osk-key">&ncaron;<sup class="wt-osk-key-shift">&Ncaron;</sup></span>
		</div>
		<!-- Letter O with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&ograve;<sup class="wt-osk-key-shift">&Ograve;</sup></span>
			<span class="wt-osk-key">&oacute;<sup class="wt-osk-key-shift">&Oacute;</sup></span>
			<span class="wt-osk-key">&ocirc;<sup class="wt-osk-key-shift">&Ocirc;</sup></span>
			<span class="wt-osk-key">&otilde;<sup class="wt-osk-key-shift">&Otilde;</sup></span>
			<span class="wt-osk-key">&ouml;<sup class="wt-osk-key-shift">&Ouml;</sup></span>
			<span class="wt-osk-key">&oslash;<sup class="wt-osk-key-shift">&Oslash;</sup></span>
			<span class="wt-osk-key">&oelig;<sup class="wt-osk-key-shift">&OElig;</sup></span>
			<span class="wt-osk-key">&ordm;</span>
		</div>
		<!-- Letter T with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&tcaron;<sup class="wt-osk-key-shift">&Tcaron;</sup></span>
		</div>
		<!-- Letter R with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&racute;<sup class="wt-osk-key-shift">&Racute;</sup></span>
			<span class="wt-osk-key">&rcaron;<sup class="wt-osk-key-shift">&Rcaron;</sup></span>
		</div>
		<!-- Letter S with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&scaron;<sup class="wt-osk-key-shift">&Scaron;</sup></span>
			<span class="wt-osk-key">&scedil;<sup class="wt-osk-key-shift">&Scedil;</sup></span>
			<span class="wt-osk-key">&#x17F;</sup></span>
		</div>
		<!-- Letter U with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&ugrave;<sup class="wt-osk-key-shift">&Ugrave;</sup></span>
			<span class="wt-osk-key">&uacute;<sup class="wt-osk-key-shift">&Uacute;</sup></span>
			<span class="wt-osk-key">&ucirc;<sup class="wt-osk-key-shift">&Ucirc;</sup></span>
			<span class="wt-osk-key">&utilde;<sup class="wt-osk-key-shift">&Utilde;</sup></span>
			<span class="wt-osk-key">&umacr;<sup class="wt-osk-key-shift">&Umacr;</sup></span>
			<span class="wt-osk-key">&uogon;<sup class="wt-osk-key-shift">&Uogon;</sup></span>
		</div>
		<!-- Letter Y with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&yacute;<sup class="wt-osk-key-shift">&Yacute;</sup></span>
		</div>
		<!-- Letter Z with diacritic -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&zdot;<sup class="wt-osk-key-shift">&Zdot;</sup></span>
			<span class="wt-osk-key">&zcaron;<sup class="wt-osk-key-shift">&Zcaron;</sup></span>
		</div>
		<!-- Esszet, Eth and Thorn -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-latn" dir="ltr">
			<span class="wt-osk-key">&szlig;<sup class="wt-osk-key-shift">&#7838;</sup></span>
			<span class="wt-osk-key">&eth;<sup class="wt-osk-key-shift">&ETH;</sup></span>
			<span class="wt-osk-key">&thorn;<sup class="wt-osk-key-shift">&THORN;</sup></span>
		</div>
		<!-- Extra Cyrillic characters -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-cyrl" dir="ltr" hidden>
			<span class="wt-osk-key">&iocy;<sup class="wt-osk-key-shift">&IOcy;</sup></span>
			<span class="wt-osk-key">&djcy;<sup class="wt-osk-key-shift">&DJcy;</sup></span>
			<span class="wt-osk-key">&gjcy;<sup class="wt-osk-key-shift">&GJcy;</sup></span>
			<span class="wt-osk-key">&jukcy;<sup class="wt-osk-key-shift">&Jukcy;</sup></span>
			<span class="wt-osk-key">&dscy;<sup class="wt-osk-key-shift">&DScy;</sup></span>
			<span class="wt-osk-key">&iukcy;<sup class="wt-osk-key-shift">&Iukcy;</sup></span>
			<span class="wt-osk-key">&yicy;<sup class="wt-osk-key-shift">&YIcy;</sup></span>
			<span class="wt-osk-key">&jsercy;<sup class="wt-osk-key-shift">&Jsercy;</sup></span>
			<span class="wt-osk-key">&ljcy;<sup class="wt-osk-key-shift">&LJcy;</sup></span>
			<span class="wt-osk-key">&njcy;<sup class="wt-osk-key-shift">&NJcy;</sup></span>
			<span class="wt-osk-key">&tshcy;<sup class="wt-osk-key-shift">&TSHcy;</sup></span>
			<span class="wt-osk-key">&kjcy;<sup class="wt-osk-key-shift">&KJcy;</sup></span>
			<span class="wt-osk-key">&ubrcy;<sup class="wt-osk-key-shift">&Ubrcy;</sup></span>
			<span class="wt-osk-key">&dzcy;<sup class="wt-osk-key-shift">&DZcy;</sup></span>
		</div>
		<!-- Cyrillic alphabet -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-cyrl" dir="ltr" hidden>
			<span class="wt-osk-key">&acy;<sup class="wt-osk-key-shift">&Acy;</sup></span>
			<span class="wt-osk-key">&bcy;<sup class="wt-osk-key-shift">&Bcy;</sup></span>
			<span class="wt-osk-key">&gcy;<sup class="wt-osk-key-shift">&Gcy;</sup></span>
			<span class="wt-osk-key">&dcy;<sup class="wt-osk-key-shift">&Dcy;</sup></span>
			<span class="wt-osk-key">&iecy;<sup class="wt-osk-key-shift">&IEcy;</sup></span>
			<span class="wt-osk-key">&zhcy;<sup class="wt-osk-key-shift">&ZHcy;</sup></span>
			<span class="wt-osk-key">&zcy;<sup class="wt-osk-key-shift">&Zcy;</sup></span>
			<span class="wt-osk-key">&icy;<sup class="wt-osk-key-shift">&Icy;</sup></span>
			<span class="wt-osk-key">&jcy;<sup class="wt-osk-key-shift">&Jcy;</sup></span>
			<span class="wt-osk-key">&kcy;<sup class="wt-osk-key-shift">&Kcy;</sup></span>
			<span class="wt-osk-key">&lcy;<sup class="wt-osk-key-shift">&Lcy;</sup></span>
			<span class="wt-osk-key">&mcy;<sup class="wt-osk-key-shift">&Mcy;</sup></span>
			<span class="wt-osk-key">&ncy;<sup class="wt-osk-key-shift">&Ncy;</sup></span>
			<span class="wt-osk-key">&ocy;<sup class="wt-osk-key-shift">&Ocy;</sup></span>
			<span class="wt-osk-key">&pcy;<sup class="wt-osk-key-shift">&Pcy;</sup></span>
			<span class="wt-osk-key">&scy;<sup class="wt-osk-key-shift">&Scy;</sup></span>
			<span class="wt-osk-key">&tcy;<sup class="wt-osk-key-shift">&Tcy;</sup></span>
			<span class="wt-osk-key">&ucy;<sup class="wt-osk-key-shift">&Ucy;</sup></span>
			<span class="wt-osk-key">&ucy;<sup class="wt-osk-key-shift">&Ucy;</sup></span>
			<span class="wt-osk-key">&fcy;<sup class="wt-osk-key-shift">&Fcy;</sup></span>
			<span class="wt-osk-key">&khcy;<sup class="wt-osk-key-shift">&KHcy;</sup></span>
			<span class="wt-osk-key">&tscy;<sup class="wt-osk-key-shift">&TScy;</sup></span>
			<span class="wt-osk-key">&chcy;<sup class="wt-osk-key-shift">&CHcy;</sup></span>
			<span class="wt-osk-key">&shcy;<sup class="wt-osk-key-shift">&SHcy;</sup></span>
			<span class="wt-osk-key">&shchcy;<sup class="wt-osk-key-shift">&SHCHcy;</sup></span>
			<span class="wt-osk-key">&hardcy;<sup class="wt-osk-key-shift">&HARDcy;</sup></span>
			<span class="wt-osk-key">&ycy;<sup class="wt-osk-key-shift">&Ycy;</sup></span>
			<span class="wt-osk-key">&softcy;<sup class="wt-osk-key-shift">&SOFTcy;</sup></span>
			<span class="wt-osk-key">&ecy;<sup class="wt-osk-key-shift">&Ecy;</sup></span>
			<span class="wt-osk-key">&yucy;<sup class="wt-osk-key-shift">&YUcy;</sup></span>
			<span class="wt-osk-key">&yacy;<sup class="wt-osk-key-shift">&YAcy;</sup></span>
		</div>
		<!-- Greek alphabet -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-grek" dir="ltr" hidden>
			<span class="wt-osk-key">&alpha;<sup class="wt-osk-key-shift">&Alpha;</sup></span>
			<span class="wt-osk-key">&beta;<sup class="wt-osk-key-shift">&Beta;</sup></span>
			<span class="wt-osk-key">&gamma;<sup class="wt-osk-key-shift">&Gamma;</sup></span>
			<span class="wt-osk-key">&delta;<sup class="wt-osk-key-shift">&Delta;</sup></span>
			<span class="wt-osk-key">&epsilon;<sup class="wt-osk-key-shift">&Epsilon;</sup></span>
			<span class="wt-osk-key">&zeta;<sup class="wt-osk-key-shift">&Zeta;</sup></span>
			<span class="wt-osk-key">&eta;<sup class="wt-osk-key-shift">&eta;</sup></span>
			<span class="wt-osk-key">&theta;<sup class="wt-osk-key-shift">&Theta;</sup></span>
			<span class="wt-osk-key">&iota;<sup class="wt-osk-key-shift">&Iota;</sup></span>
			<span class="wt-osk-key">&kappa;<sup class="wt-osk-key-shift">&Kappa;</sup></span>
			<span class="wt-osk-key">&lambda;<sup class="wt-osk-key-shift">&Lambda;</sup></span>
			<span class="wt-osk-key">&mu;<sup class="wt-osk-key-shift">&Mu;</sup></span>
			<span class="wt-osk-key">&nu;<sup class="wt-osk-key-shift">&Nu;</sup></span>
			<span class="wt-osk-key">&xi;<sup class="wt-osk-key-shift">&Xi;</sup></span>
			<span class="wt-osk-key">&omicron;<sup class="wt-osk-key-shift">&Omicron;</sup></span>
			<span class="wt-osk-key">&pi;<sup class="wt-osk-key-shift">&Pi;</sup></span>
			<span class="wt-osk-key">&rho;<sup class="wt-osk-key-shift">&Rho;</sup></span>
			<span class="wt-osk-key">&sigma;<sup class="wt-osk-key-shift">&Sigma;</sup></span>
			<span class="wt-osk-key">&tau;<sup class="wt-osk-key-shift">&Tau;</sup></span>
			<span class="wt-osk-key">&upsilon;<sup class="wt-osk-key-shift">&Upsilon;</sup></span>
			<span class="wt-osk-key">&phi;<sup class="wt-osk-key-shift">&Phi;</sup></span>
			<span class="wt-osk-key">&chi;<sup class="wt-osk-key-shift">&chi;</sup></span>
			<span class="wt-osk-key">&psi;<sup class="wt-osk-key-shift">&Psi;</sup></span>
			<span class="wt-osk-key">&omega;<sup class="wt-osk-key-shift">&Omega;</sup></span>
		</div>
		<!-- Arabic alphabet -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-arab" dir="rtl" hidden>
			<span class="wt-osk-key">ا</span>
			<span class="wt-osk-key">ب</span>
			<span class="wt-osk-key">ت</span>
			<span class="wt-osk-key">ثج</span>
			<span class="wt-osk-key">ح</span>
			<span class="wt-osk-key">خ</span>
			<span class="wt-osk-key">د</span>
			<span class="wt-osk-key">ذ</span>
			<span class="wt-osk-key">ر</span>
			<span class="wt-osk-key">ز</span>
			<span class="wt-osk-key">س</span>
			<span class="wt-osk-key">ش</span>
			<span class="wt-osk-key">ص</span>
			<span class="wt-osk-key">ض</span>
			<span class="wt-osk-key">ط</span>
			<span class="wt-osk-key">ظ</span>
			<span class="wt-osk-key">ع</span>
			<span class="wt-osk-key">غ</span>
			<span class="wt-osk-key">ف</span>
			<span class="wt-osk-key">ق</span>
			<span class="wt-osk-key">ك</span>
			<span class="wt-osk-key">ل</span>
			<span class="wt-osk-key">من</span>
			<span class="wt-osk-key">ه</span>
			<span class="wt-osk-key">و</span>
			<span class="wt-osk-key">ي</span>
			<span class="wt-osk-key">آ</span>
			<span class="wt-osk-key">ة</span>
			<span class="wt-osk-key">ى</span>
			<span class="wt-osk-key">ی</span>
		</div>
		<!-- Hebrew alphabet -->
		<div class="wt-osk-group wt-osk-script wt-osk-script-hebr" dir="rtl" hidden>
			<span class="wt-osk-key">&#x5d0;</span>
			<span class="wt-osk-key">&#x5d1;</span>
			<span class="wt-osk-key">&#x5d2;</span>
			<span class="wt-osk-key">&#x5d3;</span>
			<span class="wt-osk-key">&#x5d4;</span>
			<span class="wt-osk-key">&#x5d5;</span>
			<span class="wt-osk-key">&#x5d6;</span>
			<span class="wt-osk-key">&#x5d7;</span>
			<span class="wt-osk-key">&#x5d8;</span>
			<span class="wt-osk-key">&#x5d9;</span>
			<span class="wt-osk-key">&#x5da;</span>
			<span class="wt-osk-key">&#x5db;</span>
			<span class="wt-osk-key">&#x5dc;</span>
			<span class="wt-osk-key">&#x5dd;</span>
			<span class="wt-osk-key">&#x5de;</span>
			<span class="wt-osk-key">&#x5df;</span>
			<span class="wt-osk-key">&#x5e0;</span>
			<span class="wt-osk-key">&#x5e1;</span>
			<span class="wt-osk-key">&#x5e2;</span>
			<span class="wt-osk-key">&#x5e3;</span>
			<span class="wt-osk-key">&#x5e4;</span>
			<span class="wt-osk-key">&#x5e5;</span>
			<span class="wt-osk-key">&#x5e6;</span>
			<span class="wt-osk-key">&#x5e7;</span>
			<span class="wt-osk-key">&#x5e8;</span>
			<span class="wt-osk-key">&#x5e9;</span>
			<span class="wt-osk-key">&#x5ea;</span>
			<span class="wt-osk-key">&#x5f0;</span>
			<span class="wt-osk-key">&#x5f1;</span>
			<span class="wt-osk-key">&#x5f2;</span>
			<span class="wt-osk-key">&#x5f3;</span>
			<span class="wt-osk-key">&#x5f4;</span>
		</div>
	</div>
</div>
