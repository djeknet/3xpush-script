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

if ($settings['sending_on']!=1) {
    status(_MODULE_OFF, 'danger');
    exit;
}
?>
<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _FAST_SEND ?></h4>
</div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php
$way_block = array('001' => _WAYBLOCK_001, '002' => _WAYBLOCK_002, '003' => _WAYBLOCK_003, '004' => _WAYBLOCK_004, '005' => _WAYBLOCK_005, '007' => _WAYBLOCK_007, '008' => _WAYBLOCK_008);
                           
$save = isset($_POST['save']) && (int) $_POST['save'];
$double = intval($_GET['edit']);
$moderate = intval($_POST['moderate_change']);
$delete = intval($_POST['delete']);
$ids = $_POST['ids'];
if ($ids) $ids = implode(',', $ids);
$off = intval($_GET['off']);
$new = intval($_GET['new']);
$id = intval($_GET['id']);
$double = intval($_GET['double']);
if ($double) $double = $double;
if ($check_login['root']!=1) {
    $where_admin = "AND admin_id=".$check_login['getid']."";
    }  
// ad status setting - deleted
if ($delete && $check_login['role']==1) {

    $db->sql_query("UPDATE myads SET status=2, last_edit=now() WHERE id='".$delete."' ".$where_admin."") or $error = mysql_error();
    if (!$error) $db->sql_query("DELETE FROM send_loop WHERE myads_id=".$delete."");  
    
    jset($check_login['id'], _ADS_DELETED.": ".$delete."");

    if ($error) {
         jset($check_login['id'], $error, 1);
        status (_OPERATION_ERROR, 'danger');
    } else {
        status(_ADS_DELETED, 'success');
    }
}


// status change
if ($check_login['role']==1) {
    if ($off==1 && $id) {
        $db->sql_query("UPDATE myads SET status=1, last_edit=now() WHERE id='".$id."' ".$where_admin."");
        jset($check_login['id'], _CHANGESTATUS." #$id - ON");
        status("#$id - "._CHANGESTATUS, 'success');
    } elseif ($off==2 && $id) {
        $db->sql_query("UPDATE myads SET status=0, last_edit=now() WHERE id='".$id."' ".$where_admin."");
        jset($check_login['id'], _CHANGESTATUS." #$id - OFF");
        status("#$id - "._CHANGESTATUS, 'success');
    }
}

// moderation
if ($moderate && $check_login['role']==1 && $check_login['root']==1) {
if (!empty($_POST['way_block'])) {$way_block2 = implode(',' , $_POST['way_block']); $way_block2 = text_filter($way_block2);} else $way_block2='';

    $db->sql_query("UPDATE myads SET moderate='$moderate', way_block='$way_block2', last_edit=now() WHERE id IN (".$ids.")");
    jset($check_login['id'], _SEND_MODERATESTATUS);
    if ($check_login['id']!=$check_login['getid']) {
     alert(_SEND_MODERATESTATUS." $moderate: $ids (user: ".$check_login['login'].")", $check_login['getid']);
    }

    $admins_alerts = myads("AND id IN (".$ids.")");
    foreach ($admins_alerts as $key => $value) {
     $admins[$value['admin_id']] = $value['admin_id'];
    }
    foreach ($admins as $key => $value) {
    $lang = admin_lang($key);    
    $text_alert = text_alert('MODERATION', $lang);
       alert($text_alert, $key, 'info');
    }
    status(_SEND_MODERATESTATUS, 'success');

}


