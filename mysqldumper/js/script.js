var NS=(document.layers);var IE=(document.all);function obj_enable(objid)
{var e=document.getElementById(objid);e.disabled=false;}
function obj_disable(objid)
{var e=document.getElementById(objid);e.disabled=true;}
function setactiveMenuFromContent()
{var a=parent.MySQL_Dumper_content.location.href;var menuid=1;if(a.indexOf("config_overview.php")!=-1)menuid=2;if(a.indexOf("filemanagement.php")!=-1)
{if(a.indexOf("action=dump")!=-1)menuid=3;if(a.indexOf("action=restore")!=-1)menuid=4;if(a.indexOf("action=files")!=-1)menuid=5;}
if(a.indexOf("sql.php")!=-1)menuid=6;if(a.indexOf("log.php")!=-1)menuid=7;if(a.indexOf("help.php")!=-1)menuid=8;setMenuActive('m'+menuid);}
function setMenuActive(id){for(var i=1;i<=10;i++){var objid='m'+i;if(id==objid)
{parent.frames[0].document.getElementById(objid).className='active';}
else
{if(parent.frames[0].document.getElementById(objid))parent.frames[0].document.getElementById(objid).className='';}}}
function GetSelectedFilename()
{var a="";var obj=document.getElementsByName("file[]");var anz=0;if(!obj.length)
{if(obj.checked){a+=obj.value;}}
else
{for(i=0;i<obj.length;i++)
{if(obj[i].checked){a+="\n"+obj[i].value;anz++;}}}
return a;}
function Check(i,k)
{var anz=0;var s="";var smp;var ids=document.getElementsByName("file[]");var mp=document.getElementsByName("multipart[]");for(var j=0;j<ids.length;j++){if(ids[j].checked)
{s=ids[j].value;smp=(mp[j].value==0)?"":" (Multipart: "+mp[j].value+" files)";anz++;if(k==0)break;}}
if(anz==0){WP("","gd");}else if(anz==1){WP(s+smp,"gd");}else{WP("> 1","gd");}}
function SelectMD(v,anz)
{for(i=0;i<anz;i++){n="db_multidump_"+i;obj=document.getElementsByName(n)[0];if(obj&&!obj.disabled){obj.checked=v;}}}
function Sel(v)
{var a=document.frm_tbl;if(!a.chk_tbl.length)
{a.chk_tbl.checked=v;}else{for(i=0;i<a.chk_tbl.length;i++){a.chk_tbl[i].checked=v;}}}
function ConfDBSel(v,adb)
{for(i=0;i<adb;i++){var a=document.getElementsByName("db_multidump["+i+"]");if(a)a.checked=v;}}
function chkFormular()
{var a=document.frm_tbl;a.tbl_array.value="";if(!a.chk_tbl.length)
{if(a.chk_tbl.checked==true)
a.tbl_array.value+=a.chk_tbl.value+"|";}else{for(i=0;i<a.chk_tbl.length;i++){if(a.chk_tbl[i].checked==true)
a.tbl_array.value+=a.chk_tbl[i].value+"|";}}
if(a.tbl_array.value==""){alert("Choose tables!");return false;}else{return true;}}
function insertHTA(s,tb)
{if(s==1)ins="AddHandler php-fastcgi .php .php4\nAddhandler cgi-script .cgi .pl\nOptions +ExecCGI";if(s==101)ins="DirectoryIndex /cgi-bin/script.pl"
if(s==102)ins="AddHandler cgi-script .extension";if(s==103)ins="Options +ExecCGI";if(s==104)ins="Options +Indexes";if(s==105)ins="ErrorDocument 400 /errordocument.html";if(s==106)ins="# (macht aus http://domain.de/xyz.html ein\n# http://domain.de/main.php?xyz)\nRewriteEngine on\nRewriteBase  /\nRewriteRule  ^([a-z]+)\.html$ /main.php?$1 [R,L]";if(s==107)ins="Deny from IPADRESS\nAllow from IPADRESS";if(s==108)ins="Redirect /service http://foo2.bar.com/service";if(s==109)ins="ErrorLog /path/logfile"
tb.value+="\n"+ins;}
function WP(s,obj){document.getElementById(obj).innerHTML=s;}
function resizeSQL(i){var obj=document.getElementById("sqltextarea");var h=0;if(i==0){obj.style.height='4px';}else{if(i==1)h=-20;if(i==2)h=20;var oh=obj.style.height;var s=Number(oh.substring(0,oh.length-2))+h;if(s<24)s=24;obj.style.height=s+'px';}}
function getObj(element,docname){if(document.layers){docname=(docname)?docname:self;if(f.document.layers[element]){return f.document.layers[element];}
for(W=0;i<f.document.layers.length;W++){return(getElement(element,fdocument.layers[W]));}}
if(document.all){return document.all[element];}
return document.getElementById(element);}
function InsertLib(i){var obj=document.getElementsByName('sqllib')[0];if(obj.selectedIndex>0){document.getElementById('sqlstring'+i).value=obj.options[obj.selectedIndex].value;document.getElementById('sqlname'+i).value=obj.options[obj.selectedIndex].text;}}
function DisplayExport(s){document.getElementById("export_working").InnerHTML=s;}
function SelectedTableCount(){var obj=document.getElementsByName('f_export_tables[]')[0];var anz=0;for(var i=0;i<obj.options.length;i++)
{if(obj.options[i].selected){anz++;}}
return anz;}
function SelectTableList(s){var obj=document.getElementsByName('f_export_tables[]')[0];for(var i=0;i<obj.options.length;i++){obj.options[i].selected=s;}}
function hide_csvdivs(i){document.getElementById("csv0").style.display='none';if(i==0){document.getElementById("csv1").style.display='none';document.getElementById("csv4").style.display='none';document.getElementById("csv5").style.display='none';}}
function check_csvdivs(i){hide_csvdivs(i);if(document.getElementById("radio_csv0").checked){document.getElementById("csv0").style.display='block';}
if(i==0){if(document.getElementById("radio_csv1").checked){document.getElementById("csv1").style.display='block';}else if(document.getElementById("radio_csv2").checked){document.getElementById("csv1").style.display='block';}else if(document.getElementById("radio_csv4").checked){document.getElementById("csv4").style.display='block';}else if(document.getElementById("radio_csv5").checked){document.getElementById("csv5").style.display='block';}}}