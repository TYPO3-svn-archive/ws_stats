<?php

########################################################################
# Extension Manager/Repository config file for ext: "ws_stats"
#
# Auto generated 09-02-2009 13:12
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Visitor Tracking',
	'description' => 'Statistics for your T3-Website with chart: IP, hostname, referer, search engine, search phrase, language. Chronological order, grouped by visitors. Perfect for Search Engine Optimization (SEO).',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.1.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Sven Wappler',
	'author_email' => 'typo3@wapplersystems.de',
	'author_company' => 'WapplerSystems',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:284:{s:29:"class.tx_wsstats_tsfehook.php";s:4:"6c5b";s:21:"ext_conf_template.txt";s:4:"42cb";s:12:"ext_icon.gif";s:4:"462a";s:17:"ext_localconf.php";s:4:"8f33";s:14:"ext_tables.php";s:4:"f1bd";s:14:"ext_tables.sql";s:4:"925c";s:16:"locallang_db.php";s:4:"ab39";s:7:"tca.php";s:4:"238f";s:16:"wsstats/main.php";s:4:"f712";s:19:"wsstats/wsstats.php";s:4:"d10d";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"7ff5";s:16:"mod1/iehacks.css";s:4:"132d";s:14:"mod1/index.php";s:4:"b2e2";s:18:"mod1/locallang.xml";s:4:"5d9b";s:22:"mod1/locallang_mod.xml";s:4:"2aa4";s:19:"mod1/moduleicon.gif";s:4:"462a";s:14:"mod1/style.css";s:4:"d6bb";s:14:"doc/manual.sxw";s:4:"f87b";s:13:"res/cross.png";s:4:"4249";s:12:"res/info.png";s:4:"f42d";s:20:"res/list-bg-gold.png";s:4:"7ec5";s:15:"res/list-bg.png";s:4:"4d78";s:16:"res/list-bg2.png";s:4:"8bf9";s:16:"res/list-bg3.png";s:4:"385d";s:14:"res/sum-bg.png";s:4:"9c26";s:37:"res/protochart/excanvas-compressed.js";s:4:"4df6";s:26:"res/protochart/excanvas.js";s:4:"4838";s:28:"res/protochart/protochart.js";s:4:"fde3";s:27:"res/protochart/prototype.js";s:4:"85ef";s:16:"res/flags/ad.png";s:4:"cc75";s:16:"res/flags/ae.png";s:4:"7391";s:16:"res/flags/af.png";s:4:"ae7c";s:16:"res/flags/ag.png";s:4:"390a";s:16:"res/flags/ai.png";s:4:"08cf";s:16:"res/flags/al.png";s:4:"7c5b";s:16:"res/flags/am.png";s:4:"fd5d";s:16:"res/flags/an.png";s:4:"7d7d";s:16:"res/flags/ao.png";s:4:"41a8";s:16:"res/flags/ar.png";s:4:"2fa3";s:16:"res/flags/as.png";s:4:"96e4";s:16:"res/flags/at.png";s:4:"62bf";s:16:"res/flags/au.png";s:4:"2fba";s:16:"res/flags/aw.png";s:4:"6e82";s:16:"res/flags/ax.png";s:4:"2770";s:16:"res/flags/az.png";s:4:"d63f";s:16:"res/flags/ba.png";s:4:"cbb6";s:16:"res/flags/bb.png";s:4:"47c8";s:16:"res/flags/bd.png";s:4:"f02d";s:16:"res/flags/be.png";s:4:"2404";s:16:"res/flags/bf.png";s:4:"cc65";s:16:"res/flags/bg.png";s:4:"77b2";s:16:"res/flags/bh.png";s:4:"5bbf";s:16:"res/flags/bi.png";s:4:"427c";s:16:"res/flags/bj.png";s:4:"67bd";s:16:"res/flags/bm.png";s:4:"cf19";s:16:"res/flags/bn.png";s:4:"4911";s:16:"res/flags/bo.png";s:4:"1518";s:16:"res/flags/br.png";s:4:"54c4";s:16:"res/flags/bs.png";s:4:"8b45";s:16:"res/flags/bt.png";s:4:"2f13";s:16:"res/flags/bv.png";s:4:"559c";s:16:"res/flags/bw.png";s:4:"15d5";s:16:"res/flags/by.png";s:4:"9e18";s:16:"res/flags/bz.png";s:4:"6e14";s:16:"res/flags/ca.png";s:4:"8618";s:23:"res/flags/catalonia.png";s:4:"7699";s:16:"res/flags/cc.png";s:4:"ebbf";s:16:"res/flags/cd.png";s:4:"34e2";s:16:"res/flags/cf.png";s:4:"252d";s:16:"res/flags/cg.png";s:4:"b5be";s:16:"res/flags/ch.png";s:4:"e67b";s:16:"res/flags/ci.png";s:4:"90e8";s:16:"res/flags/ck.png";s:4:"e704";s:16:"res/flags/cl.png";s:4:"dc7b";s:16:"res/flags/cm.png";s:4:"f5cd";s:16:"res/flags/cn.png";s:4:"a82f";s:16:"res/flags/co.png";s:4:"4bd2";s:16:"res/flags/cr.png";s:4:"cd28";s:16:"res/flags/cs.png";s:4:"4db3";s:16:"res/flags/cu.png";s:4:"9d53";s:16:"res/flags/cv.png";s:4:"2f4e";s:16:"res/flags/cx.png";s:4:"8efc";s:16:"res/flags/cy.png";s:4:"f297";s:16:"res/flags/cz.png";s:4:"815b";s:16:"res/flags/de.png";s:4:"ddab";s:16:"res/flags/dj.png";s:4:"197e";s:16:"res/flags/dk.png";s:4:"fe92";s:16:"res/flags/dm.png";s:4:"8584";s:16:"res/flags/do.png";s:4:"1539";s:16:"res/flags/dz.png";s:4:"c57f";s:16:"res/flags/ec.png";s:4:"0152";s:16:"res/flags/ee.png";s:4:"1cdf";s:16:"res/flags/eg.png";s:4:"09c4";s:16:"res/flags/eh.png";s:4:"7dd5";s:16:"res/flags/el.png";s:4:"fd9b";s:16:"res/flags/en.png";s:4:"0894";s:21:"res/flags/england.png";s:4:"73f2";s:16:"res/flags/er.png";s:4:"481d";s:16:"res/flags/es.png";s:4:"d669";s:16:"res/flags/et.png";s:4:"7376";s:16:"res/flags/eu.png";s:4:"ffce";s:27:"res/flags/europeanunion.png";s:4:"ffce";s:16:"res/flags/fa.png";s:4:"2ac0";s:17:"res/flags/fam.png";s:4:"0b36";s:16:"res/flags/fi.png";s:4:"e30b";s:16:"res/flags/fj.png";s:4:"7c3e";s:16:"res/flags/fk.png";s:4:"9627";s:16:"res/flags/fm.png";s:4:"d376";s:16:"res/flags/fo.png";s:4:"d0e6";s:16:"res/flags/fr.png";s:4:"c1cf";s:16:"res/flags/ga.png";s:4:"972d";s:16:"res/flags/gb.png";s:4:"0894";s:16:"res/flags/gd.png";s:4:"95b8";s:16:"res/flags/ge.png";s:4:"aa40";s:16:"res/flags/gf.png";s:4:"c1cf";s:16:"res/flags/gh.png";s:4:"12da";s:16:"res/flags/gi.png";s:4:"0ca5";s:16:"res/flags/gl.png";s:4:"073b";s:16:"res/flags/gm.png";s:4:"a7d7";s:16:"res/flags/gn.png";s:4:"acba";s:16:"res/flags/gp.png";s:4:"c2dc";s:16:"res/flags/gq.png";s:4:"70f6";s:16:"res/flags/gr.png";s:4:"fd9b";s:16:"res/flags/gs.png";s:4:"3b51";s:16:"res/flags/gt.png";s:4:"384e";s:16:"res/flags/gu.png";s:4:"2d05";s:16:"res/flags/gw.png";s:4:"35eb";s:16:"res/flags/gy.png";s:4:"d816";s:16:"res/flags/hk.png";s:4:"389d";s:16:"res/flags/hm.png";s:4:"2fba";s:16:"res/flags/hn.png";s:4:"ac92";s:16:"res/flags/hr.png";s:4:"0868";s:16:"res/flags/ht.png";s:4:"b536";s:16:"res/flags/hu.png";s:4:"6c6f";s:16:"res/flags/id.png";s:4:"fed5";s:16:"res/flags/ie.png";s:4:"48e4";s:16:"res/flags/il.png";s:4:"a135";s:16:"res/flags/in.png";s:4:"50d6";s:16:"res/flags/io.png";s:4:"38af";s:16:"res/flags/iq.png";s:4:"39cf";s:16:"res/flags/ir.png";s:4:"2ac0";s:16:"res/flags/is.png";s:4:"7fff";s:16:"res/flags/it.png";s:4:"784f";s:16:"res/flags/jm.png";s:4:"a582";s:16:"res/flags/jo.png";s:4:"9dd1";s:16:"res/flags/jp.png";s:4:"1095";s:16:"res/flags/ke.png";s:4:"3571";s:16:"res/flags/kg.png";s:4:"1920";s:16:"res/flags/kh.png";s:4:"8658";s:16:"res/flags/ki.png";s:4:"703c";s:16:"res/flags/km.png";s:4:"cc94";s:16:"res/flags/kn.png";s:4:"f096";s:16:"res/flags/ko.png";s:4:"0eaa";s:16:"res/flags/kp.png";s:4:"0eaa";s:16:"res/flags/kr.png";s:4:"cf63";s:16:"res/flags/kw.png";s:4:"2e04";s:16:"res/flags/ky.png";s:4:"da2c";s:16:"res/flags/kz.png";s:4:"6d51";s:16:"res/flags/la.png";s:4:"3375";s:16:"res/flags/lb.png";s:4:"dad5";s:16:"res/flags/lc.png";s:4:"18c0";s:16:"res/flags/li.png";s:4:"8220";s:16:"res/flags/lk.png";s:4:"4e90";s:16:"res/flags/lr.png";s:4:"3b6d";s:16:"res/flags/ls.png";s:4:"c228";s:16:"res/flags/lt.png";s:4:"95ef";s:16:"res/flags/lu.png";s:4:"3be0";s:16:"res/flags/lv.png";s:4:"6ffa";s:16:"res/flags/ly.png";s:4:"3f9d";s:16:"res/flags/ma.png";s:4:"c936";s:16:"res/flags/mc.png";s:4:"63c6";s:16:"res/flags/md.png";s:4:"e414";s:16:"res/flags/me.png";s:4:"7a2e";s:16:"res/flags/mg.png";s:4:"5aea";s:16:"res/flags/mh.png";s:4:"948d";s:16:"res/flags/mk.png";s:4:"6179";s:16:"res/flags/ml.png";s:4:"d951";s:16:"res/flags/mm.png";s:4:"82ad";s:16:"res/flags/mn.png";s:4:"4adb";s:16:"res/flags/mo.png";s:4:"6339";s:16:"res/flags/mp.png";s:4:"929b";s:16:"res/flags/mq.png";s:4:"be5f";s:16:"res/flags/mr.png";s:4:"6c1c";s:16:"res/flags/ms.png";s:4:"bfdd";s:16:"res/flags/mt.png";s:4:"7a7e";s:16:"res/flags/mu.png";s:4:"48d5";s:16:"res/flags/mv.png";s:4:"24c9";s:16:"res/flags/mw.png";s:4:"1d2b";s:16:"res/flags/mx.png";s:4:"479a";s:16:"res/flags/my.png";s:4:"e1c0";s:16:"res/flags/mz.png";s:4:"159c";s:16:"res/flags/na.png";s:4:"e582";s:16:"res/flags/nc.png";s:4:"ad8b";s:16:"res/flags/ne.png";s:4:"f2ec";s:16:"res/flags/nf.png";s:4:"c624";s:16:"res/flags/ng.png";s:4:"0c50";s:16:"res/flags/ni.png";s:4:"f43a";s:16:"res/flags/nl.png";s:4:"6186";s:16:"res/flags/no.png";s:4:"559c";s:16:"res/flags/np.png";s:4:"52c1";s:16:"res/flags/nr.png";s:4:"2fb0";s:16:"res/flags/nu.png";s:4:"9a2f";s:16:"res/flags/nz.png";s:4:"179c";s:16:"res/flags/om.png";s:4:"7b00";s:16:"res/flags/pa.png";s:4:"6479";s:16:"res/flags/pe.png";s:4:"d1ed";s:16:"res/flags/pf.png";s:4:"e59d";s:16:"res/flags/pg.png";s:4:"48f6";s:16:"res/flags/ph.png";s:4:"8ff2";s:16:"res/flags/pk.png";s:4:"3bd1";s:16:"res/flags/pl.png";s:4:"fad0";s:16:"res/flags/pm.png";s:4:"ba41";s:16:"res/flags/pn.png";s:4:"e4dc";s:16:"res/flags/pr.png";s:4:"40b7";s:16:"res/flags/ps.png";s:4:"68d5";s:16:"res/flags/pt.png";s:4:"5b8a";s:16:"res/flags/pw.png";s:4:"f2bf";s:16:"res/flags/py.png";s:4:"b9d3";s:16:"res/flags/qa.png";s:4:"c1dc";s:16:"res/flags/re.png";s:4:"c1cf";s:16:"res/flags/ro.png";s:4:"d038";s:16:"res/flags/rs.png";s:4:"5b67";s:16:"res/flags/ru.png";s:4:"0d31";s:16:"res/flags/rw.png";s:4:"bef9";s:16:"res/flags/sa.png";s:4:"6058";s:16:"res/flags/sb.png";s:4:"5e4b";s:16:"res/flags/sc.png";s:4:"3965";s:22:"res/flags/scotland.png";s:4:"eca5";s:16:"res/flags/sd.png";s:4:"b972";s:16:"res/flags/se.png";s:4:"4c01";s:16:"res/flags/sg.png";s:4:"8af6";s:16:"res/flags/sh.png";s:4:"e707";s:16:"res/flags/si.png";s:4:"7390";s:16:"res/flags/sj.png";s:4:"559c";s:16:"res/flags/sk.png";s:4:"5a7e";s:16:"res/flags/sl.png";s:4:"7390";s:16:"res/flags/sm.png";s:4:"56e3";s:16:"res/flags/sn.png";s:4:"501a";s:16:"res/flags/so.png";s:4:"4be2";s:16:"res/flags/sr.png";s:4:"8f9a";s:16:"res/flags/st.png";s:4:"ab27";s:16:"res/flags/sv.png";s:4:"c6c8";s:16:"res/flags/sy.png";s:4:"a088";s:16:"res/flags/sz.png";s:4:"e976";s:16:"res/flags/tc.png";s:4:"5073";s:16:"res/flags/td.png";s:4:"6c8d";s:16:"res/flags/tf.png";s:4:"f7cc";s:16:"res/flags/tg.png";s:4:"5c62";s:16:"res/flags/th.png";s:4:"af85";s:16:"res/flags/tj.png";s:4:"5cc5";s:16:"res/flags/tk.png";s:4:"896f";s:16:"res/flags/tl.png";s:4:"093e";s:16:"res/flags/tm.png";s:4:"b36c";s:16:"res/flags/tn.png";s:4:"ae99";s:16:"res/flags/to.png";s:4:"ce86";s:16:"res/flags/tr.png";s:4:"31ea";s:16:"res/flags/tt.png";s:4:"9ead";s:16:"res/flags/tv.png";s:4:"6fec";s:16:"res/flags/tw.png";s:4:"0e41";s:16:"res/flags/tz.png";s:4:"c846";s:16:"res/flags/ua.png";s:4:"7ef7";s:16:"res/flags/ug.png";s:4:"17e1";s:16:"res/flags/uk.png";s:4:"73f2";s:16:"res/flags/um.png";s:4:"f0f1";s:16:"res/flags/us.png";s:4:"9685";s:16:"res/flags/uy.png";s:4:"9ca8";s:16:"res/flags/uz.png";s:4:"37e4";s:16:"res/flags/va.png";s:4:"4936";s:16:"res/flags/vc.png";s:4:"60ee";s:16:"res/flags/ve.png";s:4:"3aee";s:16:"res/flags/vg.png";s:4:"79ef";s:16:"res/flags/vi.png";s:4:"c95b";s:16:"res/flags/vn.png";s:4:"6381";s:16:"res/flags/vu.png";s:4:"c37b";s:19:"res/flags/wales.png";s:4:"42c7";s:16:"res/flags/wf.png";s:4:"86cc";s:16:"res/flags/ws.png";s:4:"6818";s:16:"res/flags/ye.png";s:4:"290e";s:16:"res/flags/yt.png";s:4:"f46c";s:16:"res/flags/yu.png";s:4:"081f";s:16:"res/flags/za.png";s:4:"98e1";s:16:"res/flags/zm.png";s:4:"ec69";s:16:"res/flags/zw.png";s:4:"e7ae";}',
);

?>