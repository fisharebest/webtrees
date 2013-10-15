<div id="content">
<h3>Bu Proje hakkinda</h3>
Bu projenin kurucusu Daniel Schlichtholz'dur.<p>2004 yilinda <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper panosunu </a>kurdu,
kisa bir süre sonra baskalari tarafindan destek gördü ve birçok kisinin katilimlari ile yazilim genisletilmeye baslandi. <p>istekleriniz veya önerileriniz için <a href="http://forum.mysqldumper.de" target="_blank">MySQLDumper-Panosuna</a> katilabilirsiniz.<p>
<br><p><h4>MySQLDumper-Ekibi</h4>

<table><tr><td><img src="images/logo.gif" alt="MySQLDumper" border="0"></td><td valign="top">
Daniel Schlichtholz</td></tr></table>
<br>

<h3>MySQLDumper Yardimi</h3>

<h4>Indirme</h4>
Bu yazilimi MySQLDumper Sitesinden temin edebilirsiniz.<br>
Güncellemeler ve destek için Sitemizi sik sik takip etmenizi tavsiye ederiz.<br>
Site adresi: <a href="http://www.mysqldumper.de" target="_blank">
http://www.mysqldumper.de</a>

<h4>Sistem gereksinimleri</h4>
Mysqldumper her sunucuda çalisir (Windows, Linux, ...) <br>
PHP sürümü >=  4.3.4 GZip-destekli, MySQL (3.23 sürümünden itibaren), JavaScript (aktiv olmak zorunda).

<a href="install.php?language=de" target="_top"><h4>Kurulum</h4></a>
Kurulumu çok basittir.
Sikistirilmis dosyayi herhangi bir klasör içerisinde açiniz.<br>
Açilann dosyalari FTP ile Sunucunuza yükleyiniz. (Örnegin root [sizindomain/]MySQLDumper)<br>
... bitti!<br>
Artik MySQLDumper'i tarayiciniz ile "http://sizindomain/MySQLDumper" adresini girerek açabilirsiniz,<br>
kurulumu tamamlamak için saadece Sistemin sorularini cevaplamaniz yeterlidir.<br>
<br><b>ÖNEMLI:</b><br><i>Sunucunuzda </i><tt>safe_mode</tt><i> açik ise, yazilimin klasör olusturma imkani yoktur.<br>
Gerekli klasörleri kendiniz olusturmaniz gerekir, MySqlDumper'in çalisabilmesi için belli bir düzende klasörlerin bulunmasi gerekir.<br>
Bu durumda yazilim kurulumu hata belirterek durduracaktir!<br>
Verilen hataya göre klasörleri olusturdugunuz taktirde yaziliminiz normal bir sekilde islev görecektir.</i>

<a name="perl"></a><h4>Perlskript kullanimi</h4>
Sunucularin birçogu Perl Scripleri destekler. <br>
Bu scriptlerin belli bir klasör içerisinde bulunmasi gerekebilir, klasörün adresi genellikle http://sizindomain/cgi-bin/ dir.
<br>
<br>
Bu durumda yapilmasi gereken islemler:<br><br>

1. MySQLDumper'i açtiktan sonra Yedekleme sayfasini açiniz ve "Yedekleme Perl" tusunu tiklayiniz. <br>
2. Crondump.pl de kayitli adres absolute_path_of_configdir: in arkasinda bulunan kayidi kopyalayiniz. <br>
3. "crondump.pl" Editör ile açiniz.<br>
4. Kopyaladiginiz adresi absolute_path_of_configdir´in arkasina yapistiriniz (bosluk birakilmayacak).<br>
5. Crondump.pl i kapatarak kayit ediniz.<br>
6. Crondump.pl, perltest.pl ve simpletest.pl dosyalarini cgi-bin-klasörüne kopyalayiniz (FTP ile Ascii-Modüsünde).<br>
7. Dosyalarin haklarini 755 olarak belirleyiniz (CHMOD). <br>
7b. Dosyabitimi cgi olmasi gerekiyorsa her 3 dosyanin adinin degistiriniz  pl -> cgi. <br>
8. Ayar Merkez, sayfasini açiniz.<br>
9. Cronscript e tiklayiniz. <br>
10. Perl veriyolunu /cgi-bin/ seklinde degistiriniz.<br>
10b. Kullanilan dosyabitimi  seçiniz.<br>
11. Ayarlari kayit ediniz. <br><br>

Ayarlar tamamlanmistir, Scriptleri yedekleme sayfasindan çalistirabilirsiniz.<br><br>

Perli her klasörden çalistirma yetkiniz bulunuyorsa:<br><br>

1. MySQLDumper'i açtiktan sonra Yedekleme sayfasini açiniz ve "Yedekleme Perl" tusunu tiklayiniz. <br>
2. Crondump.pl de kayitli adres absolute_path_of_configdir: in arkasinda bulunan kayidi kopyalayiniz. <br>
3. "crondump.pl" Editör ile açiniz. <br>
4. Kopyaladiginiz adresi absolute_path_of_configdir´in arkasina yapistiriniz (bosluk birakilmayacak).<br>
5. Crondump.pl i kapatarak kayit ediniz.<br>
6. Dosyalarin haklarini 755 olarak belirleyiniz (CHMOD). <br>
6b. Dosyabitimi cgi olmasi gerekiyorsa her 3 dosyanin adinin degistiriniz  pl -> cgi. <br>
(Gerekirse yukaridaki 10b+11 ci adimlari da uygulayiniz)<br>
<br>

