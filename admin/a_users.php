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

if ($check_login['root']!=1) exit;

?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _USERS ?></h4>
</div>
         <?php
$edit = intval($_POST['edit']);
$name = text_filter($_POST['name']);
$email = text_filter($_POST['email']);
$email_check = intval($_POST['email_check']);
$telegram = text_filter($_POST['telegram']);
$newpass = text_filter($_POST['newpass']);
$skype = text_filter($_POST['skype']);
$ref_active = intval($_POST['ref_active']);
$discount = intval($_POST['discount']);
$deny_ads = intval($_POST['deny_ads']);
$deny_sending = intval($_POST['deny_sending']);
$check_city = intval($_POST['check_city']);
$is_root = intval($_POST['root']);
$is_support = intval($_POST['is_support']);
$good_user = intval($_POST['good_user']);

$status = intval($_GET['status']);
$id = intval($_GET['id']);
$virtual_id = intval($_GET['virtual_id']);
if ($virtual_id && $check_login['role']==1) {
status("You are under account ".$virtual_id."", 'success');   
}

// change user status
if ($status && $id && $check_login['role']==1) {
$db->sql_query("UPDATE admins SET status='$status', last_edit=now() WHERE id=".$id.""); 
if ($status==2) {
$db->sql_query("UPDATE myads SET status='2', last_edit=now() WHERE admin_id=".$id."");    
}
jset($check_login['id'], _USTATUS_CHANGED.": $id - $status");
if ($check_login['id']!=$check_login['getid']) {
alert(_USTATUS_CHANGED.": $id - $status (user: ".$check_login['login'].")", $check_login['getid']);
}
   
status("#$id - "._STATUSUPDATE, 'success');
} 

// edit user
if ($edit && $check_login['role']==1) {
    $admin_id = $edit;
if (!$name) $stop = _NEED_NAME;
if (!$email) $stop .= "<br>"._NEED_MAIL;
if ($newpass) {
    $newpass = md5($newpass);
    $pass = ", pass='$newpass'";
}
if ($check_login['id']==1) $is_root = 1; // 1 is always super)

 if (!$stop) {
$db->sql_query('UPDATE admins SET good_user="'.$good_user.'", skype="'.$skype.'", is_support="'.$is_support.'", root="'.$is_root.'", check_city="'.$check_city.'", deny_sending="'.$deny_sending.'", ref_active="'.$ref_active.'", name="'.$name.'", email="'.$email.'", email_check="'.$email_check.'", telegram="'.$telegram.'" '.$pass.' WHERE id='.$edit.'') or $stop = mysqli_error();
if ($comission && $comission < 100) {
    if ($comission==-1) $comission = 0;
$db->sql_query('UPDATE balance SET comission="'.$comission.'" WHERE admin_id='.$edit.'') or $stop = mysqli_error();
}
if ($stop) {
status($stop, 'danger');
} else {
    
        jset($check_login['id'], " $edit - "._USER_UPDATED);
if ($check_login['id']!=$check_login['getid']) {
alert("$edit - "._USER_UPDATED." (user: ".$check_login['login'].")", $check_login['getid']);
}

status("$edit - "._USER_UPDATED."", 'success');
}
} else {
status($stop, 'danger');
}
 }
