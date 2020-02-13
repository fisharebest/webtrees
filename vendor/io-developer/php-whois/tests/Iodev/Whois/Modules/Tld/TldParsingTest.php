<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Loaders\FakeSocketLoader;
use Iodev\Whois\Whois;

class TldParsingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $filename
     * @return bool|string
     */
    private static function loadContent($filename)
    {
        $file = __DIR__ . '/parsing_data/' . $filename;
        if (!file_exists($file)) {
            throw new InvalidArgumentException("File '$file' not found");
        }
        return file_get_contents($file);
    }

    /**
     * @param string $filename
     * @return Whois
     */
    private static function whoisFrom($filename)
    {
        $l = new FakeSocketLoader();
        $l->text = self::loadContent($filename);
        return new Whois($l);
    }

    /**
     * @param array $a
     * @return array
     */
    private static function sort($a)
    {
        sort($a);
        return $a;
    }

    /**
     * @dataProvider getTestData
     *
     * @param string $domain
     * @param string $srcTextFilename
     * @param string $expectedJsonFilename
     * @throws ConnectionException
     * @throws ServerMismatchException
     * @throws \Iodev\Whois\Exceptions\WhoisException
     */
    public function testResponseParsing($domain, $srcTextFilename, $expectedJsonFilename = null)
    {
        $w = self::whoisFrom($srcTextFilename);
        $tld = $w->getTldModule();
        $info = $tld->loadDomainInfo($domain);

        if (empty($expectedJsonFilename)) {
            $this->assertNull($info, "Loaded info should be null for free domain ($srcTextFilename)");
            $this->assertTrue($tld->isDomainAvailable($domain), "Free domain should be available ($srcTextFilename)");
            return;
        }

        $expected = json_decode(self::loadContent($expectedJsonFilename), true);
        $this->assertNotEmpty($expected, "Failed to load/parse expected json ($expectedJsonFilename)");

        $this->assertNotNull($info, "Loaded info should not be null ($srcTextFilename)");
        $this->assertFalse($tld->isDomainAvailable($domain), "Domain should not be available ($srcTextFilename)");

        $this->assertEquals(
            $expected["domainName"],
            $info->getDomainName(),
            "Domain name mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            $expected["whoisServer"],
            $info->getWhoisServer(),
            "Whois server mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            self::sort($expected["nameServers"]),
            self::sort($info->getNameServers()),
            "Name servers mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            strtotime($expected["creationDate"]),
            $info->getCreationDate(),
            "Creation date mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            strtotime($expected["expirationDate"]),
            $info->getExpirationDate(),
            "expirationDate mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            self::sort($expected["states"]),
            self::sort($info->getStates()),
            "States mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            $expected["owner"],
            $info->getOwner(),
            "Owner mismatch ($srcTextFilename)"
        );
        $this->assertEquals(
            $expected["registrar"],
            $info->getRegistrar(),
            "Registrar mismatch ($srcTextFilename)"
        );
    }

    public function getTestData()
    {
        return [
            // .AC
            [ "free.ac", ".ac/free.txt", null ],
            [ "google.ac", ".ac/google.ac.txt", ".ac/google.ac.json" ],

            // .AE
            [ "free.ae", ".ae/free.txt", null ],
            [ "google.ae", ".ae/google.ae.txt", ".ae/google.ae.json" ],

            // .AF
            [ "free.af", ".af/free.txt", null ],
            [ "google.com.af", ".af/google.com.af.txt", ".af/google.com.af.json" ],

            // .AG
            [ "free.ag", ".ag/free.txt", null ],
            [ "google.com.ag", ".ag/google.com.ag.txt", ".ag/google.com.ag.json" ],

            // .AI
            [ "free.ai", ".ai/free.txt", null ],
            [ "google.com.ai", ".ai/google.com.ai.txt", ".ai/google.com.ai.json" ],

            // .AM
            [ "free.am", ".am/free.txt", null ],
            [ "google.am", ".am/google.am.txt", ".am/google.am.json" ],
            [ "google.com.am", ".am/google.com.am.txt", ".am/google.com.am.json" ],

            // .AO
            [ "free.ao", ".ao/free.txt", null ],
            [ "google.it.ao", ".ao/google.it.ao.txt", ".ao/google.it.ao.json" ],

            // .AS
            [ "free.as", ".as/free.txt", null ],
            [ "google.as", ".as/google.as.txt", ".as/google.as.json" ],

            // .AR
            [ "free.ar", ".ar/free.txt", null ],
            [ "google.com.ar", ".ar/google.com.ar.txt", ".ar/google.com.ar.json" ],

            // .AT
            [ "free.at", ".at/free.txt", null ],
            [ "google.at", ".at/google.at.txt", ".at/google.at.json" ],

            // .AU
            [ "free.au", ".au/free.txt", null ],
            [ "google.com.au", ".au/google.com.au.txt", ".au/google.com.au.json" ],

            // .BABY
            [ "free.baby", ".baby/free.txt", null ],
            [ "google.baby", ".baby/google.baby.txt", ".baby/google.baby.json" ],

            // .BE
            [ "free.be", ".be/free.txt", null ],
            [ "google.be", ".be/google.be.txt", ".be/google.be.json" ],
            [ "youtu.be", ".be/youtu.be.txt", ".be/youtu.be.json" ],

            // .BG
            [ "free.bg", ".bg/free.txt", null ],
            [ "google.bg", ".bg/google.bg.txt", ".bg/google.bg.json" ],

            // .BI
            [ "free.bi", ".bi/free.txt", null ],
            [ "google.bi", ".bi/google.bi.txt", ".bi/google.bi.json" ],

            // .BJ
            [ "free.bj", ".bj/free.txt", null ],
            [ "google.bj", ".bj/google.bj.txt", ".bj/google.bj.json" ],

            // .BM
            [ "free.bm", ".bm/free.txt", null ],
            [ "bermudanic.bm", ".bm/bermudanic.bm.txt", ".bm/bermudanic.bm.json" ],

            // .BN
            [ "free.bn", ".bn/free.txt", null ],
            [ "google.com.bn", ".bn/google.com.bn.txt", ".bn/google.com.bn.json" ],

            // .BO
            [ "free.bo", ".bo/free.txt", null ],
            [ "google.com.bo", ".bo/google.com.bo.txt", ".bo/google.com.bo.json" ],

            // .BR
            [ "free.br", ".br/free.txt", null ],
            [ "google.com.br", ".br/google.com.br.txt", ".br/google.com.br.json" ],

            // .BW
            [ "free.bw", ".bw/free.txt", null ],
            [ "google.co.bw", ".bw/google.co.bw.txt", ".bw/google.co.bw.json" ],

            // .BY
            [ "free.by", ".by/free.txt", null ],
            [ "google.by", ".by/google.by.txt", ".by/google.by.json" ],
            [ "google.com.by", ".by/google.com.by.txt", ".by/google.com.by.json" ],

            // .BZ
            [ "free.bz", ".bz/free.txt", null ],
            [ "google.com.bz", ".bz/google.com.bz.txt", ".bz/google.com.bz.json" ],

            // .CAT
            [ "free.cat", ".cat/free.txt", null ],
            [ "google.cat", ".cat/google.cat.txt", ".cat/google.cat.json" ],

            // .CC
            [ "free.cc", ".cc/free.txt", null ],
            [ "google.cc", ".cc/google.cc.txt", ".cc/google.cc.json" ],

            // .CF
            [ "free.cf", ".cf/free.txt", null ],
            [ "google.cf", ".cf/google.cf.txt", ".cf/google.cf.json" ],

            // .CI
            [ "free.ci", ".ci/free.txt", null ],
            [ "google.ci", ".ci/google.ci.txt", ".ci/google.ci.json" ],

            // .CL
            [ "free.cl", ".cl/free.txt", null ],
            // [ "google.cl", ".cl/google.cl.txt", ".cl/google.cl.json" ],

            // .CM
            [ "free.cm", ".cm/free.txt", null ],
            [ "google.cm", ".cm/google.cm.txt", ".cm/google.cm.json" ],

            // .CN
            [ "free.cn", ".cn/free.txt", null ],
            [ "google.cn", ".cn/google.cn.txt", ".cn/google.cn.json" ],

            // .CO
            [ "free.co", ".co/free.txt", null ],
            [ "google.co", ".co/google.co.txt", ".co/google.co.json" ],
            [ "google.com.co", ".co/google.com.co.txt", ".co/google.com.co.json" ],

            // .COM
            [ "free.com", ".com/free.txt", null ],
            [ "google.com", ".com/google.com.txt", ".com/google.com.json" ],
            [ "google.com", ".com/google.com_registrar_whois.txt", ".com/google.com_registrar_whois.json" ],

            // .CR
            [ "free.cr", ".cr/free.txt", null ],
            [ "google.co.cr", ".cr/google.co.cr.txt", ".cr/google.co.cr.json" ],

            // .CZ
            [ "free.cz", ".cz/free.txt", null ],
            [ "google.cz", ".cz/google.cz.txt", ".cz/google.cz.json" ],

            // .DE
            [ "free.de", ".de/free.txt", null ],
            [ "google.de", ".de/google.de.txt", ".de/google.de.json" ],
            [ "fäw.de", ".de/xn--fw-via.de.txt", ".de/xn--fw-via.de.json" ],

            // .DK
            [ "free.dk", ".dk/free.txt", null ],
            [ "google.dk", ".dk/google.dk.txt", ".dk/google.dk.json" ],

            // .DM
            [ "free.dm", ".dm/free.txt", null ],
            [ "google.dm", ".dm/google.dm.txt", ".dm/google.dm.json" ],

            // .DO
            [ "free.do", ".do/free.txt", null ],
            [ "google.com.do", ".do/google.com.do.txt", ".do/google.com.do.json" ],

            // .DZ
            [ "free.dz", ".dz/free.txt", null ],
            [ "google.dz", ".dz/google.dz.txt", ".dz/google.dz.json" ],

            // .EC
            [ "free.ec", ".ec/free.txt", null ],
            // [ "google.com.ec", ".ec/google.com.ec.txt", ".ec/google.com.ec.json" ],

            // .EE
            [ "free.ee", ".ee/free.txt", null ],
            [ "google.ee", ".ee/google.ee.txt", ".ee/google.ee.json" ],

            // .ES
            [ "free.es", ".es/free.txt", null ],
            [ "google.es", ".es/google.es.txt", ".es/google.es.json" ],

            // .EU
            [ "dsfasdfasdfsdafasfasfas.eu", ".eu/free.txt", null ],
            [ "google.eu", ".eu/google.eu.txt", ".eu/google.eu.json" ],

            // .FI
            [ "free.fi", ".fi/free.txt", null ],
            [ "google.fi", ".fi/google.fi.txt", ".fi/google.fi.json" ],
            [ "xn--sisministeri-icb5x.fi", ".fi/xn--sisministeri-icb5x.fi.txt", ".fi/xn--sisministeri-icb5x.fi.json" ],
            [ "sisäministeriö.fi", ".fi/xn--sisministeri-icb5x.fi.txt", ".fi/xn--sisministeri-icb5x.fi.json" ],

            // .FJ
            [ "free.fj", ".fj/free.txt", null ],
            // [ "google.com.fj", ".fj/google.com.fj.txt", ".fj/google.com.fj.json" ],

            // .FM
            [ "free.fm", ".fm/free.txt", null ],
            [ "google.fm", ".fm/google.fm.txt", ".fm/google.fm.json" ],

            // .FR
            [ "free.fr", ".fr/free.txt", null ],
            [ "google.fr", ".fr/google.fr.txt", ".fr/google.fr.json" ],

            // .GA
            [ "free.ga", ".ga/free.txt", null ],
            // [ "google.ga", ".ga/google.ga.txt", ".ga/google.ga.json" ],

            // .GD
            [ "free.gd", ".gd/free.txt", null ],
            [ "google.gd", ".gd/google.gd.txt", ".gd/google.gd.json" ],

            // .GF
            [ "free.gf", ".gf/free.txt", null ],
            // [ "google.gf", ".gf/google.gf.txt", ".gf/google.gf.json" ],

            // .GG
            [ "free.gg", ".gg/free.txt", null ],
            // [ "google.gg", ".gg/google.gg.txt", ".gg/google.gg.json" ],

            // .GI
            [ "free.gi", ".gi/free.txt", null ],
            [ "google.com.gi", ".gi/google.com.gi.txt", ".gi/google.com.gi.json" ],

            // .GL
            [ "free.gl", ".gl/free.txt", null ],
            [ "google.gl", ".gl/google.gl.txt", ".gl/google.gl.json" ],

            // .GOV
            [ "free.gov", ".gov/free.txt", null ],
            [ "usa.gov", ".gov/usa.gov.txt", ".gov/usa.gov.json" ],

            // .GY
            [ "free.gy", ".gy/free.txt", null ],
            [ "google.gy", ".gy/google.gy.txt", ".gy/google.gy.json" ],

            // .HK
            [ "free.hk", ".hk/free.txt", null ],
            // [ "google.com.hk", ".hk/google.com.hk.txt", ".hk/google.com.hk.json" ],

            // .HR
            [ "free.hr", ".hr/free.txt", null ],
            [ "google.hr", ".hr/google.hr.txt", ".hr/google.hr.json" ],

            // .HT
            [ "free.ht", ".ht/free.txt", null ],
            [ "google.ht", ".ht/google.ht.txt", ".ht/google.ht.json" ],

            // .HU
            [ "free.hu", ".hu/free.txt", null ],
            [ "google.hu", ".hu/google.hu.txt", ".hu/google.hu.json" ],

            // .ID
            [ "free.id", ".id/free.txt", null ],
            [ "google.co.id", ".id/google.co.id.txt", ".id/google.co.id.json" ],

            // .IE
            [ "free.ie", ".ie/free.txt", null ],
            [ "google.ie", ".ie/google.ie.txt", ".ie/google.ie.json" ],

            // .IL
            [ "free.il", ".il/free.txt", null ],
            [ "google.co.il", ".il/google.co.il.txt", ".il/google.co.il.json" ],

            // .IM
            [ "free.im", ".im/free.txt", null ],
            // [ "google.im", ".im/google.im.txt", ".im/google.im.json" ],

            // .IN
            [ "free.in", ".in/free.txt", null ],
            [ "google.co.in", ".in/google.co.in.txt", ".in/google.co.in.json" ],

            // .INFO
            [ "free.info", ".info/free.txt", null ],
            [ "info.info", ".info/info.info.txt", ".info/info.info.json" ],

            // .IO
            [ "free.io", ".io/free.txt", null ],
            [ "github.io", ".io/github.io.txt", ".io/github.io.json" ],
            [ "google.io", ".io/google.io.txt", ".io/google.io.json" ],
            [ "codepen.io", ".io/codepen.io.txt", ".io/codepen.io.json" ],

            // .IQ
            [ "free.iq", ".iq/free.txt", null ],
            [ "google.iq", ".iq/google.iq.txt", ".iq/google.iq.json" ],

            // .IR
            [ "free.ir", ".ir/free.txt", null ],
            [ "mhf.ir", ".ir/mhf.ir.txt", ".ir/mhf.ir.json" ],

            // .IS
            [ "free.is", ".is/free.txt", null ],
            [ "google.is", ".is/google.is.txt", ".is/google.is.json" ],

            // .IT
            [ "free.it", ".it/free.txt", null ],
            [ "google.it", ".it/google.it.txt", ".it/google.it.json" ],
            [ "nintendo.it", ".it/nintendo.it.txt", ".it/nintendo.it.json" ],

            // .JE
            [ "free.je", ".je/free.txt", null ],
            // [ "google.je", ".je/google.je.txt", ".je/google.je.json" ],

            // .JP
            [ "free.jp", ".jp/free.txt", null ],
            [ "shop.jp", ".jp/shop.jp.txt", ".jp/shop.jp.json" ],
            [ "google.co.jp", ".jp/google.co.jp.txt", ".jp/google.co.jp.json" ],

            // .KG
            [ "free.kg", ".kg/free.txt", null ],
            // [ "google.kg", ".kg/google.kg.txt", ".kg/google.kg.json" ],

            // .KI
            [ "free.ki", ".ki/free.txt", null ],
            [ "google.ki", ".ki/google.ki.txt", ".ki/google.ki.json" ],

            // .KR
            [ "free.kr", ".kr/free.txt", null ],
            [ "google.co.kr", ".kr/google.co.kr.txt", ".kr/google.co.kr.json" ],

            // .KZ
            [ "free.kz", ".kz/free.txt", null ],
            [ "google.kz", ".kz/google.kz.txt", ".kz/google.kz.json" ],

            // .LA
            [ "free.la", ".la/free.txt", null ],
            [ "google.la", ".la/google.la.txt", ".la/google.la.json" ],

            // .LC
            [ "free.lc", ".lc/free.txt", null ],
            [ "google.com.lc", ".lc/google.com.lc.txt", ".lc/google.com.lc.json" ],

            // .LT
            [ "free.lt", ".lt/free.txt", null ],
            [ "google.lt", ".lt/google.lt.txt", ".lt/google.lt.json" ],

            // .LTD
            [ "free.ltd", ".ltd/free.txt", null ],
            [ "donuts.ltd", ".ltd/donuts.ltd.txt", ".ltd/donuts.ltd.json" ],

            // .LU
            [ "free.lu", ".lu/free.txt", null ],
            [ "google.lu", ".lu/google.lu.txt", ".lu/google.lu.json" ],

            // .LV
            [ "free.lv", ".lv/free.txt", null ],
            [ "google.lv", ".lv/google.lv.txt", ".lv/google.lv.json" ],

            // .LY
            [ "free.ly", ".ly/free.txt", null ],
            // [ "google.com.ly", ".ly/google.com.ly.txt", ".ly/google.com.ly.json" ],

            // .MA
            [ "free.ma", ".ma/free.txt", null ],
            [ "google.co.ma", ".ma/google.co.ma.txt", ".ma/google.co.ma.json" ],

            // .MD
            [ "free.md", ".md/free.txt", null ],
            [ "google.md", ".md/google.md.txt", ".md/google.md.json" ],

            // .ME
            [ "free.me", ".me/free.txt", null ],
            [ "google.me", ".me/google.me.txt", ".me/google.me.json" ],

            // .MG
            [ "free.mg", ".mg/free.txt", null ],
            [ "google.mg", ".mg/google.mg.txt", ".mg/google.mg.json" ],

            // .MK
            [ "free.mk", ".mk/free.txt", null ],
            [ "google.mk", ".mk/google.mk.txt", ".mk/google.mk.json" ],

            // .ML
            [ "free.ml", ".ml/free.txt", null ],
            // [ "google.ml", ".ml/google.ml.txt", ".ml/google.ml.json" ],

            // .MN
            [ "free.mn", ".mn/free.txt", null ],
            [ "google.mn", ".mn/google.mn.txt", ".mn/google.mn.json" ],

            // .MS
            [ "free.ms", ".ms/free.txt", null ],
            [ "google.ms", ".ms/google.ms.txt", ".ms/google.ms.json" ],

            // .MU
            [ "free.mu", ".mu/free.txt", null ],
            [ "google.mu", ".mu/google.mu.txt", ".mu/google.mu.json" ],

            // .MX
            [ "free.mx", ".mx/free.txt", null ],
            // [ "google.com.mx", ".mx/google.com.mx.txt", ".mx/google.com.mx.json" ],

            // .MZ
            [ "free.mz", ".mz/free.txt", null ],
            [ "google.co.mz", ".mz/google.co.mz.txt", ".mz/google.co.mz.json" ],

            // .NA
            [ "free.na", ".na/free.txt", null ],
            [ "google.com.na", ".na/google.com.na.txt", ".na/google.com.na.json" ],

            // .NET
            [ "free.net", ".net/free.txt", null ],
            [ "speedtest.net", ".net/speedtest.net.txt", ".net/speedtest.net.json" ],
            [ "speedtest.net", ".net/speedtest.net_registrar_whois.txt", ".net/speedtest.net_registrar_whois.json" ],

            // .NEWS
            [ "free.news", ".news/free.txt", null ],
            [ "google.news", ".news/google.news.txt", ".news/google.news.json" ],

            // .NF
            [ "free.nf", ".nf/free.txt", null ],
            [ "google.com.nf", ".nf/google.com.nf.txt", ".nf/google.com.nf.json" ],

            // .NG
            [ "free.ng", ".ng/free.txt", null ],
            [ "google.com.ng", ".ng/google.com.ng.txt", ".ng/google.com.ng.json" ],

            // .NL
            [ "free.nl", ".nl/free.txt", null ],
            // [ "google.nl", ".nl/google.nl.txt", ".nl/google.nl.json" ],

            // .NO
            [ "free.no", ".no/free.txt", null ],
            [ "google.no", ".no/google.no.txt", ".no/google.no.json" ],

            // .NU
            [ "free.nu", ".nu/free.txt", null ],
            [ "google.nu", ".nu/google.nu.txt", ".nu/google.nu.json" ],

            // .NZ
            [ "free.nz", ".nz/free.txt", null ],
            [ "progressbuilders.co.nz.nz", ".nz/free_progressbuilders.co.nz.nz.txt", null ],
            [ "secuirty-services.co.nz", ".nz/free_secuirty-services.co.nz.txt", null ],
            [ "google.co.nz", ".nz/google.co.nz.txt", ".nz/google.co.nz.json" ],
            [ "payrollmatters.co.nz", ".nz/payrollmatters.co.nz.txt", ".nz/payrollmatters.co.nz.json" ],
            [ "smarttech.nz", ".nz/smarttech.nz.txt", ".nz/smarttech.nz.json" ],

            // .ORG
            [ "free.org", ".org/free.txt", null ],
            [ "linux.org", ".org/linux.org.txt", ".org/linux.org.json" ],

            // .OM
            [ "free.om", ".om/free.txt", null ],
            [ "google.com.om", ".om/google.com.om.txt", ".om/google.com.om.json" ],

            // .PAGE
            [ "free.page", ".page/free.txt", null ],
            [ "microsoft.page", ".page/microsoft.page.txt", ".page/microsoft.page.json" ],

            // .PE
            [ "free.pe", ".pe/free.txt", null ],
            [ "google.com.pe", ".pe/google.com.pe.txt", ".pe/google.com.pe.json" ],

            // .PL
            [ "free.pl", ".pl/free.txt", null ],
            [ "google.pl", ".pl/google.pl.txt", ".pl/google.pl.json" ],

            // .PR
            [ "free.pr", ".pr/free.txt", null ],
            [ "google.com.pr", ".pr/google.com.pr.txt", ".pr/google.com.pr.json" ],

            // .PS
            [ "free.ps", ".ps/free.txt", null ],
            [ "google.ps", ".ps/google.ps.txt", ".ps/google.ps.json" ],

            // .QA
            [ "free.qa", ".qa/free.txt", null ],
            [ "google.com.qa", ".qa/google.com.qa.txt", ".qa/google.com.qa.json" ],

            // .RO
            [ "free.ro", ".ro/free.txt", null ],
            [ "google.ro", ".ro/google.ro.txt", ".ro/google.ro.json" ],
            [ "rotld.ro", ".ro/rotld.ro.txt", ".ro/rotld.ro.json" ],
            [ "anaf.ro", ".ro/anaf.ro.txt", ".ro/anaf.ro.json" ],

            // .RS
            [ "free.rs", ".rs/free.txt", null ],
            [ "google.rs", ".rs/google.rs.txt", ".rs/google.rs.json" ],

            // .RU
            [ "free.ru", ".ru/free.txt", null ],
            [ "google.ru", ".ru/google.ru.txt", ".ru/google.ru.json" ],

            // .RW
            [ "free.rw", ".rw/free.txt", null ],
            [ "google.rw", ".rw/google.rw.txt", ".rw/google.rw.json" ],

            // .SA
            [ "free.sa", ".sa/free.txt", null ],
            // [ "google.com.sa", ".sa/google.com.sa.txt", ".sa/google.com.sa.json" ],

            // .SB
            [ "free.sb", ".sb/free.txt", null ],
            [ "google.com.sb", ".sb/google.com.sb.txt", ".sb/google.com.sb.json" ],

            // .SC
            [ "free.sc", ".sc/free.txt", null ],
            [ "google.sc", ".sc/google.sc.txt", ".sc/google.sc.json" ],

            // .SE
            [ "free.se", ".se/free.txt", null ],
            [ "google.se", ".se/google.se.txt", ".se/google.se.json" ],

            // .SG
            [ "free.sg", ".sg/free.txt", null ],
            // [ "google.com.sg", ".sg/google.com.sg.txt", ".sg/google.com.sg.json" ],

            // .SH
            [ "free.sh", ".sh/free.txt", null ],
            [ "google.sh", ".sh/google.sh.txt", ".sh/google.sh.json" ],

            // .SI
            [ "free.si", ".si/free.txt", null ],
            [ "google.si", ".si/google.si.txt", ".si/google.si.json" ],

            // .SK
            [ "free.sk", ".sk/free.txt", null ],
            [ "google.sk", ".sk/google.sk.txt", ".sk/google.sk.json" ],

            // .SL
            [ "free.sl", ".sl/free.txt", null ],
            [ "google.com.sl", ".sl/google.com.sl.txt", ".sl/google.com.sl.json" ],

            // .SM
            [ "free.sm", ".sm/free.txt", null ],
            // [ "google.sm", ".sm/google.sm.txt", ".sm/google.sm.json" ],

            // .SN
            [ "free.sn", ".sn/free.txt", null ],
            // [ "google.sn", ".sn/google.sn.txt", ".sn/google.sn.json" ],

            // .SO
            [ "free.so", ".so/free.txt", null ],
            [ "google.so", ".so/google.so.txt", ".so/google.so.json" ],

            // .ST
            [ "free.st", ".st/free.txt", null ],
            [ "google.st", ".st/google.st.txt", ".st/google.st.json" ],

            // .TG
            [ "free.tg", ".tg/free.txt", null ],
            [ "google.tg", ".tg/google.tg.txt", ".tg/google.tg.json" ],

            // .TH
            [ "free.th", ".th/free.txt", null ],
            [ "google.co.th", ".th/google.co.th.txt", ".th/google.co.th.json" ],

            // .TK
            [ "free.tk", ".tk/free.txt", null ],
            // [ "google.tk", ".tk/google.tk.txt", ".tk/google.tk.json" ],

            // .TL
            [ "free.tl", ".tl/free.txt", null ],
            [ "google.tl", ".tl/google.tl.txt", ".tl/google.tl.json" ],

            // .TM
            [ "free.tm", ".tm/free.txt", null ],
            [ "google.tm", ".tm/google.tm.txt", ".tm/google.tm.json" ],

            // .TN
            [ "free.tn", ".tn/free.txt", null ],
            [ "ati.tn", ".tn/ati.tn.txt", ".tn/ati.tn.json" ],
            [ "google.com.tn", ".tn/google.com.tn.txt", ".tn/google.com.tn.json" ],

            // .TO
            [ "free.to", ".to/free.txt", null ],
            // [ "google.to", ".to/google.to.txt", ".to/google.to.json" ],

            // .TR
            [ "free.tr", ".tr/free.txt", null ],
            // [ "google.com.tr", ".tr/google.com.tr.txt", ".tr/google.com.tr.json" ],

            // .TW
            [ "free.tw", ".tw/free.txt", null ],
            // [ "google.com.tw", ".tw/google.com.tw.txt", ".tw/google.com.tw.json" ],

            // .TZ
            [ "free.tz", ".tz/free.txt", null ],
            [ "google.co.tz", ".tz/google.co.tz.txt", ".tz/google.co.tz.json" ],

            // .UA
            [ "free.ua", ".ua/free.txt", null ],
            [ "google.com.ua", ".ua/google.com.ua.txt", ".ua/google.com.ua.json" ],

            // .UK
            [ "free.uk", ".uk/free.txt", null ],
            [ "google.co.uk", ".uk/google.co.uk.txt", ".uk/google.co.uk.json" ],

            // .US
            [ "free.us", ".us/free.txt", null ],
            [ "google.us", ".us/google.us.txt", ".us/google.us.json" ],

            // .UY
            [ "free.uy", ".uy/free.txt", null ],
            // [ "google.uy", ".uy/google.uy.txt", ".uy/google.uy.json" ],

            // .UZ
            [ "free.uz", ".uz/free.txt", null ],
            [ "google.uz", ".uz/google.uz.txt", ".uz/google.uz.json" ],

            // .VC
            [ "free.vc", ".vc/free.txt", null ],
            [ "google.com.vc", ".vc/google.com.vc.txt", ".vc/google.com.vc.json" ],

            // .VE
            [ "free.ve", ".ve/free.txt", null ],
            // [ "google.co.ve", ".ve/google.co.ve.txt", ".ve/google.co.ve.json" ],

            // .VG
            [ "free.vg", ".vg/free.txt", null ],
            [ "google.vg", ".vg/google.vg.txt", ".vg/google.vg.json" ],

            // .VIP
            [ "free.vip", ".vip/free.txt", null ],
            [ "google.vip", ".vip/google.vip.txt", ".vip/google.vip.json" ],

            // .VU
            [ "free.vu", ".vu/free.txt", null ],
            // [ "google.vu", ".vu/google.vu.txt", ".vu/google.vu.json" ],

            // .WS
            [ "free.ws", ".ws/free.txt", null ],
            [ "google.ws", ".ws/google.ws.txt", ".ws/google.ws.json" ],

            // .ZM
            [ "free.zm", ".zm/free.txt", null ],
            [ "google.co.zm", ".zm/google.co.zm.txt", ".zm/google.co.zm.json" ],

            // .РФ
            [ "free.xn--p1ai", ".xn--p1ai/free.txt", null ],
            [ "xn--80a1acny.xn--p1ai", ".xn--p1ai/xn--80a1acny.xn--p1ai.txt", ".xn--p1ai/xn--80a1acny.xn--p1ai.json" ],

            // .XIN
            [ "free.xin", ".xin/free.txt", null ],
            [ "microsoft.xin", ".xin/microsoft.xin.txt", ".xin/microsoft.xin.json" ],
        ];
    }
}