Windows kullanicilarinin Scriptlerin ilk satirinda /cgi-bin/ veriyolunu degistirmeleri gerekir:<br>
#!/usr/bin/perl -w yerine <br>
#!C:\perl\bin\perl.exe -w yazilacak<br>

<h4>Kullanim</h4><ul>

<h6>Menü</h6>
islenecek Veritabanini burada seçeceksiniz.<br>
Bütün islemler burada beelirlenmis olan Veritabanina uygulanir.

<h6>Ana Sayfa</h6>
Burada kullandiginiz sistem hakkinda bilgiler bulabilirsiniz, yüklenmis
sürümler, Veritabanilari vs..<br>
Veritabni ismine tiklandiginda tablolarin listesine ulasilabilir.
Kayitsayisi ebat ve son güncelleme bilgilerini burada bulabilirsiniz.

<h6>Ayar Merkezi</h6>
Sistem ayarlarini burada belirleyebilir, yedeklemeden geri dönüstürebilir veya sifirlayabilirsiniz.
<ul><br>
    <li><a name="conf1"></a><strong>Veritabanlari:</strong> Veritabanlari Listesi. Aktiv olan Veritabani <b>kalin</b> yazilmistir. </li>
    <li><a name="conf2"></a><strong>Tablo ön eki:</strong> Burada belirleyeceginiz filtre tedeklenecek tablolarda uygulanacaktir
    	 (örnegin: "phpBB_" ile baslayan tablolar). Veritabaninin bütün tablolarini yedeklemek istiyorsanin burasini bos birakiniz.</li>
    <li><a name="conf3"></a><strong>Sikistirma:</strong> Sikistirmayi burada açabilirsiniz. Sikistirmayi kullanmanizi tavsiye ederiz.</li>
    <li><a name="conf5"></a><strong>Yedekleme ekli Mail:</strong> Bu Opsyon kullanildiginda, islemin sonunda gönderilecek mail'e yedekleme dosyasi eklenecektir. Sikistirmanin aktiv olmasini öneririz !).</li>
    <li><a name="conf6"></a><strong>Email-Adresi:</strong> Mailin ulastirilacagi adres.</li>
    <li><a name="conf7"></a><strong>Email göndericisi:</strong> gönderilecek mailin kimin adina gönderildigi.</li>
    <li><a name="conf13"></a><strong>FTP-Transferi: </strong>Bu Opsyon kullanildiginda, islem sonunda yedekleme dosyasi FTP ile gönderilir.</li>
    <li><a name="conf14"></a><strong>FTP Sunucusu: </strong>Die FTP sunucusunun adress (örnegin: ftp.mybackups.de).</li>
    <li><a name="conf15"></a><strong>FTP Sunucu Portu: </strong>FTP-Sunucusunun Portu (Genelde 21).</li>
    <li><a name="conf16"></a><strong>FTP Kulanicisi: </strong>FTP-kullanicisinin adi. </li>
    <li><a name="conf17"></a><strong>FTP sifresi: </strong>FTP-kullanicisinin  sifresi. </li>
    <li><a name="conf18"></a><strong>FTP yükleme klasörü: </strong>Yedekleme dosyasinin yüklenecegi klasörün adi. (UYARI: CHMOD ayarlarini göz önünde bulundurunuz).</li>
    <li><a name="conf8"></a><strong>Otomatik dosya silme:</strong> Bu Opsyon kullanildiginda, belirlenecek kurallara göre yedekleme dosyalari silinecektir.</li>
    <li><a name="conf10"></a><strong>Dosya sayisi:</strong> 0 dan büyük bir deger, bu degeri asan dosya sayisindan fazla olan dosyalari silecektir.</li>
    <li><a name="conf11"></a><strong>Dil:</strong> MySQL Dumperin kullanacagi dili burada belirlersiniz.</li>
    <li><a name="conf12"></a><strong>Cronjob zamandilimi:</strong> Saniye olarak belirlenecek deger, Cronjob için geçerli olan süreyi (eger yetkiniz varsa) yükseltmege deger.</li>
</ul>

<h6>Dosya yönetimi</h6>
Dosya islemleri burada yapilir
Yedekleme Klasörünüzde bulunan dosyalar listelenir.<br>
islemlerin uygulanabilmesi için bir dosyanin seçilmis olmasi gerekiyor.
<UL>
    <li><strong>Restore:</strong> Burada veritabani, seçilmis dosya ile dönüstürülür.</li>
    <li><strong>Delete:</strong> Seçilmis yedekleme dosyalari silinir.</li>
    <li><strong>Yeni Yedekleme baslat:</strong> Ayarlarda belirlenmis sartlarla yeni bir yedekleme olusturulur.</li>
</UL>

<h6>Raporlar</h6>
Burada rapor dosyalarini görebilir veya silebilirsiniz.
<h6>Künye / Yardim</h6>
bu Sayfa.
</ul>