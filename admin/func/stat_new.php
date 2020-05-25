<?php
error_reporting(E_WARNING);
ini_set('display_errors', 0);

function group_stat_new($where = '', $type = '')
{
    global $db, $SxGeo;

    $data_arr = array();
    $mbr = "Opera Mini,symbian,iphone,iemobile,pocket,sony,opera mobi,android,blackberry,smartphone,acs,alca,amoi,audi,aste,benq,bird,blac,blaz,brew,cell,cldc,cmd-,dang,doco,eric,hipt,inno,ipaq,java,jigs,kddi,keji,leno,lg-c,lg-d,lg-g,lg-a,lg-b,lg-c,lg-d,lg-f,lg-g,lg-k,lg-l,lg-m,lg-o,lg-p,lg-s,lg-t,lg-u,lg-w,lge-,maui,maxo,midp,mits,mmef,mobi,mot-,moto,mwbp,nec-,newt,noki,opwv,palm,pana,pant,pdxg,phil,play,pluc,prox,qtek,qwap,sage,sams,sany,sch-,sec-,send,seri,sgh-,shar,sie-,siem,smal,smar,sph-,symb,t-mo,teli,tim-,tsm-,upg1,upsi,vk-v,voda,wap-,wapa,wapi,wapp,wapr,webc,winw,xda,xda-,up.browser,up.link,windows ce,mini,mmp,wap,mobile,Android,hiptop,avantgo,plucker,xiino,blazer,elaine,iris,3g_t,vx1000,m800,e860,u940,ux840,compal,wireless,ahong,lg380,lgku,lgu900,lg210,lg47,lg920,lg840,lg370,sam-r,mg50,s55,g83,t66,vx400,mk99,d615,d763,el370,sl900,mp500,samu3,samu4,vx10,xda_,samu5,samu6,samu7,samu9,a615,b832,m881,s920,n210,s700,c-810,_h797,mob-x,sk16d,848b,mowser,s580,r800,471x,v120,rim8,c500foma:,160x,x160,480x,x640,t503,w839,i250,sprint,w398samr810,m5252,c7100,mt126,x225,s5330,s820,htil-g1,fly v71,s302,-x113,novarra,k610i,-three,8325rc,8352rc,sanyo,vx54,c888,nx250,n120,mtk ,c5588,s710,t880,c5005,i;458x,p404i,s210,c5100,teleca,s940,c500,s590,foma,samsu,vx8,vx9,a1000,_mms,myx,a700,gu1100,bc831,e300,ems100,me701,me702m-three,sd588,s800,ac831,mw200 ,d88,htc,htc_touch,355x,m50,km100,d736,p-9521,telco,sl74,ktouch,m4u,me702,phone,lg ,sonyericsson,samsung,240x,x320,vx10,nokia ,motorola,vodafone,o2,treo,1207,3gso,4thp,501i,502i,503i,504i,505i,506i,6310,6590,770s,802s,a wa,acer,airn,asus,attw,au-m,aur ,aus,abac,acoo,aiko,alco,anex,anny,anyw,aptu,arch,argo,bell,bw-n,bw-u,beck,bilb,c55,cdm-,chtm,capi,cond,craw,dall,dbte,dc-s,dica,ds-d,ds12,dait,devi,dmob,dopo,el49,erk0,esl8,ez40,ez60,ez70,ezos,ezze,elai,emul,ezwa,fake,fly-,fly_,g-mo,g1 u,g560,gf-5,grun,gene,go.w,good,grad,hcit,hd-m,hd-p,hd-t,hei-,hp i,hpip,hs-c ,htc-,htca,htcg,htcp,htcs,htct,htc_,haie,hita,huaw,hutc,i-20,i-go,i-ma,i230,iac,iac-,ig01,im1k,iris,jata,kgt,kpt ,kwc-,klon,lexi,lg g,lynx,m1-w,m3ga,mc01,mc21,mcca,medi,meri,mio8,mioa,mo01,mo02,mode,modo,mot ,mt50,mtp1,mtv ,mate,merc,motv,mozz,n100,n101,n102,n202,n203,n300,n302,n500,n502,n505,n700,n701,n710,nem-,newg,neon,netf,nzph,o2 x,o2-x,owg1,opti,oran,p800,pand,pg-1,pg-2,pg-3,pg-6,pg-8,pg-c,pg13,pn-2,pt-g,pire,pock,pose,psio,qa-a,qc-2,qc-3,qc-5,qc-7,qc07,qc12,qc21,qc32,qc60,qci-,qwap,r380,r600,raks,rim9,rove,sc01,scp-,se47,sec-,sec0,sec1,semc,sk-0,sl45,slid,smb3,smt5,sp01,spv ,spv-,sy01,samm,sava,scoo,smit,soft,sony,t218,t250,t600,t610,t618,tcl-,tdg-,telm,ts70,tsm3,tsm5,tx-9,tagt,talk,topl,hiba,up.b,utst,v400,v750,veri,vk40,vk50,vk52,vk53,vm40,vx98,virg,vite,vulc,w3c ,w3c-,wapj,wapu,wapm,wig ,wapv,wapy,waps,wapt,winc,wonu,x700,xda2,xdag,yas-,your,zte-,zeto,avan,brvw,bumb,ccwa,eml2,fetc,http,ibro,idea,ikom,jbro,jemu,kyoc,kyok,libw,m-cr,mywa,nok6,o2im,port,rozo,sama,sec-,sony,tosh,treo,vx52,vx53,vx60,vx61,vx70,vx80,vx81,vx83,vx85,whit,wmlb";
    $mbr = explode(',', $mbr);

    $select = 'browser, ip, referer, subid, del, subs_type, sended, device, brand, model';

    if ($type == 'regions') {
        $select = "cc, browser, ip, referer, subid, del, subs_type, sended";
    }

    if ($type == 'browser' || $type == 'os') {
        $select = "os, browser_short, browser, subid, del, subs_type, sended";
    }

    if ($type == 'devices') {
        $select = "device, ip, referer, subid, del, subs_type, sended";
    }

    if ($type == 'brand') {
        $select = "subid, del, subs_type, sended, brand";
    }

    $sql = "SELECT " . $select . " FROM subscribers WHERE 1 " . $where . "";

    $subscribers = $db->sql_query($sql);
    $subscribers = $db->sql_fetchrowset($subscribers);
    $countsubscribers = count($subscribers);
    if (is_array($subscribers)) {
        foreach ($subscribers as $key => $val) {
            $data_arr['types'][$val['subs_type']]++;
            $data_arr['all']++;
            
            
/*
            $mobile = 0;
            foreach ($mbr as $key2 => $mbrowser) {
                if (preg_match('/' . $mbrowser . '/i', $val['browser']) == 1) {
                    $mobile = 1;
                }
            }
            if ($mobile == 1) {
                $data_arr['mobile']++;
            }
            */

            if ($type == 'regions') {
                if (!$val['cc']) $val['cc'] = 'unknown';
                $data_arr['country'][$val['cc']]['subs']++;
                $data_arr['country'][$val['cc']]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['country']['DEL'][$val['cc']]++;
            }


            if ($type == 'browser') {
                $data_arr['agent'][$val['browser_short']]['subs']++;
                $data_arr['agent'][$val['browser_short']]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['agent']['DEL'][$val['browser_short']]++;
            }

            if ($type == 'os') {
                $data_arr['os'][$val['os']]['subs']++;
                $data_arr['os'][$val['os']]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['os']['DEL'][$val['os']]++;
            }

            if ($type == 'devices') {
                if (!$val['device']) $val['device'] = 'unknown';
                $data_arr['device'][$val['device']]['subs']++;
                $data_arr['device'][$val['device']]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['device']['DEL'][$val['device']]++;
            }

            if ($type == 'brand') {
                if (!$val['brand']) $val['brand'] = 'unknown';
                $brand_full = $val['brand'];//." ".$val['model'];
                $data_arr['brand'][$brand_full]['subs']++;
                $data_arr['brand'][$brand_full]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['brand']['DEL'][$brand_full]++;
            }

            if ($type == 'network') {
                $ipsel = explode(".", $val['ip']);
                $podset = $ipsel[0] . "." . $ipsel[1];

                $data_arr['network'][$podset]['subs']++;
                $data_arr['network'][$podset]['sended'] += $val['sended'];
                if ($val['del']==1) $data_arr['network']['DEL'][$podset]++;
            }

            if (!empty($val['referer'])) {
                $parse = parse_url($val['referer']);
                $domain_ref = $parse['host'];
                $domain_ref = str_replace("www.", "", $domain_ref);
                $data_arr['fromsite'][$domain_ref]++;
                if ($val['del']==1) $data_arr['fromsite']['DEL'][$domain_ref]++;
            } else {
                $data_arr['fromsite']['noref']++;
                if ($val['del']==1) $data_arr['fromsite']['DEL']['noref']++;
            }

            if ($val['del'] == 1) {
                $data_arr['DEL']['country'][$usercountry]++;
                $data_arr['DEL']['agent'][$browsershort]++;
                $data_arr['DEL']['os'][$platform]++;
                $data_arr['DEL']['types'][$val['sposob']]++;
                $data_arr['DEL']['fromsite'][$domain_ref]++;
                $data_arr['DEL']['device'][$val['device']]++;
                $data_arr['DEL']['brand'][$brand_full]++;
                $data_arr['DEL']['network'][$podset]++;
            }

            if (!empty($val['subid'])) {
                $data_arr['sub'][$val['subid']]++;
                if ($val['del']==1) $data_arr['sub']['DEL'][$val['subid']]++;
            }

        }

        if ($type == 'devices') {
            arsort($data_arr['device']);
        }

        if ($type == 'brand') {
            arsort($data_arr['brand']);
        }

        if ($type == 'regions') {
            arsort($data_arr['country']);
        }


        if ($type == 'browser') {
            arsort($data_arr['agent']);
        }

        if ($type == 'os') {
            arsort($data_arr['os']);
        }
        
        if ($type == 'network') {
            arsort($data_arr['network']);
        }

        arsort($data_arr['fromsite']);

        if (!empty($data_arr['subid'])) arsort($data_arr['subid']);
        arsort($data_arr['types']);

        return $data_arr;

    } else return false;

}