?>
  <script type="text/javascript">
                                  function viewblock(id, context) {

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

                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $email = text_filter($_GET['email']);
                             $login = text_filter($_GET['login']);
                             $telegram = text_filter($_GET['telegram']);
                             $ip = text_filter($_GET['ip']);
                             if (!$admin_id) $admin_id = intval($_GET['admin_id']);
                             $like = intval($_GET['like']);
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             $sort  = intval($_GET['sort']);
                             
                             if (!$pagenum) $pagenum = 1;

                                     if ($email) {
                                     $where .= "AND a.email LIKE '%$email%' ";
                                     $dopurl .= "&email=$email";
                                     }
                                     if ($login) {
                                        if ($like==1) {
                                         $where .= "AND a.login LIKE '%$login%'  ";    
                                        } else {
                                         $where .= "AND a.login = '$login'  ";    
                                        }
                                    
                                     }
                                     if ($admin_id) {
                                     $where .= "AND a.id = '$admin_id'  ";
                                     }
                                     if ($telegram) {
                                     $where .= "AND a.telegram = '$telegram'  ";
                                     }
                                     if ($ip) {
                                     $where .= "AND a.ip LIKE '%$ip%' ";
                                     $dopurl .= "&ip=$ip";
                                     }
                                     $filenum = 30;
                                     $offset = ($pagenum - 1) * $filenum;
                                      $sorts = 'a.id';
                                     if ($sort==1) {
                                        $sorts = 'a.id';
                                        $sortsel[$sort] = 'selected';
                                     } elseif ($sort==2) {
                                        $sorts = 'b.summa';
                                        $sortsel[$sort] = 'selected';
                                     }
                                     
                                      $admins = admins_balance($where, $sorts, "$offset, $filenum");
                                      $all_admins = admins($where);
                                      $users_ip = users_ip();
                                      if (is_array($all_admins)) $all_admins = count($all_admins); else $all_admins = 0;
                                      if ($like==1) {
                                        $ch[0] = 'checked';
                                        }
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-3">ID <input type="text" name="admin_id" value="<?php echo $admin_id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _INSTALL7 ?>  <input type="text" name="login" value="<?php echo $login ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3">E-mail <input type="text" name="email" value="<?php echo $email ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3">IP <input type="text" name="ip" value="<?php echo $ip ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3">Telegram <input type="text" name="telegram" value="<?php echo $telegram ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2 inlineblock"><?php echo _SORTFORM; ?><br />
                                     <select name="sort" class="form-control-sm form-control col col-md-8">
                                      <option value="1" <?php echo $sortsel[1] ?>>ID</option>
                                      <option value="2" <?php echo $sortsel[2] ?>><?php echo _BALANCE; ?></option>
                                     </select>
                                    </div>
                                </div>
                                <input type="checkbox" value="1" name="like" <?php echo $ch[0] ?> /> <?php echo _ANY; ?> <br />

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>

                            <div class="card-body">
                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _INSTALL7; ?></th>
                                            <th><?php echo _USERNAME; ?></th>
                                            <th><?php echo _CONTACTS; ?></th>
                                            <th><?php echo _STAT; ?></th>
                                            <th><?php echo _BALANCE; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $langs = admins_lang();
                                     if ($admins!=false) {
                                      foreach ($admins as $key => $value) {
                                        
                                         $result = $SxGeo->getCityFull($value['ip']);
                                       
                                         $cc = $result['country']['iso'];
                                         $country = $result['country']['name_ru'];
                                         
                                      	 $balance = balance($key);
                                         if ($value['status']==1) {
                                            $status = "<span class=green><strong>"._ACTIVE2."</strong></span><br><br><a href=?m=a_users&status=2&id=$key class=red>"._BLOCK."</a>";
                                         } else {
                                            $status = "<span class=red><strong>"._BLOCKED."</strong></span><br><br><a href=?m=a_users&status=1&id=$key class=green>"._ACTIVATE."</a>";
                                         }
                                         if ($value['owner_id']!=0) {
                                            $owner_login = get_onerow('login', 'admins', "id=".$value['owner_id']."");
                                            $slave = "<br><span class=small>"._ACCCREATED." <strong>".$owner_login."</strong></span>";
                                         } else $slave='';
                                         if ($value['role']==2) $guest = ' <span class=small>[guest]</span>'; else $guest ='';
                                         if ($value['root']==1) $sadmin = ' <span class="small3 green">[admin]</span>'; else $sadmin = ' ';
                                         if (!$value['name']) $value['name'] = '-';
                                         if ($value['email_check']==0) $email_check = "<br><span class=\"small3 red\"><strong>"._EMAILNOTAPPROVE."</strong></span>"; else $email_check = '';
                                      	 $total_stat = total_stat("AND admin_id=".$key."");
                                         if (!$total_stat['subscribers']) $total_stat['subscribers'] = 0;
                                         if (!$total_stat['unsubs']) $total_stat['unsubs'] = 0;
                                         if ($total_stat['subscribers'] >0 && $total_stat['unsubs']) {
                                            $active_proc = round(($total_stat['unsubs']/$total_stat['subscribers'])*100, 0);
                                         } else $active_proc = '-';
                                         
                                         $all_sites = get_onerow('COUNT(id)', 'sites', "admin_id=$key");
                                         if (!$all_sites) $all_sites = 0; else $all_sites = "<a href=?m=sites&admin_id=".$key." target=_blank>".$all_sites."</a>";

                                         $code = md5($config['global_secret'].$key);
                                         if ($value['telegram']) {
                                            $value['telegram'] = "<br><br><span class=small>Telegram:</span><br><a href=\"tg:".$value['telegram']."\" target=_blank>".$value['telegram']."</a>";
                                         }
                                         if ($value['skype']) {
                                            $value['skype'] = "<br><br><span class=small>Skype:</span><br><a href=\"skype:".$value['skype']."\" target=_blank>".$value['skype']."</a>";
                                         }
                                         if ($value['good_user']==1) {
                                            $value['good_user'] = "<span class=green title=\""._GOOD_USER."\"><i class=\"fas fa-shield-alt\"></i></span>";
                                         } else $value['good_user']='';
                                         
                                         if ($value['reg_from']) {
                                            $reg_from = "<strong>"._REFERER."</strong>: <a href=/url.php?u=".urlencode($value['reg_from']).">".short_text($value['reg_from'], 100)."</a>";
                                         } else $reg_from='';
                                         
                                         $count_ip = $users_ip['ip'][$value['ip']];
                                         $check_ip='';
                                    
                                         if ($count_ip > 1) {
                                            $net = subnet($value['ip'], 2);
                                            $count_subnet = $users_ip['subnet'][$net];
                                            $check_ip = "<br>"._DOUBLEIP.": <a href=?m=a_users&ip=".$value['ip'].">".$count_ip."</a><br>"._FROM_NETWORK.":  <a href=?m=a_users&ip=".$net.">".$count_subnet."</a>";
                                         }
                                         if ($value['comission']>0) {
                                          $comission = "<br><span class=\"small2 green\">"._COMISSION.": ".$value['comission']."%</span>";  
                                         } else $comission = '';
                                         
                                            
                                            $lang = $langs[$key];
                                            if ($lang=='ru') {
                                              $flag = "<img src=images/flags/RU.gif align=absmiddle>";  
                                            } else {
                                              $flag = "<img src=images/flags/US.gif align=absmiddle>";  
                                            }
                                         
                                           echo "<tr>
                                            <td>".$key."<br><span class=small>".$value['date']."</span></td>
                                            <td>".$value['login']."".$sadmin."".$guest."".$slave." ".$value['good_user']."<br>
                                            <span class=small>".$value['ip']." ".$check_ip."</span></td>
                                            <td>".$value['name']."</td>
                                            <td>"._LANG1.": ".$flag." <br>
                                            ".$value['email']."".$email_check."".$value['telegram']."".$value['skype']."</td>
                                            <td><b>"._REGION.":</b> $cc, $country<br>
                                            <b>"._SITES.":</b> ".$all_sites." <br>
                                            <b>"._SUBSCRIBERS.":</b> <a href=?m=subscribers&admin_id=".$key." target=_blank>".$total_stat['subscribers']."</a> &nbsp;&nbsp; <a href=?m=daystat&admin_id=".$key." target=_blank>"._DAYSTAT."</a><br>
                                            <b>"._ACTIVEPROC.":</b> ".$active_proc."% <br>
                                            <b>"._LASTLOGIN2.":</b> ".$value['last_login']."</td>
                                            <td> <a href=?m=a_payment&admin_id=".$key." target=_blank>".$balance."</a> ".$comission."</td>
                                            <td>".$status."<br>
                                            <a href=?m=".$module."&virtual_id=".$key.">"._ACCOUNT_ENTER."</a><br>
                                            <a href=# data-toggle=\"modal\" data-target=\"#edituser\" onclick=\"aj('user_edit.php','$key|$code',1); return false;\">"._EDIT."</a><br>
                                            <a href=# data-toggle=\"modal\" data-target=\"#uinfo\" onclick=\"aj('user_info.php','$key|$code',2); return false;\">"._DETAIL."</a></td>
                                        </tr>";
                                        
                                        echo "<tr><td colspan=7 style=\"border-top: none;\">".$reg_from."</td></tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_admins / $filenum);
                                 num_page($all_admins, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

<div class="modal fade" id="edituser" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _USER_EDIT; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=a_users" method="post">
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
                
<div class="modal fade" id="uinfo" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _MORE_INFO; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=a_users" method="post">
                          <div id="block-2"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                            </div>
                            <input name="addsite" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                </div>              