// save
if ($save==1 && $check_login['role']==1) {
    
$double_save = intval($_POST['double_save']);
$copy_icon = text_filter($_POST['copy_icon'], 2);
$copy_image = text_filter($_POST['copy_image'], 2);
$status = $form['status'] = intval($_POST['status']);
$title = $form['title'] = text_filter($_POST['title']);
$text = $form['text'] = text_filter($_POST['text']);
$url = $form['url'] = text_filter($_POST['url'], 2);  
$subsid = $form['subsid'] = text_filter($_POST['subsid']);  
$loop_send = $form['loop_send'] = intval($_POST['loop_send']);

$send_time = text_filter($_POST['send_time']);  
if ($send_time) {
    $form['send_time'] = $send_time;
    $send_time = converToTz($send_time, $config['proj_timezone'], $settings['timezone']);
    }
if ($subsid) {
   $_POST['tags']='';
   $_POST['langs']='';
   $_POST['sids']='';
   $_POST['regions']=''; 
}  
if (!empty($_POST['tags'])) {$save_tags = implode(',' , $_POST['tags']); $save_tags = text_filter($save_tags); $form['tags'] = explode(',',$save_tags); }
if (!empty($_POST['langs'])) {$langs = implode(',' , $_POST['langs']); $langs = text_filter($langs); $form['langs'] = explode(',',$langs); }
if (!empty($_POST['sids'])) {$sids = implode(',' , $_POST['sids']); $sids = text_filter($sids); $form['sids'] = explode(',',$sids);}
if (!empty($_POST['regions'])) {
        
        $regions = implode(',' , $_POST['regions']); 
        $regions = text_filter($regions); 
        $form['regions'] = explode(',',$regions);
        
        } 
if (!empty($_POST['options'])){
        foreach ($_POST['options'] as $key => $value) {
            $options[$key] = text_filter($value);
        }
        $options_save = json_encode($options, JSON_UNESCAPED_UNICODE);
    }  
  
if (intval($_FILES['img']['size'])) $img_check=1;
if (intval($_FILES['img2']['size'])) $img_check=1;
  
 if (!$title) $stop .= _MYADV4."<br />";
 if (!$text) $stop .= _MYADV5."<br />";
 if (!$url) $stop .= _NEED_URL."<br />";
 
 $check_deny = admins("AND id=".$check_login['getid']."");

 if ($check_deny[$check_login['getid']]['deny_sending']==1)  $stop .= _DENY_SENDING."<br />";
 
    $directory = "../pushimg/".$check_login['getid'];
    if ($copy_icon) {
        if (!$double_save) $copy_icon = "..".$copy_icon; //condition for images from myads, where the address is already there .. first
        $type = explode(".", $copy_icon);
        $type = strtolower(end($type));
        $filename = gen_pass(30).".".$type;
        $images['icon'] = "".$directory."/".$filename."";
        if(!copy($copy_icon, $images['icon'])) $stop = _COPYERROR;
        if ($check_login['root']==1 && $stop) {
            $stop .= "<br>copy_icon: $copy_icon, icon: ".$images['icon'];
        }
    }
    if ($copy_image) {
        if (!$double_save)  $copy_image = "..".$copy_image;
        $type = explode(".", $copy_image);
        $type = strtolower(end($type));
        $filename = gen_pass(30).".".$type;
        $images['image'] = "".$directory."/".$filename."";
        if(!copy($copy_image, $images['image'])) $stop = _COPYERROR;
        if ($check_login['root']==1 && $stop) {
            $stop .= "<br>copy_image: $copy_image, image: ".$images['image'];
        }
    }
    
    if ($img_check==1) {
    $images = load_img($check_login['getid']);

     } elseif(!$images['icon']) {
        $stop .= _MYADV6."<br />";
     }
     
    if ($images['icon']) $newimg = "icon='".$images['icon']."', "; 
    if ($images['image']) $newimg .= "image='".$images['image']."', "; 
    
 $where_arr['admin_id'] = $check_login['getid'];   
 $where_arr['exchange']=1;
 
 $subscribers_list = check_subscribers($_POST, $where_arr, 1);

 if ($subscribers_list==false) $stop .= _SEND_FAIL."<br />"; else {
 $subscribers = count($subscribers_list);   
 }


  if (!$stop) {
    
    $moderate = check_moderate($url);

                $db->sql_query("INSERT INTO myads (id, admin_id, date, time, title, text, icon, image, url, regions, status,  langs, tags, options, send_time, subscribers, sids, subsid, loop_send, moderate) VALUES
                (NULL, ".$check_login['getid'].", now(), now(), '$title', '$text',  '".$images['icon']."', '".$images['image']."', '$url', '$regions', '$status', '$langs', '$save_tags', '$options_save', '$send_time', '$subscribers', '$sids', '$subsid', '$loop_send', '$moderate')")  or $error = mysqli_error();
                 $new_id = $db->sql_nextid();
                 
                 if ($loop_send==1) {
                    foreach ($subscribers_list as $key => $value) {
                    $db->sql_query("INSERT INTO send_loop (id, subs_id, myads_id) VALUES (NULL, '".$value['id']."', '".$new_id."')")  or $error = mysqli_error();
                    }
                 }

                if ($error) {
                 jset($check_login['id'], $error, 1);
                status (_OPERATION_ERROR, 'danger');
                } else {
                
                jset($check_login['id'], _SEND_ADDED.": ".$new_id."");
                if ($check_login['id']!=$check_login['getid']) {
                   alert(_SEND_ADDED.": $new_id (user: ".$check_login['login'].")", $check_login['getid']);
                }
                // send admin notification
                if ($check_login['root']!=1) {
                $super_admins = admins("AND root=1 AND status=1 AND role=1");
                foreach ($super_admins as $key => $value) {
                alert(_SEND_ADDED.": $new_id (user: ".$check_login['login'].")", $key);
                }
                }
                status (_SEND_ADDED, 'success');
                 redirect('?m=my_send');
                }


            
  } else {

    status($stop, 'danger', 1);
  }       
    }                       $text = text_filter($_GET['text']);
                            $limit  = intval($_GET['limit']);
                            $search_text = text_filter($_GET['text']);
                            $search_url = text_filter($_GET['url']);
                            $regions = $_GET['search_regions'];
                            $pagenum  = intval($_GET['page']);
                            if (!$pagenum) $pagenum = 1;
                            $moderate  = intval($_REQUEST['moderate']);
                            $sorts  = intval($_GET['sorts']);
                            $viewstatus  = intval($_GET['viewstatus']);
                            $viewid = intval($_GET['viewid']);
                            if ($viewid==0) $viewid = '';
                            
                             if (!$limit) $limit = 30; else {
                                $dopurl .= "&limit=$limit";
                             }
                            $limitsel[$limit] = 'selected';
                            $filenum = $limit;
                            $offset = ($pagenum - 1) * $filenum;
  
                             

                             if ($search_text) {
                                $where .= "AND (title LIKE '%$search_text%' OR text LIKE '%$search_text%')";
                                $dopurl .= "&text=$search_text";
                            }
                            if ($search_url) {
                                $where .= "AND url LIKE '%$search_url%'  ";
                                $dopurl .= "&url=$search_url";
                            }
                            if ($viewstatus) {
                                $selstatus[$viewstatus] = 'selected';
                                $dopurl .= "&viewstatus=$viewstatus";
                                if ($viewstatus==2) $viewstatus=0;
                                if ($viewstatus==3) $viewstatus=2;
                                $where .= "AND status='$viewstatus'  ";

                            }
                            if ($viewid) {
                                $where .= "AND id='$viewid' ";
                            }
                            if ($regions) {
                                        foreach ($regions as $key => $val) {
                                            $regions[] = text_filter($val);
                                            $dopurl .= "&regions[]=$val";
                                        }
                                        $regions = array_unique($regions);
                                $where .= wherelike('regions', $regions);
                            }
                            if ($moderate) {
                                if ($moderate==1) $where .= "AND moderate='0' AND status!=2";
                                if ($moderate==2) $where .= "AND moderate='1' ";
                                if ($moderate==3) $where .= "AND moderate='2' ";
                                $dopurl .= "&moderate=$moderate";
                            }
                            if ($check_login['root']==1) {
                                $only_my = intval($_GET['only_my']);
                                $admin_id = text_filter($_GET['admin_id']);
                            } else {
                                $only_my=1;
                                $where .= "AND status!='2' ";
                            }
                            if ($admin_id) {
                                $where .= "AND admin_id='$admin_id'  ";
                                $dopurl .= "&admin_id=$admin_id";
                            } 
                            
                            if ($only_my==1) {
                               $where .= "AND admin_id=".$check_login['getid']." ";
                               }
                                   $sort = "id";
                            if ($sorts==2) {
                                $sort = "views";
                            } elseif ($sorts==3) {
                                $sort = "clicks";
                            }
                            $dopurl .= "&sorts=".$sorts."";
                            $sortssel[$sorts] = 'selected';
                          
                       
                               $myads = myads($where, $sort, "$offset, $filenum");
                               
                            
                               $all_ads = myads("AND ".$where);
                               if (is_array($all_ads)) {
                               $all_ads = count($all_ads);} else $all_ads = 0;
                               $targets['tag'] = array();
                               $subscribers = subscribers("AND admin_id=".$check_login['getid']."", '', 9999999);
                               if (is_array($subscribers)) {
                                   foreach ($subscribers as $key => $value) {
                                     $targets['tag'][$value['tag']] = $value['tag'];
                                   }
                               }       
                            $isolist = isolist();
                            $langslist = langslist();
                            $landings = landings();
                            
                            
                            
                            $title_form = _CREATE_SEND;
                            $icon = 'plus';
                            $statussel[1] = "checked";
                           
                            if ($double) {

                             $edit = myads("AND id=".$double." ".$where_admin.""); 
                            
                                $title_form = _SEND_COPY." #".$double;
                                $icon_hide = "<input type=\"hidden\" name=\"copy_icon\" value=\"".$edit[$double]['icon']."\" />";
                                if ($edit[$double]['image']) {
                                $image_hide = "<input type=\"hidden\" name=\"copy_image\" value=\"".$edit[$double]['image']."\" />";
                                }
                          
                             $status = $edit[$double]['status'];
                             $statussel[$status] = "checked"; 
                             if (!$form['loop_send'] && $edit[$double]['loop_send']) $form['loop_send'] = $edit[$double]['loop_send']; 
                             if (!$form['subsid'] && $edit[$double]['subsid']) $form['subsid'] = $edit[$double]['subsid']; 
                             if (!$form['title'] && $edit[$double]['title']) $form['title'] = $edit[$double]['title']; 
                             if (!$form['text'] && $edit[$double]['text']) $form['text'] = $edit[$double]['text']; 
                             if (!$form['url'] && $edit[$double]['url']) $form['url'] = $edit[$double]['url']; 
                             if (!$form['langs'] && $edit[$double]['langs']) $form['langs'] = explode(",", $edit[$double]['langs']); 
                             if (!$form['tags'] && $edit[$double]['tags']) $form['tags'] = explode(",", $edit[$double]['tags']);
                             if (!$form['regions'] && $edit[$double]['regions']) $form['regions'] = explode(",",$edit[$double]['regions']);
                             if (!$form['send_time'] && $edit[$double]['send_time']) {
                                $form['send_time'] = $edit[$double]['send_time'];
                                $form['send_time'] = converToTz($form['send_time'], $settings['timezone'], $config['proj_timezone']); 
                             }
                             if (!$form['sids'] && $edit[$double]['sids']) $form['sids'] = explode(",",$edit[$double]['sids']);      
                             if (!$options && $edit[$double]['options']) { $options = json_decode($edit[$double]['options'], true);} else $options = array();     
                           
                            }
                            if ($_GET['subsid']) {
                              $form['subsid'] = text_filter($_GET['subsid']);  
                            }
                            if (!$form['send_time']) $form['send_time'] = date("Y-m-d H:i");
                            
                            if ($form['loop_send']==1) $loop_send_check = 'checked="true"';
                            
                            $sites = sites($where_admin);
                            $sites_all = sites("AND type=1");
                            $exch_sites = traf_exchange_admins("AND admin_id=".$check_login['getid']." AND status=1");  
                            
                              if ($check_login['root']==1) {
                                $admin_filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'moderate' => "$moderate");
                                echo admin_filters($admin_filters);
                                $admins = admins();
                            }
                            
                                     ?>
                             <?php echo status(_MY_SEND_INFO, 'info', 'my_send'); ?>         
                             <div class="row form-group">
                            
                                <div class="col col-lg-2"><?php echo _ADV ?> № <input type="text" name="viewid" value="<?php echo $viewid ?>"  class="form-control form-control-sm"></div>
                                <div class="col col-md-3"><?php echo _TEXT ?> <input type="text" name="text" value="<?php echo $search_text ?>"  class="form-control form-control-sm"></div>
                                <div class="col col-md-3"><?php echo _LINK ?> <input type="text" name="url" value="<?php echo $search_url ?>"  class="form-control form-control-sm"></div>
                                <div class="col col-md-3"><?php echo _COUNTRYS ?>
                                   <select data-placeholder="<?php echo _CHOSE2; ?>" multiple class="standardSelect4" name="search_regions[]" id="regions">
                                     <option value=""></option>
                                        <?php

                                        foreach ($isolist as $key => $arr) {

                                        if ($regions && in_array($arr['iso'], $regions)) $sel ='selected'; else $sel='';
                                    echo "<option value=\"".$arr['iso']."\" ".$sel.">".$arr[$lang]." [".$arr['iso']."]</option>";

                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="col col-lg-2 inlineblock"><?php echo _STATUS ?><br />
                                    <select name="viewstatus" class="form-control-sm form-control col col-md-8">
                                        <option value="0"><?php echo _EVERY ?></option>
                                        <option value="1" <?php echo $selstatus[1] ?>><?php echo _ACTIVE2 ?></option>
                                        <option value="2" <?php echo $selstatus[2] ?>><?php echo _STATUSOFF ?></option>
                                        <?php
                                        if ($check_login['root']==1) {
                                         echo "<option value=\"3\" ".$selstatus[3].">"._STATUSDEL."</option>";
                                            }
                                        ?>
                                    </select></div>
                               <div class="col col-lg-2 inlineblock"><?php echo _SORTS ?><br />
                                    <select name="sorts" class="form-control-sm form-control col col-md-8">
                                        <option value="1" <?php echo $sortssel[1] ?>>id</option>
                                        <option value="2" <?php echo $sortssel[2] ?>><?php echo _VIEWS ?></option>
                                        <option value="3" <?php echo $sortssel[3] ?>><?php echo _CLICKS ?></option>
                                    </select>
                                </div>    
                              <div class="col col-md-2 inlineblock"><?php echo _LINES ?><br />
                                    <select name="limit" class="form-control-sm form-control col col-md-8">
                                        <option value="30" <?php echo $limitsel[30] ?>>30</option>
                                        <option value="50" <?php echo $limitsel[50] ?>>50</option>
                                        <option value="80" <?php echo $limitsel[80] ?>>80</option>
                                        <option value="100" <?php echo $limitsel[100] ?>>100</option>
                                    </select>
                                </div>    
                                </div>
                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>    
                             </form>
                             </div>

                            <div class="card-body">
                            
<a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form ?></a>
  <form action="?m=my_send" method="post" id=adsform  enctype="multipart/form-data"<?php echo !$double && !$new ? ' style="display: none"' : ''?>>
<table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                          <tr>
                                    <td width="20%" valign=top><strong><?php echo _TITLE; ?></strong></td>
                                    <td><input name="title" type="text" required value="<?php echo $form['title']; ?>"  maxlength="30" class=longinput> <li class="far fa-smile smile-button"></li>
                                    <br />
                                        <a href="#" onclick="viewblock('macross', this); return false;" class=small><i class="fa  fa-tags"></i> <?php echo _MACROS; ?></a>
                                        <div id="macross" style='display: none;'>
                                            <font class=small2>
                                                <div class="macros_link"><span>[COUNTRY]</span> - <?php echo _COUNTRY; ?></div>
                                                <div class="macros_link"><span>[REGION]</span> - <?php echo _REGION; ?></div>
                                                <div class="macros_link"><span>[CITY]</span> - <?php echo _CITY; ?></div>
                                                <div class="macros_link"><span>[BRAND]</span> - <?php echo _MOBDEV; ?></div>
                                                <div class="macros_link"><span>[MODEL]</span> - <?php echo _MODEL; ?></div>
                                                <div class="macros_link"><span>[TAG]</span> - <?php echo _MACROS_TAG; ?></div>
                                            </font>
                                        </div>
                                        </td>
                                </tr>
                                <tr>
                                    <td  valign=top><strong><?php echo _DESCR; ?></strong></td>
                                    <td><textarea name="text" cols="50" required rows="5" maxlength="120" class=longinput><?php echo $form['text'] ?></textarea> <li class="far fa-smile smile-button"></li>
                                        <br />
                                        <a href="#" onclick="viewblock('macross3', this); return false;" class=small><i class="fa  fa-tags"></i> <?php echo _MACROS; ?></a>
                                        <div id="macross3" style='display: none;'>
                                            <font class=small2>
                                                <div class="macros_link"><span>[COUNTRY]</span> - <?php echo _COUNTRY; ?></div>
                                                <div class="macros_link"><span>[REGION]</span> - <?php echo _REGION; ?></div>
                                                <div class="macros_link"><span>[CITY]</span> - <?php echo _CITY; ?></div>
                                                <div class="macros_link"><span>[BRAND]</span> - <?php echo _MOBDEV; ?></div>
                                                <div class="macros_link"><span>[MODEL]</span> - <?php echo _MODEL; ?></div>
                                                <div class="macros_link"><span>[TAG]</span> - <?php echo _MACROS_TAG; ?></div>
                                            </font>
                                        </div>
                                     
                                    </td>
                    </div>
                    </tr>      
                    <tr>
                        <td valign=top><?php echo _PUSHBUTTON; ?> 1</td>
                        <td><input name="options[button1]" type="text" value="<?php echo  $options['button1']; ?>"  maxlength="15" class="longinput"> <li class="far fa-smile smile-button"></li></td>
                    </tr>
                    <tr>
                        <td valign=top><?php echo _PUSHBUTTON; ?> 2</td>
                        <td><input name="options[button2]" type="text" value="<?php echo $options['button2']; ?>"  maxlength="15" class="longinput"> <li class="far fa-smile smile-button"></li></td>
                    </tr>
                      <tr>
                        <td valign=top><strong><?php echo _LINK; ?></strong></td>
                        <td><input name="url" type="text" required value="<?php echo $form['url']; ?>"  maxlength="300" class=longinput><br />
                        <a href="#" onclick="viewblock('macross2', this); return false;" class=small><i class="fa  fa-tags"></i> <?php echo _MACROS; ?></a>
                                        <div id="macross2" style='display: none;'>
                                            <font class=small2>
                                                <div class="macros_link_url"><span>[ID]</span> - <?php echo _FAST_SEND_MACROS1; ?></div>
                                                <div class="macros_link_url"><span>[ISO]</span> - <?php echo _MACROS_ISO; ?></div>
                                                <div class="macros_link_url"><span>[COUNTRY]</span> - <?php echo _MACROS_COUNTRY; ?></div>
                                                <div class="macros_link_url"><span>[REGION]</span> - <?php echo _MACROS_REGION; ?></div>
                                                <div class="macros_link_url"><span>[CITY]</span> - <?php echo _MACROS_CITY; ?></div>
                                                <div class="macros_link_url"><span>[DEVICE]</span> - <?php echo _MACROS_DEVICE; ?></div>
                                                <div class="macros_link_url"><span>[BRAND]</span> - <?php echo _MACROS_BRAND; ?></div>
                                                <div class="macros_link_url"><span>[MODEL]</span> - <?php echo _MACROS_MODEL; ?></div>
                                                <div class="macros_link_url"><span>[OS]</span> - <?php echo _MACROS_OS; ?></div>
                                                <div class="macros_link_url"><span>[BROWSER]</span> - <?php echo _MACROS_BROWSER; ?></div>
                                                <div class="macros_link_url"><span>[AD_ICON]</span> - <?php echo _MACROS_AD_ICON; ?></div>
                                                <div class="macros_link_url"><span>[AD_IMG]</span> - <?php echo _MACROS_AD_IMG; ?></div>
                                                <div class="macros_link_url"><span>[AD_TITLE]</span> - <?php echo _MACROS_AD_TITLE; ?></div>
                                                <div class="macros_link_url"><span>[AD_TEXT]</span> - <?php echo _MACROS_AD_TEXT; ?></div>
                                            </font>
                                        </div>
                     </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo _ICON192; ?></strong></td>
                        <td><input type="file" name="img"> <?php echo tooltip("JPG, JPEG, PNG"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _ICON360; ?></td>
                        <td><input type="file" name="img2"> <?php echo tooltip("JPG, JPEG, PNG. "._NOT_NECESSERY); ?></td>
                    </tr>
                      <tr>
                        <td><?php echo _SITES; ?></td>
                        <td>
                            <select data-placeholder="<?php echo _CHOSE; ?>" multiple  class="standardSelect5" name="sids[]">
                                <option value=""></option>
                                <?php
                                foreach ($sites as $sid => $arr) {
                                    if ($arr['type']==2) {
                                        continue;
                                    }
                                    if ($form['sids'] && in_array($sid, $form['sids'])) $sel ='selected'; else $sel='';
                                    echo "<option value=\"$sid\" ".$sel.">#".$sid." ".$arr['title']." (".$arr['subscribers'].")</option>";
                                }
                                // if there are partner sites in exchange, show them
                                if (is_array($exch_sites)) {
                                 foreach ($exch_sites as $id => $arr) {
                                    $url = $sites_all[$arr['site_id']]['url'];
                                    $max_send = $arr['max_send'] - $arr['sended_today'];
                                    if ($form['sids'] && in_array($arr['site_id'], $form['sids'])) $sel ='selected'; else $sel='';
                                    echo "<option value=\"".$arr['site_id']."\" ".$sel.">#".$arr['site_id']." ".$url." (".$max_send.")</option>";
                                }   
                                }
                                ?>
                            </select> <?php echo tooltip(_SITES_TARGET, 'right'); ?>    

                        </td>
                    </tr>
                     <tr>
                        <td><?php echo _COUNTRYS; ?></td>
                        <td>
                            <select required data-placeholder="<?php echo _CHOSE; ?>" multiple class="standardSelect" name="regions[]">
                                <option value=""></option>
                                <?php

                                foreach ($isolist as $key => $arr) {
                                   
                                    if ($form['regions'] && in_array($arr['iso'], $form['regions'])) $sel ='selected'; else $sel='';
                                    echo "<option value=\"".$arr['iso']."\" ".$sel.">[".$arr['iso']."] ".$arr[$lang]."</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr><td valign=top><?php echo _LANGS; ?></td><td>
                            <select data-placeholder="<?php echo _CHOSE; ?>" multiple class="standardSelect2" name="langs[]">
                                <option value=""></option>
                                <?php
                                foreach ($langslist as $key => $value) {
                                    if ($form['langs'] && in_array($value['iso'], $form['langs'])) $sel = "selected"; else $sel='';
                                    echo "<option value=\"".$value['iso']."\" ".$sel.">[".$value['iso']."] ".$value[$lang]."</option>";
                                }
                                ?>
                            </select>
                        </td></tr>
                      <tr class="local"><td valign=top>Tags</td><td>
                            <select data-placeholder="<?php echo _CHOSE; ?>" multiple class="standardSelect3" name="tags[]">
                                <option value=""></option>
                                <?php
                                foreach ($targets['tag'] as $val) {
                                    if ($form['tags'] && in_array($val, $form['tags'])) $sel = "selected"; else $sel='';
                                    echo "<option value=\"$val\" ".$sel.">".$val."</option>";
                                }
                                ?>
                            </select>

                        </td></tr> 
                      <tr class="local"><td valign=top><?php echo _SUBSID; ?></td><td>
                      <textarea name="subsid" cols="50" rows="5" maxlength="120" class=longinput><?php echo $form['subsid'] ?></textarea>
                      <?php echo tooltip(_SUBSID_INFO, 'right'); ?>  
                        </td></tr>      
                     <tr><td valign=top><?php echo _FAST_SEND_TIME; ?></td><td>
                       <input name="send_time" type="text" value="<?php echo $form['send_time']; ?>" id='datepicker' class="longinput">     
                        </td></tr>
                     <tr>
                        <td><?php echo _SEND_LOOP; ?></td>
                        <td>
                        <label class="switch switch-text switch-success switch-pill"><input type="checkbox" name="loop_send" value="1" class="switch-input" <?php echo $loop_send_check; ?>> <span data-on="On" data-off="Off" class="switch-label"></span> <span class="switch-handle"></span></label>
                          <?php echo tooltip(_SEND_LOOP_INFO, 'right'); ?>   
                        </td>
                    </tr>     
                     <tr>
                        <td><?php echo _STATUS; ?></td>
                        <td>
                            <label><input name="status" type="radio" value="1" <?php echo $statussel[1]; ?>> <?php echo _STATUSON; ?></label> &nbsp;&nbsp;
                            <label><input name="status" type="radio" value="0" <?php echo $statussel[0]; ?>> <?php echo _STATUSOFF; ?></label>
                        </td>
                    </tr>      
                         </table>

        
                    <input type="hidden" name="sending" value="1">
                    <input type="hidden" name="save" value="1">
                    <input type="hidden" name="double_save" value="<?php echo $double; ?>">
                    <br />
                    <?php echo $icon_hide.$image_hide; ?>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo _SEND; ?></button>
                    <button type="button" id=submitButtonId class="btn btn-primary"><i class="fa fa-spinner"></i>  <?php echo _CHECK; ?></button> &nbsp; <label><input type="checkbox" name="send_push" value="1" checked /> <?php echo _SEND_ME; ?></label> <?php echo tooltip(_SEND_ME_TOOLTIP); ?>
                    <div id=checksubs style='margin-top: 5px;'></div>
                       </form> 
      
                        <form action="index.php?m=<?php echo $module; ?>" method="post">
                                 <table class="table1">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th width=10%><?php echo _DATE; ?></th>
                                <th width=25%><?php echo _ADV; ?></th>
                                <th width=15%><?php echo _CONDITION; ?></th>
                                <th width=30%><?php echo _STAT; ?></th>
                                <th><?php echo _ACTIONS; ?></th>
                            </tr>
                            </thead>
                            <tbody>
<?php
                            
                            $blocked_targets = targets();
                            $time = date("Y-m-d H:i:s");
                            if (is_array($myads)) {
                                foreach ($myads as $key => $value) {
                                    
                                    $langs = str_replace(",", ', ', $value['langs']);
                                    $value['regions'] = str_replace(",", ', ', $value['regions']);
                                   
                                    if ($check_login['root']==1) {
                                     if ($value['moderate']==0) {
                                            $moderate = "<strong class=orange>"._MODERATENO."</strong><br>";
                                        } elseif ($value['moderate']==1) {
                                            $moderate = "<strong class=green>"._MODERATEYES."</strong><br>";
                                        } elseif ($value['moderate']==2) {
                                            $moderate = "<strong class=red>"._MODERATEBLOCKED."</strong><br>";
                                        }
                                    } else $moderate='';
                       
                                    if ($value['status']==1) {
                                        if ($value['moderate']==1) {
                                        if ($value['sended'] > 0) {    
                                          $status = "<div class=green-block><i class=\"fas fa-play\"></i> "._SEND_NOW."</div>";
                                        } else {
                                        $status = "<div class=gray-block2><i class=\"fas fa-clock\"></i> "._SEND_WAIT."</div>";       
                                        }
                                        } elseif ($value['moderate']==0) {
                                        $status = "<div class=gray-block2><i class=\"fas fa-clock\"></i> "._SEND_MODER."</div>";      
                                        } elseif ($value['moderate']==2) {
                                        $status = "<div class=red-block><i class=\"fas fa-minus-circle\"></i> "._MODERATEBLOCKED."</div>";      
                                        }
                                        $status_link = "<a href=?m=my_send&id=".$key."&off=2>"._STOP."</a>";
                                        }
                                    elseif($value['status']==0)  {
                                        $status = "<div class=orange-block><i class=\"fas fa-pause\"></i> "._SEND_STOP."</div>";
                                        $status_link = "<a href=?m=my_send&id=".$key."&off=1>"._START."</a>";}
                                    elseif($value['status']==2)  {
                                        $status = "<div class=red-block>"._STATUSDEL."</div>";
                                        $moderate='';
                                    }
                                    
                                    if ($value['sended'] > 0) {  
                                        $count = $value['sended'] + $value['sended_wrong'];
                                        if ($value['loop_send']==1 && $value['loop_finish']==1) {
                                        $status = "<div class=green-block><i class=\"fa fa-check-circle\"></i> "._SEND_FINISH."</div>";     
                                        } elseif ($value['loop_send']==0 && $count >=  $value['subscribers']) {
                                        $status = "<div class=green-block><i class=\"fa fa-check-circle\"></i> "._SEND_FINISH."</div>";    
                                        }
                                        }
                                            
                                            
                                    $url = str_replace("&amp;", "|", $value['url']);
                                    $view_url = short_text($value['url'], 50);

                                    if ($value['langs']) {
                                    $view_langs = short_text($value['langs'], 50);
                                    $value['langs'] = "<span tooltip=\"".$value['langs']."\" flow=\"right\">".$view_langs."</span>";
                                    }
                                    
                                    if ($value['regions']) {
                                        $view_regions = short_text($value['regions'], 50);
                                        $value['regions'] = "<span tooltip=\"".$value['regions']."\" flow=\"right\">".$view_regions."</span>";
                                    }

                                    if ($value['clicks'] && $value['views']) $ctr  = round(($value['clicks']/$value['views'])*100, 2); else $ctr=0;
                                    $url = "<br /><font class=small><span tooltip=\"".$value['url']."\" flow=\"right\"><a href=\"/url.php?u=".$url."\" target=_blank>".$view_url."</a></span></font>";


                                    if ($value['image']) {

                                        $image = "<br><a href=\"#\" onclick=\"viewblock2('showimg_".$key."', this); return false;\" class=small><i class=\"fa fa-picture-o\"></i> "._SHOWIMG."</a><div id=\"showimg_".$key."\" style='display: none;'><img src=".$value['image']." class=bigimg></div><br>";
                                    } else $image='';
                                    
                                    $buttons='';
                                    if ($value['options']) {
                                        $options = json_decode($value['options'], true);
                                        if ($options['button1']) {
                                            $buttons = "<br><strong><i class=\"fa fa-square\"></i> <a href=#>".$options['button1']."</a></strong> &nbsp;&nbsp;";
                                        }
                                        if ($options['button2']) {
                                            $buttons .= "<strong><i class=\"fa fa-square\"></i> <a href=#>".$options['button2']."</a></strong>";
                                        }
                                    }

                                    if ($check_login['root']==1) {
                                        $admin = "<strong>user id:</strong> <a href=\"?m=a_users&admin_id=".$value['admin_id']."\">#".$value['admin_id']."</a> (".$admins[$value['admin_id']]['login'].") <a href=\"?m=my_send&admin_id=".$value['admin_id']."\">ads</a><br>";

                                    } else {$admin= ''; }
                                
                                     
                                    echo "<tr>
                                            <td>".$key." <br><input type=\"checkbox\" value=\"".$key."\" id=\"id".$key."\" name=\"ids[]\" /></td>
                                            <td>".$value['date']."</td>
                                            <td><img src=".$value['icon']." width=70 border=0 align=left class=advimg> <b>".$value['title']."</b><br />".$value['text']."<br />".$url."".$image."".$buttons."</td>
                                            <td valign=top>".$admin."";
                                            
                                    $site_names=array();
                                    if ($value['sids']) {
                                        $sids = explode(",", $value['sids']);
                                        $site_names=array();
                                        foreach ($sids as $sid) {
                                            $site_names[] = "<a href=\"?m=daystat&sid=".$sid."\" target=_blank>".$sites[$sid]['title']."</a>";
                                        }
                                        echo "<i class=\"fa fa-bars\"></i> <b>"._SITES.":</b> ".implode(", ", $site_names)."<br />";
                                    }        
                                            
                                    if ($value['regions']) {
                                        echo "<i class=\"fa fa-globe\"></i> <b>"._COUNTRYS.":</b> ".$value['regions']."<br />";
                                    }

                                    $tag_list=array();
                                    if ($value['tags']) {
                                        $tagsarr = explode(",", $value['tags']);
                                        foreach ($tagsarr as $tag) {
                                          //  $tag_list[] = "<a href=\"?m=sended&adv_id=".$key."&tag=".$tag."\" target=_blank>".$tag."</a>";
                                        }
                                        echo "<i class=\"fa fa-tags\"></i>  <b>Tags:</b> ".implode(", ", $tagsarr)."<br />";
                                    }
                                  
                                    if ($value['langs']) {
                                        echo "<i class=\"fa fa-comments\"></i> <b>"._LANGS.":</b> ".$value['langs']."<br />";
                                    }
                                    if ($value['send_time']) {
                                        $value['send_time'] = converToTz($value['send_time'], $settings['timezone']);
                                       echo "<i class=\"fa fa-clock-o\"></i> <b>"._FAST_SEND_TIME.":</b><br> ".$value['send_time']."<br>";
                                    }    
                                        if ($value['loop_send']==1) {
                                       echo '<i class="fa fa-eye"></i> '._SEND_LOOP.' <br>';
                                            } 
 
                                       

                                    if ($value['way_block']) {
                                        $ways = array();
                                        $arr = explode(',', $value['way_block']);
                                         foreach ($arr as $key2 => $value2) {
                                           $ways[] = $way_block[$value2];
                                         }
                                         $ways = implode('<br>', $ways);
                                         $ways = "<br><span class=red><strong>"._WAYBLOCK."</strong>:<br> $ways</span><br>";
                                        } else $ways = '';
                                        
                                    if ($value['comment']) {
                                        foreach ($blocked_targets as $key2 => $val) {
                                        $value['comment'] = str_replace($key2, $val[$lang], $value['comment']);
                                        }
                                        }
                                        
                                        $code = md5($config['global_secret'].$key);

                                     if ($value['comment']) {
                                            status_small($value['comment'], 'info');
                                            }

                                            
                                      echo "</td>
                                            <td>".$status."
                                            <table class=mys-table width=100%>
                                            <tr class=mys-table-tr1><td width=70%>"._SEND_RECIEVE."</td><td  align=right>".bigint($value['subscribers'])."</td></tr>
                                            <tr class=mys-table-tr2><td>"._MAILSSENDED."</td><td  align=right>".bigint($value['sended'])."</td></tr>
                                            <tr class=mys-table-tr3><td>"._VIEWS."</td><td  align=right>".bigint($value['views'])."</td></tr>
                                            <tr class=mys-table-tr4><td>"._CLICKS."</td><td  align=right>".bigint($value['clicks'])."</td></tr>
                                            <tr class=mys-table-tr5><td>CTR</td><td  align=right>".$ctr."%</td></tr>
                                            <tr class=mys-table-tr6><td>"._SEND_UNSUBS." ".tooltip(_SEND_UNSUBS_TOOLTIP)."</td><td  align=right>".bigint($value['unsubs'])."</td></tr>
                                            </table>

                                            </td>
                                            <td valign=top>".$moderate."".$ways."".$status_link."<br /><br />
                                            <a href=?m=my_send&double=".$key.">"._DOUBLE."</a><br /><br />
                                            <a href=# data-toggle=\"modal\" data-target=\"#del".$key."\">"._DELETE."</a><br /><br />";
                                            
                                            if ($check_login['root']==1) {
                                                echo "<div id=block-moderate" . $key . "><a href=# onclick=\"aj('ads_moderate.php', '" . $key . "|1|" . date('Y-m-d H:i:s') . "', 'moderate" . $key . "'); return false;\" style='color:#28882E'>"._MODERATEALLOW."</a></div>
                                                  <a href=# onClick=\"document.getElementById('blocker').style.display='block'; setCheckedOne('id".$key."'); return false;\" style='color: #931E1E'>"._MODERATEBLOCK."</a>";
                                            }
                                           
                                            echo "</td>
                                        </tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
            
             <?php
                    if ($all_ads > 0) {
                       
                        $numpages = ceil($all_ads / $filenum);
                        num_page($all_ads, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                        }

 if ($check_login['root']==1) {
 echo "<div class=blockwindow id=blocker>
<b>"._WAYBLOCK."</b> <div align=\"right\" style='float: right'><a href=# onClick=\"document.getElementById('blocker').style.display='none'; return false;\">X</a></div><br />";
foreach ($way_block as $key => $value) {
echo "<label><input name=\"way_block[]\" type=\"checkbox\" value=\"$key\"> $value</label><br />";
}

echo "<input name=\"time\" type=\"hidden\" value=\"" . date('Y-m-d H:i:s') . "\">
<button type=\"submit\" value=\"2\" name=\"moderate_change\" class=\"btn btn-danger\">"._MODERATEBLOCK."</button>
</div>";
echo " <label>"._CHOSENALL." <input type=\"checkbox\" name=\"set\" onclick=\"setChecked(this)\" /></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<input name=\"moderate\" type=\"hidden\" value=\"" . $moderate . "\">
<br><br><button type=\"submit\" value=\"1\" name=\"moderate_change\" class=\"btn btn-success\">"._MODERATEALLOW."</button> &nbsp;&nbsp;
<button type=\"button\" value=\"2\" name=\"moderate_change\" class=\"btn btn-danger\" onClick=\"document.getElementById('blocker').style.display='block';\">"._MODERATEBLOCK."</button>";
                        }
                        ?>
            </form>                                     
          <script>
                $('#datepicker').flatpickr({
                                     enableTime: true,
                                     dateFormat: "Y-m-d H:i",
                                     time_24hr: true,
                                     locale: "<?php echo $lang ?>"
                                 });
    $(document).ready(function () {
        
   $("#adsform").validate({
  rules: {
    title: "required",
    text: "required",
    url: {
      required: true,
      url: true
    }
  },
  messages: {
    url: " <span class=red><i class='fa fa-times-circle'></i> <?php echo _URL_WRONG ?></span>",
    title: " <span class=red><i class='fa fa-times-circle'></i> <?php echo _MYADV4 ?></span>",
    text: " <span class=red><i class='fa fa-times-circle'></i> <?php echo _MYADV5 ?></span>"
  }
});

            jQuery(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
        jQuery(".standardSelect2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
        jQuery(".standardSelect3").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
        jQuery(".standardSelect4").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "300px"
        });
        jQuery(".standardSelect5").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
        
        function loadEmoji(element) {

            var parent = $(element).parent();

            if($(element).parent().find('.push-tooltip-body').find('.emoji').length > 0) {
                $(parent).find('.push-tooltip-container').show();
            }
            else {
                $.ajax({
                    url: 'ajax/smiles.php',
                    beforeSend: function() {
                        $(parent).find('.push-tooltip-container').show();
                        $(element).parent().find('.push-tooltip-body').html('Loading');
                    },
                    success: function (response) {

                        $(parent).find('.push-tooltip-container').show();

                        $(element).parent().find('.push-tooltip-body').html(response);
                        $(element).parent().find('.push-tooltip-body').find('.emoji').click(emoji);
                    }
                });
            }

        }

        function emoji(e) {

            var inputElement = $(e.currentTarget).closest('td').find('input, textarea');

            var code = this.outerHTML;

            var smile = $(this).data('c');

            var value = inputElement.val();
            value += " " + smile;

            inputElement.val(value);
        }

        function createTooltip() {
            var content = '';
            content += '<div class="push-tooltip-container"><div class="push-tooltip"><div class="push-tooltip-body">333</div></div></div>';
            return content;
        }

        var res = createTooltip();

        $(".smile-button").each(function () {
            $(this).closest('td').append(res);
        });

        $(".smile-button").click(function () {
            $(".push-tooltip-container").hide();
            loadEmoji(this);
        });

        $(document).mouseup(function (e){ 
            var div = $(".push-tooltip-container"); 
            if (!div.is(e.target) 
                && div.has(e.target).length === 0) {
                div.hide();
            }
        });

    })
              
                          function viewblock(id, context) {

                                    if($('#'+id).css('display')=='none') {
                                        $('#'+id).show();
                                    }
                                    else {
                                        $('#'+id).hide();
                                    }
                                }
                        $("#submitButtonId").click(function(e) {

                            e.preventDefault();

                            var form = document.getElementById('adsform');
                            var formData = new FormData(form);

                            $.ajax({
                                type: "POST",
                                url: 'ajax/check_subs.php',
                                data: formData,
                                cache:false,
                                contentType: false,
                                processData: false,
                                beforeSend: function() {
                                    document.getElementById("checksubs").innerHTML = '<div class="ajax-load"></div>';
                                },
                                success: function(data)
                                {
                                    document.getElementById("checksubs").innerHTML = data;

                                }
                            });


                        });

                        $("#show_form").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });

                    </script>     
                            <script>
                                            function insertPosition(txtToAdd, input) {
                                                if(!input) {
                                                    return false;
                                                }

                                                var $txt = input;
                                                var caretPos = $txt[0].selectionStart;
                                                var textAreaTxt = $txt.val();
                                                console.log(textAreaTxt);
                                                $txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
                                            }

                                            var input = null;

                                            $(document).ready(function () {

                                                $("textarea[name=text], input[name=title]").on('click', function () {
                                                    input = $(this);
                                                });

                                                $(".macros_link span").on('click', function (e) {
                                                    var macros = $(this).text();
                                                    insertPosition(macros, input);
                                                })

                                                $(".macros_link_url span").on('click', function (e) {
                                                    var macros = $(this).text();
                                                    insertPosition(macros, $('input.longinput[name=url]'));
                                                })
                                            })
                                        </script>   
                                                   <script type="text/javascript">
                        function setChecked(obj)
                        {

                            var check = document.getElementsByName("ids[]");
                            for (var i=0; i<check.length; i++)
                            {
                                check[i].checked = obj.checked;
                            }
                        }
                         function setCheckedOne(id)
                        {
                         document.getElementById(id).checked = true;
                        }
                        
                            function viewblock2(id, context) {

                                    if($('#'+id).css('display')=='none') {
                                        $('#'+id).show();

                                        $(context).html('<i class="fa fa-picture-o"></i> <?php echo _HIDEIMG ?>');
                                    }
                                    else {
                                        $('#'+id).hide();
                                        $(context).html('<i class="fa fa-picture-o"></i> <?php echo _SHOWIMG ?>');
                                    }
                                }

                    </script>    
                                           
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
    <?php

if (is_array($myads)) {
    foreach ($myads as $key => $value) {
        modal(_DELADV, _DELADV." ".$key."? <input name=\"delete\" type=\"hidden\" value=\"".$key."\">", 1, "del".$key, "?m=my_send");
    }
}
?>