<?php

ini_set('display_errors', 0);
error_reporting(0);
require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/stat.php");
require_once("../../include/info.php");
require_once("../forms.php");
header('Content-Type: text/html; charset=utf-8');

$check_login = check_login();
if ($check_login==false) {
  exit;
}
$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];
include("../langs/".$lang.".php");

$param = text_filter($_GET['param']);
$params = explode('|', $param);
$id = intval($params[0]);

$site_info=array();

if ($id) {
    if ($check_login['root']==1) {
    $site_info = sites("AND id=$id");
    } else {
    $site_info = sites("AND id=$id AND admin_id=".$check_login['getid']."");    
    }
    $key = $id;
    if ($site_info[$id]['status']==1) $status = 'checked';
    if ($site_info[$id]['partner_api']==1) {
        $partner_api = 'checked';
        if ($check_login['root']!=1) {
            $url_dis = "readonly";
            $partner_info = "<br>(!) "._PARTNERURL;
           }
        }
        if ($site_info[$id]['clickconf']) {
            $click_conf = json_decode($site_info[$id]['clickconf'], true);
            if ($click_conf['no_img']==1) $no_img = 'checked';
        }
         if ($site_info[$id]['cid_filter']) {
            $cid_filter = explode(',', $site_info[$id]['cid_filter']);
            }
        
} else $key=0;

if ($site_info[$id]['comission']==0) $site_info[$id]['comission'] = '';

$cats =sites_category();
                        
                            
  echo "<script type=\"text/javascript\">
                                  function viewblock(id, context) {

                                      if($('#'+id).css('display')=='none') {
                                          $('#'+id).show();

                                          $(context).html('<i class=\"fa fa-tags\"></i> "._MACROS."');
                                      }
                                      else {
                                          $('#'+id).hide();
                                          $(context).html('<i class=\"fa fa-tags\"></i> "._MACROS."');
                                      }
                                }
                                </script> ";
                                
echo "<table><tbody>";
if ($check_login['root']==1) {
echo "<tr><td width=30%>user id</th><td> <input name=\"aadmin_id\" type=\"text\" class=\"form-control longinput\" value=\"".$check_login['getid']."\"></td></tr>";  
    }
if (!$id) {
echo "<tr><td>"._TYPE."</th><td> <input type=\"radio\" name=\"type\" value=\"1\" onclick=\"document.getElementById('url').disabled = false;\" /> "._SITE." &nbsp;&nbsp; <input type=\"radio\" name=\"type\" value=\"2\" onclick=\"document.getElementById('url').disabled = true;\" /> "._LANDING."</td></tr>";
}
echo "<tr><td>"._NAME."</th><td><input name=\"name\" type=\"text\" class=\"form-control longinput\" value=\"".$site_info[$id]['title']."\"></td> </tr>";
if ($site_info[$id]['type']!=2)  {
echo "<tr><td>URL</th><td><input name=\"url\" id=url class=\"form-control longinput\" type=\"text\" placeholder=\"http\" value=\"".$site_info[$id]['url']."\" ".$url_dis.">".$partner_info."</td></tr>";
echo "<tr><td valign=top>"._CATEGORY."</th><td>
<select name=\"category\" class=\"form-control longinput\" id=\"category\">";
foreach ($cats as $keyid => $value) {
    if ($site_info[$id]['category'] && $site_info[$id]['category']==$keyid) $sel = 'selected'; else $sel = '';
    echo "<option value=\"".$keyid."\" ".$sel.">".$value['title'][$lang]."</option>";
    }
echo "</select>   ".tooltip(_SITE_CATEGORY_INFO)." 
 </td></tr>";
    }
echo "<tr><td valign=top>Postback</th><td><input name=\"postback\" class=\"form-control longinput\" id=postback type=\"text\" value=\"".$site_info[$id]['postback']."\"> ".tooltip(_POSTBACKTOOLTIP)."<br />
<a href=\"#\" onclick=\"viewblock('macross".$key."', this); return false;\" class=small><i class=\"fa  fa-tags\"></i> "._MACROS."</a><div id=\"macross".$key."\" style='display: none;'>
                <font class=small2>
                "._POSTBACKINFO." <br /> 
                SID - "._SITE." <br /> 
                UID - "._MACROS10." <br /> 
                IP - "._MACROS2." <br /> 
                UA - "._MACROS12." <br />
                SUBID - "._MACROS4." <br />
                TOKEN - "._MACROS3."<br />
                REFERER - "._REFERERMACROS."<br />
                LOCALE - "._MACROS5."</font>
                </td></tr>";
                
echo "<tr><td valign=top>"._OPTIONS14."</th><td><textarea name=\"stopwords\" cols=\"50\" rows=\"5\" class=\"form-control longinput\">".$site_info[$id]['stopwords']."</textarea><br><span class=small>"._STOP_WORDS_INFO."</span></td></tr>";   
             
if ($check_login['root']==1) {

echo "<tr><td colspan=2><strong>"._ADMIN_CONF."</strong></td></tr>";   
  
echo "<tr><td>"._COMISSION." %</th><td><input name=\"comission\" class=\"form-control longinput\" type=\"text\" value=\"".$site_info[$id]['comission']."\"></td></tr>";     
echo "<tr><td>"._STATUSON."</th><td><input type=\"checkbox\" value=\"1\" name=\"status\" ".$status." /></td></tr>"; 
}
echo "</tbody>
</table><input name=\"edit\" type=\"hidden\" value=\"".$key."\">";
if ($id) {
  echo "<input name=\"type\" type=\"hidden\" value=\"".$site_info[$id]['type']."\">";  
}