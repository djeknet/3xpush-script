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

?>

 	<div class="page-inner">
<div class="page-header">
						<h4 class="page-title"><?php echo _SITES ?></h4>
					</div>
         <?php

$delete = intval($_POST['delete']);
$edit = intval($_POST['edit']);
$type = intval($_POST['type']);
$addsite = intval($_POST['addsite']);
$partner_api = intval($_POST['partner_api']);  
$status = intval($_POST['status']);  
$comission = intval($_POST['comission']);  
$request_limit = intval($_POST['request_limit']);  
$category = intval($_POST['category']);  

$clickconf = $_POST['clickconf'];
$stopwords = text_filter($_POST['stopwords']);

if ($clickconf) {
    $clickconf = json_encode($clickconf);
}
if ($partner_api==1) {
    $key_arr = api_keys("AND admin_id=".$check_login['getid']."");
    if (!is_array($key_arr)) {
        $rand = rand(1,9999);
$token = sha1(time().$check_login['getid'].$rand);
$keyname = "main";
$db->sql_query('INSERT INTO api_keys (id, admin_id, token, name, status, created)
VALUES (NULL, '.$check_login['getid'].', "'.$token.'", "'.$keyname.'", "1", now())') or $stop = mysqli_error();
status('API ключ создан автоматически, можно получить API URL', 'info');
    }
}
// add site
if ($addsite==1 && $check_login['role']==1) {
$name = text_filter($_POST['name']);
$url = text_filter($_POST['url'], 2);

$postback = text_filter($_POST['postback'], 2);
if ($_POST['cids']) $cids = implode(',', $_POST['cids']);

if ($check_login['root']==1) {
$admin_id = intval($_POST['aadmin_id']);
$save_admin = $admin_id;
} else {
$save_admin = $check_login['getid'];
}
if (!$type) $stop = _SITETYPE; 
if ($type==2) {$url = 'landing';} else {
    $url_arr = parse_url($url);
    if (!$url_arr['host']) {
     $stop .= _SITE_URL_WRONG."<br>";   
    }
}
if ($url && $type==1) {
    $is_url = get_onerow('id', 'sites', "url='".$url."' AND admin_id=".$check_login['getid']."");
}

if ($is_url) $stop .= _IS_SITE."<br>";
if (!$name) $stop .= _ADDSITETEXT1."<br>";
if (!$url) $stop .= _ADDSITETEXT2."<br>";
if (!$stop) {
$db->sql_query('INSERT INTO sites (id, admin_id, date, title,  url, type, postback, partner_api, comission, clickconf, cid_filter, stopwords, category) VALUES (NULL, '.$save_admin.', now(), "'.$name.'", "'.$url.'", "'.$type.'", "'.$postback.'", '.$partner_api.', "'.$comission.'", \''.$clickconf.'\', "'.$cids.'", "'.$stopwords.'", "'.$category.'")') or $error = mysqli_error();
$next_id = $db->sql_nextid();
if ($error) {
jset($check_login['id'], $error, 1);    
status(_OPERATION_ERROR, 'danger');
} else {
     if ($type==2) {
$text = _USER_ADD_LAND;    
    } else {
$text = _ADDSITETEXT3.". "._SITESADD_INFO;
// send admin notification
if ($check_login['root']!=1) {
$super_admins = admins("AND root=1 AND status=1 AND role=1");
foreach ($super_admins as $key => $value) {
alert(_ADDSITETEXT3.": $url (user: ".$check_login['login'].")", $key);
} 
}
}

jset($check_login['id'], $text.": $name"); 
if ($check_login['id']!=$check_login['getid']) {
 alert($text.": $name (user: ".$check_login['login'].")", $check_login['getid']); 
}
                 
status($text, 'success');    

}
} else {
status($stop, 'danger');
}
}
if ($delete && $check_login['role']==1) {
    if ($check_login['root']!=1) {
        $where_del = "AND admin_id=".$check_login['getid']."";
        }
$db->sql_query("DELETE FROM sites WHERE id=".$delete." ".$where_del."") or $error = mysqli_error();
if ($error) {
 jset($check_login['id'], $error, 1);   
status(_OPERATION_ERROR, 'danger');
} else {
jset($check_login['id'], _DELETESITE.": $delete"); 
if ($check_login['id']!=$check_login['getid']) {
 alert(_DELETESITE.": $delete (user: ".$check_login['login'].")", $check_login['getid']); 
}
status(_DELETESITE, 'success');
}
}
// редактирование сайта
if ($edit && $check_login['role']==1) {
$name = text_filter($_POST['name']);
$url = text_filter($_POST['url']);
$postback = text_filter($_POST['postback']);
if ($_POST['cids']) $cids = implode(',', $_POST['cids']);


if ($type==2) $url = 'landing';
if (!$name) $stop = _ADDSITETEXT1;
if (!$url) $stop = _ADDSITETEXT2;
 if (!$stop) {
    if ($check_login['root']!=1) {
        $where_up = "AND admin_id=".$check_login['getid']."";
        }
if ($check_login['root']==1) {
  $comission = "comission=".$comission.", status=".$status.", request_limit='".$request_limit."', "; 
}
$db->sql_query('UPDATE sites SET  '.$comission.' category="'.$category.'", clickconf=\''.$clickconf.'\', partner_api="'.$partner_api.'", postback="'.$postback.'", title="'.$name.'", url="'.$url.'", cid_filter="'.$cids.'", stopwords="'.$stopwords.'" WHERE id='.$edit.' '.$where_up.'') or $error = mysqli_error();

if ($error) {
jset($check_login['id'], $error, 1); 
status(_OPERATION_ERROR, 'danger');
} else {
jset($check_login['id'], _UPDATESITE.": $edit"); 
if ($check_login['id']!=$check_login['getid']) {
 alert(_UPDATESITE.": $edit (user: ".$check_login['login'].")", $check_login['getid']); 
}   
status(_UPDATESITE, 'success');
}
} else {
status($stop, 'danger');
}
 }
?>
  <script type="text/javascript">
                                  function viewblock(id, context) {

                                      console.log(id, context);

                                      if($('#'+id).css('display')=='none') {
                                          $('#'+id).show();

                                          $(context).html('<i class="fa fa-tags"></i> <?php echo _MACROS ?>');
                                      }
                                      else {
                                          $('#'+id).hide();
                                          $(context).html('<i class="fa fa-tags"></i> <?php echo _MACROS ?>');
                                      }
                                }
                                </script>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
                         <div class="addbutton">
                         <button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addsite" onclick="aj('site_form.php','0',1); return false;"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _ADDSITE."/"._LANDING; ?></button>
                          </div>
                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $sid = intval($_GET['sid']);
                             if ($sid==0) $sid='';
                             $advid = intval($_GET['advid']);
                             $name = text_filter($_GET['name']);
                             $url = text_filter($_GET['url']);
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                             if ($check_login['root']==1) {
                             $only_my = intval($_GET['only_my']);
                             $admin_id = text_filter($_GET['admin_id']);
                             $subs_from = intval($_GET['subs_from']);
                             $subs_to = intval($_GET['subs_to']);
                             } else {
                              $only_my=1;
                             }

                                     if ($sid) {
                                     $where .= "AND id='$sid' ";
                                     $dopurl .= "&sid=$sid";
                                     }
                                     if ($name) {
                                     $where .= "AND title LIKE '%$name%' ";
                                     $dopurl .= "&name=$name";
                                     }
                                     if ($url) {
                                     $where .= "AND url LIKE '%$url%'  ";
                                     $dopurl .= "&url=$url";
                                     }
                                     if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     $dopurl .= "&admin_id=$admin_id";
                                     }
                                     if ($subs_from) {
                                     $where .= "AND subscribers >= '$subs_from'  ";
                                     $dopurl .= "&subs_from=$subs_from";
                                     }
                                     if ($subs_to) {
                                     $where .= "AND subscribers <= '$subs_to'  ";
                                     $dopurl .= "&subs_to=$subs_to";
                                     }
                                      if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      }

                                      $filenum = 30;
                                      $offset = ($pagenum - 1) * $filenum;

                                      $sites = sites($where, 'id', "$offset, $filenum");
                                      $domains = domains("AND admin_id=".$check_login['getid']." AND ssl_ready=1");
                                      $domains_form .= "<option value=\"".$settings['domain_link']."\">-</option>";
                                      if (is_array($domains)) {
                                      foreach ($domains as $key => $value) {
                                         $domains_form .= "<option value=\"".$value['domain']."\">".$value['domain']."</option>";
                                        }
                                        }


                                      $all_sites = sites($where, 'id', 0);
                                      if (is_array($all_sites)) {
                                        $all_sites = count($all_sites);
                                      } else $all_sites = 0;
                                      if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'subs_from' => "$subs_from", 'subs_to' => "$subs_to");
                                      echo admin_filters($filters);
                                      $admins = admins();


                                      }

                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-3">№ <input type="text" name="sid" value="<?php echo $sid ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _NAME ?> <input type="text" name="name" value="<?php echo $name ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _LINK ?> <input type="text" name="url" value="<?php echo $url ?>"  class="form-control form-control-sm"></div>
                                </div>

                                 <input name="m" type="hidden" value="sites">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp;&nbsp; <?php echo _ALLFOUND.": ".$all_sites; ?>
                             </form>
                             </div>

                            <div class="card-body">

                                    <?php

                                     if ($sites!=false) {
                                      foreach ($sites as $key => $value) {
                   
                                      	  if ($value['unsubs']) {
                                      	  $active = $value['subscribers'] - $value['unsubs'];
                                      	  $active_proc =  round(($value['unsubs']/$value['subscribers'])*100, 0);
                                      	  $active_proc = 100 - $active_proc;
                                      	  } elseif ($value['subscribers']) {
                                      	  $active = $value['subscribers'];
                                      	  $active_proc = 100;
                                      	  } else {
                                      	  $active = 0;
                                      	  $active_proc = '?';
                                      	  }
                                          if ($active <0) {$active = 0; $active_proc=0;}
                                          
                                      	  if ($value['type']==1) {
                                      	     $value['url'] = str_replace("https://", "", $value['url']);
                                            $value['url'] = str_replace("http://", "", $value['url']);
                                      	  $type = _SITE;
                                          $url = "[<a href=\"//".$value['url']."\" target=_blank>".$value['url']."</a>]";
                                      	  } elseif ($value['type']==2) {
                                      	  $type = _LANDING;
                                         $url = '';
                                            }
                                            
                                                
                                                  if ($value['status']==0) {
                                                $status = "<span class=\"badge badge-danger\" title=\""._STATUSOFF."\">OFF</span>";
                                                } else $status='';
                                                
                                                if ($check_login['root']==1) {
                                                   $login = $admins[$value['admin_id']]['login'];
                                                if ($login==$check_login['login']) $login = "<strong>$login</strong>";
                                                 $user= "&nbsp;&nbsp;<a href=\"?m=a_users&admin_id=".$value['admin_id']."\">#".$value['admin_id']."</a> (".$login.")&nbsp;&nbsp;";
                                         
                                         
                                                    $admin_info=array();
                                                   if ($value['comission']>0) {
                                                $admin_info[] = "Комиссия: -".$value['comission']."%";
                                                } 
                                                
                                                $admin_info = implode("<br>", $admin_info);
                                                $admin_info = "<br> <b>"._ADDED.":</b> ".$value['date']." ".$user." <span class=green>".$admin_info."</span><br />";
                                                }
                                                  
                                          
                                                if ($value['last_subscribe']=='0000-00-00 00:00:00') {
                                                    $value['last_subscribe'] = '??';
                                                }
                                      	 echo "<table class=\"site-table\">
                                    <tbody><tr>
                                            <td width=70% valign=top><span class=\"site-table-title\">#".$key." ".$value['title']." ".$url."</span> ".$status." ".$admin_info."</td>
                                            <td class=\"site-table-right\">
                                            <a href=\"?m=subscribers&sid=".$key."&start_date=".$value['date']."\" target=_blank>"._SUBS.": <span class=\"badge badge-primary\">".$value['subscribers']."</span></a> <br />
                                            <a href=\"?m=subscribers&sid=".$key."&start_date=".$value['date']."&status=1\" target=_blank>"._ACTIVE.": <span class=\"badge badge-success\">".$active." (".$active_proc."%)</span></a> <br />
                                            "._LASTSUB.": ".$value['last_subscribe']."</td>
                                            </tr>
                                            <tr>
                                            <td colspan=2 class=\"site-table-links\">";
                                            if ($value['type']==1) {
                                                
                                            echo "<i class=\"fas fa-code\"></i> <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#code".$key."\">"._GETCODE."</button> ";

                                            } elseif ($value['type']==2) {
                                            echo "<i class=\"fas fa-share-square\"></i> <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#link".$key."\">"._GET_LINK."</button> 
                                             <i class=\"fas fa-cogs\"></i> <a href=?m=landinghtml&id=".$key.">"._LANDHTML."</a> ";
                                            }
                                            echo "<i class=\"fas fa-chart-bar\"></i> <a href=?m=daystat&sid=".$key.">"._STAT."</a> 
                                            <i class=\"fas fa-edit\"></i> <a href=# data-toggle=\"modal\" data-target=\"#editsite\" onclick=\"aj('site_form.php','$key|0',2); return false;\">"._EDIT."</a>
                                            <i class=\"fas fa-trash-alt\"></i> <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#del".$key."\">"._DELETE."</button>
                                            </td>
                                        </tr></tbody>
                                </table>";
                                      }
                                     } else {
                                        status(_SITE_EMPTY, 'info');
                                     }
                                    ?>
                                 

                                <?php
                                $numpages = ceil($all_sites / $filenum);
                                 num_page($all_sites, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
      <?php

         if ($sites!=false) {
             foreach ($sites as $key => $value) {
              modal(_DELETESITE1." ".$value['title'], _DELETESITE2." ".$value['title']."? <input name=\"delete\" type=\"hidden\" value=\"".$key."\">", 1, "del".$key, "?m=sites");
              $form = "<table><tbody>
                                        <tr><td>"._NAME."</th><td><input name=\"name\" type=\"text\" class=\"longinput\" value=\"".$value['title']."\"></td> </tr>";
                                      if ($value['type']!=2)  $form .= "<tr><td>URL</th><td><input name=\"url\" class=\"longinput\" type=\"text\" value=\"".$value['url']."\"></td></tr>";
              $form .= "<tr><td valign=top>Postback</th><td><input name=\"postback\" class=\"longinput\" id=postback type=\"text\" value=\"".$value['postback']."\"> ".tooltip(_POSTBACKTOOLTIP)."<br />
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
              $form .= "</tbody>
                                </table><input name=\"type\" type=\"hidden\" value=\"".$value['type']."\"><input name=\"edit\" type=\"hidden\" value=\"".$key."\">";
             
              $rand = rand(1,99999);
              $form2 = "<div class=\"default-tab\">
                                    <nav>
                                        <div class=\"nav nav-tabs\" id=\"nav-tab-".$key."\" role=\"tablist\">
                                            <a class=\"nav-item nav-link active\" id=\"nav-1-tab-".$key."\" data-toggle=\"tab\" href=\"#nav-1-".$key."\" role=\"tab\" aria-controls=\"nav-1\" aria-selected=\"true\">HTTPS</a>
                                            <a class=\"nav-item nav-link\" id=\"nav-2-tab-".$key."\" data-toggle=\"tab\" href=\"#nav-2-".$key."\" role=\"tab\" aria-controls=\"nav-2\" aria-selected=\"false\">HTTP</a>
                                            <a class=\"nav-item nav-link\" id=\"nav-3-tab-".$key."\" data-toggle=\"tab\" href=\"#nav-3-".$key."\" role=\"tab\" aria-controls=\"nav-3\" aria-selected=\"false\">Block Content</a>
                                            <a class=\"nav-item nav-link\" id=\"nav-4-tab-".$key."\" data-toggle=\"tab\" href=\"#nav-4-".$key."\" role=\"tab\" aria-controls=\"nav-4\" aria-selected=\"false\">Redirect</a>
                                        </div>
                                    </nav>
                                    <div class=\"tab-content bluelinks pl-3 pt-2\" id=\"nav-tabContent-".$key."\">
                                        <div class=\"tab-pane fade show active\" id=\"nav-1-".$key."\" role=\"tabpanel\" aria-labelledby=\"nav-1-".$key."-tab\">
                                        <p>"._CODETEXT8."</p>
                                        <div class=\"sufee-alert alert with-close alert-info  alert-dismissible fade show\" id=\"statusblock\" role=\"alert\"><i class=\"fa fa-external-link\"></i> <a href=https://".$rand.".".$settings['siteurl']."/push.html target=_blank>"._SEE_EXAMPLE."</a></div>
                                        <textarea readonly='false' class='code-text'>
                                         &lt;script&gt;
                                          psx_host = '".$settings['domain_code']."';
                                          psx_site_id = '".$key."';
                                          psx_sub_id = ''; // "._CODETEXT1."
                                          psx_tag = ''; // "._CODETEXT25."
                                          psx_time = ''; // "._CODETEXT3."
                                          blocksite = 0; // "._CODETEXT4."
                                          hasBlockCross = 1; // "._CODETEXT5."
                                          blockText = ''; // "._CODETEXT6."
                                          redirect_url = ''; // "._CODETEXT30."
                                          block_url = ''; // "._CODETEXT31."
                                          (function(d){let s=d.createElement('script');s.async=true;s.src='https://'+psx_host+'/new.js';d.head.appendChild(s);})(document);
                                         &lt;/script&gt;
                                        </textarea>
                                       <br /><br />
                                       <p>"._CODETEXT7." - <a href=http://".$settings['domain_code']."/firebase-messaging-sw.js target=_blank>http://".$settings['domain_code']."/firebase-messaging-sw.js</a></p>
                                        </div>
                                        <div class=\"tab-pane fade\" id=\"nav-2-".$key."\" role=\"tabpanel\" aria-labelledby=\"nav-2-".$key."-tab\">
                                       <p>"._CODETEXT9."</p>
                                       <div class=\"sufee-alert alert with-close alert-info  alert-dismissible fade show\" id=\"statusblock\" role=\"alert\"><i class=\"fa fa-external-link\"></i> <a href=https://".$rand.".".$settings['siteurl']."/push_http.html target=_blank>"._SEE_EXAMPLE."</a></div>
                                        <textarea readonly='false' class='code-text' style='height: 210px'>
                                         &lt;script&gt;
                                         'use strict';
                                         let psx_host = '".$settings['domain_code']."',
                                         s = document.createElement('script');
                                         s.src=\"https://\"+psx_host+\"/up/push-popup.js\";
                                         document.getElementsByTagName('head')[0].appendChild(s);
                                         let psx_site_id = '".$key."',
                                         psx_sub_id = '', // "._CODETEXT1."
                                         psx_tag = ''; // "._CODETEXT25."
                                         &lt;/script&gt;
                                        </textarea> <br />
                                        <p>"._CODETEXT10."</p>
                                       <textarea readonly='false' class='code-text' style='height: 200px'>
                                        &lt;div class=&quot;push-popup&quot;&gt;
                                        &lt;div class=&quot;push-popup__subscribe active&quot;&gt;
                                        &lt;div class=&quot;push-popup__text&quot;&gt;"._CODETEXT11."&lt;/div&gt;
                                        &lt;div class=&quot;push-popup__subscribe-bottom&quot;&gt;
                                        &lt;button type=&quot;button&quot; class=&quot;push-popup__subscribe-btn&quot;&gt;"._CODETEXT12."&lt;/button&gt;
                                        &lt;span class=&quot;push-popup__subscribe-separator&quot;&gt;или&lt;/span&gt; &lt;span class=&quot;push-popup__subscribe-cancel&quot;&gt;"._CODETEXT13."&lt;/span&gt;
                                        &lt;/div&gt;&lt;/div&gt;&lt;/div&gt;
                                       </textarea>
                                        </div>
                                        <div class=\"tab-pane fade\" id=\"nav-3-".$key."\" role=\"tabpanel\" aria-labelledby=\"nav-3-".$key."-tab\">
                                            <p>"._CODETEXT14."</p>
                                            <div class=\"sufee-alert alert with-close alert-info  alert-dismissible fade show\" id=\"statusblock\" role=\"alert\"><i class=\"fa fa-external-link\"></i> <a href=https://".$rand.".".$settings['siteurl']."/closing-content.html target=_blank>"._SEE_EXAMPLE."</a></div>
                                            <textarea readonly='false' class='code-text'>
                                            &lt;script&gt;
                                            'use strict';
                                            let psx_host = '".$settings['domain_code']."',
                                            s = document.createElement('script'),
                                            fb = document.createElement('script');
                                            fb.src=\"https://www.gstatic.com/firebasejs/5.2.0/firebase.js\";
                                            document.getElementsByTagName('head')[0].appendChild(fb);
                                            s.src=\"https://\"+psx_host+\"/closing-content.js\";
                                            document.getElementsByTagName('head')[0].appendChild(s);
                                            let psx_site_id = '".$key."',
                                            psx_sub_id = '', // "._CODETEXT1."
                                            psx_tag = ''; // "._CODETEXT25."
                                            &lt;/script&gt;
                                            </textarea><br />
                                            <p>"._CODETEXT15."</p>
                                            <textarea readonly='false' class='code-text' style='height: 190px'>
                                            &lt;div class=&quot;closing-content&quot;&gt;

                                            -- "._CODETEXT16." --

                                            &lt;div class=&quot;closing-content__overlay&quot;&gt;
                                            &lt;span class=&quot;closing-content__text&quot;&gt;"._CODETEXT17."
                                            &lt;span class=&quot;closing-content__subscribe&quot;&gt;"._CODETEXT12."&lt;/span&gt;
                                            &lt;/span&gt;
                                            &lt;/div&gt;
                                            &lt;/div&gt;
                                            </textarea>
                                            <p>"._CODETEXT7." - <a href=http://".$settings['domain_code']."/firebase-messaging-sw.js target=_blank>http://".$settings['domain_code']."/firebase-messaging-sw.js</a></p>
                                        </div>
                                           <div class=\"tab-pane fade\" id=\"nav-4-".$key."\" role=\"tabpanel\" aria-labelledby=\"nav-4-".$key."-tab\">
                                            <p>"._CODETEXT18."</p>
                                            <div class=\"sufee-alert alert with-close alert-info  alert-dismissible fade show\" id=\"statusblock\" role=\"alert\"><i class=\"fa fa-external-link\"></i> <a href=https://".$rand.".".$settings['siteurl']."/links.html target=_blank>"._SEE_EXAMPLE."</a></div>
                                            <textarea readonly='false' class='code-text'>
                                            &lt;script&gt;
                                            'use strict';
                                            let psx_host = '".$settings['domain_code']."',
                                            s = document.createElement('script');
                                            s.src=\"https://\"+psx_host+\"/links.js\";
                                            document.getElementsByTagName('head')[0].appendChild(s);

                                            let site_id = '".$key."',
                                            sub_id = '', // "._CODETEXT1."
                                            tag = '', // "._CODETEXT25."
                                            repeat = '0', // "._CODETEXT19."
                                            fon ='', // "._CODETEXT20."
                                            text ='', // "._CODETEXT21."
                                            type = 0, // "._CODETEXT22."
                                            filename = ''; // "._FILENAME."
                                            &lt;/script&gt;
                                            </textarea><br />
                                            <p>"._CODETEXT23."</p>
                                            <p>"._CODETEXT24."</p>
                                        </div>
                                    </div>
                                </div>";
                             
              modal(_GETCODE2." ".$value['title'], $form2, 3, "code".$key, "?m=sites");
if ($next_id && $value['type']==1) {
echo "<script>
$(\"#code".$next_id."\").modal(\"show\");
</script>";
}
               
                    
                     
                 $links = land_links($key);

$form3 = "<div class=block-info>"._GET_LINK_1."<br>
<select size=\"1\" name=\"domain\" class=linkselect>".$domains_form."</select> <input data-url=\"".$links['landing']."\" name=\"Name\" class=linkinput style='width: 75%' onFocus=\"this.select()\" onClick=\"this.select()\" type=\"text\" value=\"".$settings['domain_link']."".$links['landing']."\"> <a class='linkUrlOpen' href=\"//".$settings['domain_link']."".$links['landing']."\" target=_blank><i class=\"fa fa-external-link\"></i></a></div>";

$form3 .= "<div class=block-info>"._GET_LINK_2."<br>
<select size=\"1\" name=\"domain\" class=linkselect>".$domains_form."</select> <input data-url=\"".$links['iframe']."\" name=\"Name\"  class=linkinput style='width: 75%' onFocus=\"this.select()\" onClick=\"this.select()\" type=\"text\" value=\"".$settings['domain_link']."".$links['iframe']."\"> <a class='linkUrlOpen' href=\"//".$settings['domain_link']."".$links['iframe']."\" target=_blank><i class=\"fa fa-external-link\"></i></a></div>";

$form3 .= "<div class=block-info>"._GET_LINK_3."<br>
<select size=\"1\" name=\"domain\" class=linkselect>".$domains_form."</select> <input data-url=\"".$links['link']."\" name=\"Name\"  class=linkinput style='width: 75%' onFocus=\"this.select()\" onClick=\"this.select()\" type=\"text\" value=\"".$settings['domain_link']."".$links['link']."\"> <a class='linkUrlOpen' href=\"//".$settings['domain_link']."".$links['link']."\" target=_blank><i class=\"fa fa-external-link\"></i></a></div>";

$form3 .= "<br>"._GET_LINK_INFO."";
               
                 modal(_GET_LINK." ".$value['title'], $form3, 3, "link".$key, "?m=sites");
if ($next_id && $value['type']==2) {

}               
              }
              }

      ?>
      <script>

	function selectText(elementId) {

		var doc = document,
		text = doc.getElementById(elementId), range, selection;

		if(doc.body.createTextRange) {

			range = document.body.createTextRange();
			range.moveToElementText(text);
			range.select();

		} else if (window.getSelection) {

			selection = window.getSelection();
			range = document.createRange();
			range.selectNodeContents(text);
			selection.removeAllRanges();
			selection.addRange(range);

		}

	}

</script>

<script>
    $(".tab-pane .code-text").each(function (k, item) {
        var val = $(this).val();
        val = val.replace(/\s{2,}/g, '\n');
        val = val.trim();
        $(this).val(val);
        $(this).click(function () {
            $(this).select();
        });
    });
</script>


      <div class="modal fade" id="addsite" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _ADDSITE; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=sites" method="post">
                          <div id="block-1"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="addsite" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                </div>
      <div class="modal fade" id="editsite" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _EDITSITE; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=sites" method="post">

                           <div id="block-2">...</div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="editsite" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                 </div>
                 

<script>
    $(document).ready(function () {
        $('select[name="domain"]').change(function () {
            var value = $(this).val();
            $('.linkinput').each(function () {
                var url = value + $(this).data('url');
                $(this).val(url);

                $(this).next().attr('href', '//' + url);
            });
        });
    })
</script>
