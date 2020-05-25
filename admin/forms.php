<?php
// 3xpush Script - Push Subscription Management System 
// Copyright 2020 Evgeniy Orel
// Site: https://script.3xpush.com/
// Email: script@3xpush.com
// Telegram: @Evgenfalcon
//
// ======================================================================
// This file is part of 3xpush Script.
//
// 3xpush Script is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// 3xpush Script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with 3xpush Script.  If not, see <https://www.gnu.org/licenses/>.
//======================================================================

if(count(get_included_files()) ==1) exit("Direct access not permitted.");

function text_alert($name, $lang)
{
    $texts = array (
    'MODERATION' => array('ru' => 'Ваши рассылки были проверены модератором', 'en' => 'Your sendings have been reviewed by a moderator')
    );
   
   return $texts[$name][$lang]; 
}
function user_page($admin_id)
{
    return "<a href=\"?m=a_users&admin_id=".$admin_id."\" target=\"_blank\">".$admin_id."</a>";
}

function targets()
{
  $info = array(
      'langs' => array('ru' => 'Языки', 'en' => 'Languages'),
      'sids' => array('ru' => 'Сайты', 'en' => 'Sites')
  );
  
  return $info;
}
function badge($text, $type='success')
{
    return "<span class=\"badge badge-".$type."\">".$text."</span>";
}
 
 
function warn_symb()
{
   return "<i class=\"fa fa-warning red\"></i>";  
}
 
 
function confirm()
{
   return "onclick=\"return confirm('"._COMFIRMACTION."')\"";  
}
 
function admin_filters($filters)
{
    $forms .= "<div class=admin_filters><strong>"._ADMIN_FILTERS."</strong><br>";
   if(is_array($filters)) {
     foreach ($filters as $key => $value) {
         if ($key=='show_all') {
            if ($value==1) $ch = 'checked'; else $ch = '';
            $forms .= "<label>"._ADMIN_SHOW_ALL." <input type=\"checkbox\" name=\"$key\" value=1 ".$ch." /></label>";
         }
          if ($key=='only_my') {
            if ($value==1) $ch = 'checked'; else $ch = '';
            $forms .= "<label>"._ONLYMY." <input type=\"checkbox\" name=\"$key\" value=1 ".$ch." /></label>";
         }
         if ($key=='admin_id') {
            $forms .= "&nbsp;&nbsp;<label>user id <input type=\"text\" size=\"20\" name=\"admin_id\" value=\"".$value."\" /></label>";
         }
         if ($key=='sid2') {
            $forms .= "&nbsp;&nbsp;<label>sid <input type=\"text\" size=\"10\" name=\"$key\" value=\"".$value."\" /></label>";
         }
         if ($key=='subs_from') {
            $forms .= "&nbsp;&nbsp;<label>"._SUBS." <input type=\"text\" size=\"5\" name=\"subs_from\" placeholder=\""._FROM."\" value=\"".$value."\" /></label>";
         }
         if ($key=='subs_to') {
            $forms .= "&nbsp;&nbsp;<label><input type=\"text\" size=\"5\" name=\"subs_to\" placeholder=\""._TILL."\" value=\"".$value."\" /></label>";
         }
         if ($key=='moderate') {
            $sel[$value] = 'selected';
            $forms .= "&nbsp;&nbsp;<label>"._MODERATION."    
               <select name=\"$key\">
                <option value=\"0\" ".$sel[0].">"._EVERY."</option>
                <option value=\"1\" ".$sel[1].">"._MODERATENO."</option>
                <option value=\"2\" ".$sel[2].">"._MODERATEYES."</option>
                <option value=\"3\" ".$sel[3].">"._MODERATEBLOCKED."</option>
                </select></label>";
         }
     }
    
   } 
   $forms .= "</div>"; 
   
   return $forms;
}
function tooltip($text, $position='right')
{
 return "<span tooltip=\"$text\" flow=\"$position\" class=question><i class=\"fa fa-question-circle\"></i></span>";
}

function redir($url) {
    status("<img src=images/loader.gif border=0> Redirecting...", 'info');
	echo "<SCRIPT language=javascript>
		if (top == self) self.location.href = \"".$url."\";
		</SCRIPT>\n";
}

function redirect($url)
{
    echo "<SCRIPT language=javascript>
if (top == self) self.location.href = \"$url\";
</SCRIPT>\n";
}

function modal($title, $text, $type, $id, $action) {
    if (!$type) $type = 1;
    if ($type==1) {$type = "smallmodalLabel"; $type2 = "sm"; }
    if ($type==2) {$type = "mediumModalLabel"; $type2 = "lg"; }
    if ($type==3) {$type = "largeModalLabel"; $type2 = "lg"; }

	echo "<form name=\"form".$id."\" action=\"".$action."\" method=\"post\">
<div class=\"modal fade\" id=\"".$id."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"".$type."\" aria-hidden=\"true\">
                    <div class=\"modal-dialog modal-".$type2."\" role=\"document\">
                        <div class=\"modal-content\">
                            <div class=\"modal-header\">
                                <h5 class=\"modal-title\" id=\"".$type."\">".$title."</h5>
                                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                    <span aria-hidden=\"true\">&times;</span>
                                </button>
                            </div>
                            <div class=\"modal-body\">
                               ".$text."
                            </div>
                            <div class=\"modal-footer\">
                                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">"._CANCEL."</button>
                                <button type=\"submit\" class=\"btn btn-primary\">"._SEND."</button>
                            </div>
                        </div>
                    </div>
                </div></form>\n";
}

function status_small($text, $type='', $cookie=false){
       if (!$type) $type='success';
       $num=0;
       if ($cookie) {
        $onclick = "onclick=\"setCookie('".$cookie."', '1', 'Mon, 01-Jan-2020 00:00:00 GMT', '/'); document.getElementById('statusblock2".$cookie."').style.display = 'none';\"";
       } else {
        $num = rand(1,999);
        $onclick = "onclick=\"document.getElementById('statusblock2".$num."".$cookie."').style.display = 'none';\"";
       }
       
       echo "<div class=\"alert-small with-close alert-small-".$type." alert-dismissible fade show\" role=\"alert\" id=\"statusblock2".$num."".$cookie."\">".$text." <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\" ".$onclick.">
        <span aria-hidden=\"true\">×</span>
       </button></div>\n";
       
       if ($cookie) {
        echo "<script>
if (getCookie('".$cookie."')!=undefined) {
    document.getElementById(\"statusblock2".$cookie."\").style.display = \"none\";
    }
     </script>";
       }
}

function status($text, $type='', $cookie=false){
       if (!$type || $type=='success') {
        $type='success';
        $icon = '<i class="fa fa-check-circle"></i>';
       } elseif ($type=='danger') {
        $icon = '<i class="fa fa-warning"></i>';
       }
       if ($cookie) {
        
        $onclick = "onclick=\"setCookie('".$cookie."', '1', 'Mon, 01-Jan-2025 00:00:00 GMT', '/');\"";
       }
       echo "<div class=\"sufee-alert alert with-close alert-".$type."  alert-dismissible fade show\" id=\"statusblock".$cookie."\" role=\"alert\">".$icon." ".$text." 
       <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\" ".$onclick.">
                                                <span aria-hidden=\"true\">×</span>
                                            </button></div>\n";
    if ($cookie) {
        echo "<script>
if (getCookie('".$cookie."')!=undefined) {
    document.getElementById(\"statusblock".$cookie."\").style.display = \"none\";
    }
     </script>";
       }
}