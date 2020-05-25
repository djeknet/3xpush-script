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


$status = intval($_POST['status']);
$ids = $_POST['ids'];
$admin_ids = text_filter($_POST['admin_ids']);
$title_save = text_filter($_POST['title']);
$content_save = text_filter($_POST['content'], 2);
$lang = text_filter($_POST['lang']);
$date= text_filter($_POST['date']);
$save = intval($_POST['save']);
$edit_save = intval($_POST['edit_save']);
$group_id = intval($_POST['group_id']);
$edit_form = intval($_GET['edit']);
$type = intval($_POST['type']);

if ($save==1 && $check_login['role']==1) {
  if (!$title_save) $stop = _MYADV4."<br>";  
  if (!$content_save) $stop .= _NEED_TEXT."<br>";   
  if (!$stop) {
     if ($edit_save) {
    if ($group_id) {
        $where1 = "AND group_id='$group_id'";
        $status_title = "Email updated. Group id $group_id";
    } else {
       $where1 = "AND  id='$edit_save'";
       $status_title = "$edit_save - Email updated";  
    }
$db->sql_query("UPDATE mails SET title='$title_save', content='$content_save', lang='$lang', date='$date' WHERE status=0 ".$where1."");   

jset($check_login['id'], $status_title);
if ($check_login['id']!=$check_login['getid']) {
alert("$status_title (user: ".$check_login['login'].")", $check_login['getid']);
} 

status($status_title, 'success');
      } else {
     if (!$admin_ids) $stop .= _NEED_RECIVERS."<br>";    
      if (!$stop) { 
        $admin_ids_arr = explode(",", $admin_ids);
        $all = count($admin_ids_arr);
        if ($all>1) {
          $group_id = rand(1,99999999);  
        }
        $i=0;
        $nomail=0;
        if ($type==2) $where_user = "AND promo_mail=1"; // если это промо рассылка, то выбираем только тех пользователей, которые разрешили такую
        foreach ($admin_ids_arr as $key => $value) {
            $admin = admins("AND id=".$value." AND get_mail=1 ".$where_user."");
            if (is_array($admin)) {
                $content_send = str_replace("[LOGIN]", $admin[$value]['login'], $content_save);
                $content_send = str_replace("[USER_NAME]", $admin[$value]['name'], $content_send);
                $content_send = str_replace("[USER_MAIL]", $admin[$value]['email'], $content_send);
                $content_send = str_replace("[USER_TELEGRAM]", $admin[$value]['telegram'], $content_send);
                $content_send = str_replace("[USER_IP]", $admin[$value]['ip'], $content_send);
                
                $db->sql_query("INSERT INTO mails (id, date, admin_id, email, title, content, create_time, group_id, lang) VALUES (NULL, '".$date."', '".$value."', '".$admin[$value]['email']."', '".$title_save."', '".$content_send."', now(), '".$group_id."', '".$lang."')")  or $stop = mysqli_error();
            $i++;
            } else {
                $nomail++;
            }
        }
        jset($check_login['id'], "Email added: $i receivers, not allowed - $nomail");
if ($check_login['id']!=$check_login['getid']) {
alert("Email added: $i receivers, not allowed - $nomail (user: ".$check_login['login'].")", $check_login['getid']);
} 
        status("Email added: $i receivers, not allowed - $nomail", 'success');
        } else {
    status($stop, 'danger');
  }
      }
  } else {
    status($stop, 'danger');
  }
}

// изменение статуса отправки
if ($ids && $status && $check_login['role']==1) {
   
    $ids = implode(',', $ids);
    $db->sql_query("UPDATE mails SET status='$status' WHERE id IN (".$ids.")");  
    
jset($check_login['id'], "Email status changed: $status");
if ($check_login['id']!=$check_login['getid']) {
alert("Email status changed: $status (user: ".$check_login['login'].")", $check_login['getid']);
}   
status(_CHANGESTATUS, 'success');

}


?>


                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $email = text_filter($_GET['email']);
                             $text = text_filter($_GET['text']);
                             $admin_id = intval($_GET['admin_id']);
                             $id = intval($_GET['id']);
                             if ($id==0) $id='';
                             $group_id = intval($_GET['group_id']);
                             if ($group_id==0) $group_id='';
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             $status  = intval($_GET['status']);
                             
                             if (!$pagenum) $pagenum = 1;

                                     if ($email) {
                                     $where .= "AND email LIKE '%$email%' ";
                                     $dopurl .= "&email=$email";
                                     }
                                     if ($admin_id) {
                                     $where .= "AND admin_id = '$admin_id'  ";
                                      $dopurl .= "&admin_id=$admin_id";
                                     }
                                     if ($text) {
                                     $where .= "AND (title LIKE '%$text%' OR content LIKE '%$text%')  ";
                                      $dopurl .= "&text=$text";
                                     }
                                     if ($id) {
                                     $where .= "AND id = '$id' ";
                                     }
                                     if ($group_id) {
                                     $where .= "AND group_id = '$group_id'  ";
                                      $dopurl .= "&group_id=$group_id";
                                     }
                                     $selstatus[0] = 'selected';
                                     if ($status) {
                                        if ($status==1) $where .= "AND status = '1' ";
                                        if ($status==2) $where .= "AND status = '0' ";
                                        if ($status==3) $where .= "AND status = '2' ";
                                         $dopurl .= "&status=$status";
                                         $selstatus[$status] = 'selected'; 
                                     }
                                     $filenum = 30;
                                     $offset = ($pagenum - 1) * $filenum;
                             
                                      $mails = mails($where, "$offset, $filenum");
                                    
                                      $all_mails = mails($where);
                                      if (is_array($all_mails)) $all_mails = count($all_mails); else $all_mails = 0;
                                      $admins = admins();
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-2">ID <input type="text" name="id" value="<?php echo $id ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2">Group id <input type="text" name="group_id" value="<?php echo $group_id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TEXT ?>  <input type="text" name="text" value="<?php echo $text ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">E-mail <input type="text" name="email" value="<?php echo $email ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2">user id <input type="text" name="admin_id" value="<?php echo $admin_id ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-3 inlineblock"><?php echo _STATUS ?><br />
                                 <select name="status" class="form-control-sm form-control col col-md-4">
                                 <option value="0"><?php echo _EVERY ?></option>
                                 <option value="1" <?php echo $selstatus[1] ?>><?php echo _MAILSSENDED ?></option>
                                 <option value="2" <?php echo $selstatus[2] ?>><?php echo _MAILSNOSENDED ?></option>
                                 <option value="3" <?php echo $selstatus[3] ?>><?php echo _MAILSCANCEL ?></option>
                                 </select></div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp; <?php echo _ALLFOUND.": <strong>".$all_mails."</strong>" ?>
                             </form>
                             </div>
                            <div class="card-body">
 
   <?php
                            $title_form = _ADD;
                            $icon = 'plus';
                            if ($edit_form) {
                                list($date, $admin_id, $email, $title, $content, $status, $group_id, $lang) = $db->sql_fetchrow($db->sql_query("SELECT date, admin_id, email, title, content, status, group_id, lang FROM mails WHERE id=".$edit_form.""));

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                $langsel[$lang] = 'selected';
                                $dis = 'disabled';
                                if ($group_id) {$grup_title = "<strong>Групповая рассылка #".$group_id."</strong>";
                                $hide = "<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\"  />";}
                            } else {
                               $date = date("Y-m-d H:i:s"); 
                            } 
                            
                            $langs = array_flip(explode(',', $settings['langs']));
   ?>  
   <script>
function serialize (form) {
    if (!form || form.nodeName !== "FORM") {
            return;
    }
    var i, j, q = [];
    for (i = form.elements.length - 1; i >= 0; i = i - 1) {
        if (form.elements[i].name === "") {
            continue;
        }
        switch (form.elements[i].nodeName) {
            case 'INPUT':
                switch (form.elements[i].type) {
                    case 'text':
                    case 'tel':
                    case 'email':
                    case 'hidden':
                    case 'password':
                    case 'button':
                    case 'reset':
                    case 'submit':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                    case 'checkbox':
                    case 'radio':
                        if (form.elements[i].checked) {
                                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        }                                               
                        break;
                }
                break;
                case 'file':
                break; 
            case 'SELECT':
                switch (form.elements[i].type) {
                    case 'select-one':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                    case 'select-multiple':
                        for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                            if (form.elements[i].options[j].selected) {
                                    q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
                            }
                        }
                        break;
                }
                break;

            }
        }
    return q.join("|");
}
</script>
   <script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>                         
 <a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._SENDING1; ?> </a>
<form action="?m=a_mails" method="post" id="htmlEditor" name="html"   enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <?php echo $grup_title ?>
  <div class="gray-block">
  <strong><?php echo _SENDING_TEXT1; ?></strong><br />
  <?php echo _LANG1 ; ?>: <select size="1" name="search_lang">
  <option value="1">all</option>
  <?php
   foreach ($langs as $key => $value) {
   echo "<option value=\"$key\">$key</option>";
   }
   ?>   
    </select> &nbsp;&nbsp;&nbsp;
  <?php echo _SENDING_TEXT2 ; ?>: <input type="text" name="search_balance" value="" size="10" />   &nbsp;&nbsp;&nbsp;
  <?php echo _SENDING_TEXT3 ; ?>: <input type="text" name="search_sites" value="" size="10" />   &nbsp;&nbsp;&nbsp;
  <button type="button"  onclick="aj('search_mail.php', serialize(this.form),1); return false;"><?php echo _SENDING_TEXT6 ; ?></button> 
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
  <tr><td width="20%"><?php echo _TYPE ; ?></td><td> <label><input type="radio" name="type" value="1" /> <?php echo _NEWS1; ?></label> &nbsp;&nbsp; <label><input type="radio" name="type" value="2" /> <?php echo _SENDING_TEXT7; ?></label> </td></tr>
  <tr><td width="20%"><?php echo _SENDING_TEXT5 ; ?> (id)</td><td><div id="block-1"><input type="text" name="admin_ids" value="<?php echo $admin_ids; ?>" size="50" <?php echo $dis; ?> /></div></td></tr>
  <tr><td><?php echo _LANG1 ; ?></td><td> <select size="1" name="lang">
    <?php
   foreach ($langs as $key => $value) {
   echo "<option value=\"".$key."\" ".$langsel[$key].">".$key."</option>";
   }
   ?> 
                                        </select> </td></tr>
 <tr><td><?php echo _TITLE ; ?></td><td><input type="text" name="title" value="<?php echo $title; ?>" size="50" /></td></tr>
 <tr><td  valign=top><?php echo _TEXT ; ?></td><td>
   
                            <textarea name="content" id="txtDefaultHtmlArea" rows=50 cols=15
                                      style='width: 60%; height: 500px;'
                                      wrap="off"><?php echo $content; ?></textarea><br />
                                      <span class="small">
                                      [LOGIN] - <?php echo _SENDING_TEXT8 ; ?><br />
                                      [USER_NAME] - <?php echo _USERNAME ; ?><br />
                                      [USER_MAIL] - email<br />
                                      [USER_TELEGRAM] - telegram<br />
                                      [USER_IP] - ip<br />
                                      </span>

                                    <script>
                                        CKEDITOR.replace( 'content', {
                                            height: 500,
                                            width: 600,
                                            allowedContent: true,
                                            title: false
                                        });
                                    </script>
                                    
                                    </td></tr>
 <tr><td><?php echo _SENDING_TEXT9 ; ?></td><td><input type="text" name="date" value="<?php echo $date; ?>" size="20" /></td></tr>
</table>
                    <input type="hidden" name="edit_save" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">  
                    <?php echo $hide; ?>           
</form>
  <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
   <script>
$("#show_form").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });
                        </script>                                

                             <form name="filters" action="index.php?m=<?php echo $module; ?>" method="post" id="searchform" class="form-horizontal">
                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _SENDING_TEXT9 ; ?> / <?php echo _CREATED; ?></th>
                                            <th><?php echo _LOGIN; ?></th>
                                            <th>E-mail</th>
                                            <th width=40%><?php echo _TITLE; ?></th>
                                            <th><?php echo _STATUS; ?></th>
                                            <th>Tracking</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if (is_array($mails)) {
                                      foreach ($mails as $key => $value) {  
                                        $edit='';
                                         if ($value['status']==1) {
                                            $status = "<span class=green><strong>"._MAILSSENDED."</strong></span>";
                                         } elseif ($value['status']==2) {
                                            $status = "<span class=red><strong>"._MAILSCANCEL."</strong></span>";
                                         } else {
                                            $status = "<span class=orange>"._MAILSNOSENDED."</span>";
                                            $edit = "<br><a href=?m=".$module."&edit=$key>"._EDIT."</a>";
                                         }
                                         $login = $admins[$value['admin_id']]['login'];
                                         if ($value['group_id']) {
                                            $group_id = "<br><br><span class=small>Group id ".$value['group_id']."</span>";
                                         } else $group_id='';
                                         $value['content'] = htmlspecialchars_decode($value['content'], ENT_QUOTES);
                                         if ($value['views']>0) {
                                            $views = "<span class=green>"._VIEWS.": ".$value['views']."</span>";
                                         } else $views = '-';
                                         if ($value['clicks']>0) {
                                            $clicks = "<br><span class=green>"._CLICKS.": ".$value['clicks']."</span>";
                                         } else $clicks = '';
                                         
                                           echo "<tr>
                                            <td>".$key." <input type=\"checkbox\" value=\"".$key."\" id=toggle name=\"ids[]\" /></td>
                                            <td>".$value['date']."<br><span class=gray>".$value['create_time']."</span></td>
                                            <td><a href=?m=a_users&admin_id=".$value['admin_id']." target=_blank>".$value['admin_id']."</a> (".$login.")</td>
                                            <td>".$value['email']."</td>
                                            <td><strong>".$value['title']."</strong><br><a href=\"#\" onclick=\"viewblock('show_".$key."', this); return false;\" class=small><i class=\"fa fa-plus-square-o\"></i> "._SHOW." "._TEXT." </a>
                                            <div id=\"show_".$key."\" style='display: none;'>".$value['content']."</div></td>
                                            <td>".$status."".$edit."".$group_id."</td>
                                            <td>".$views."".$clicks."</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                          
                                  <?php
                                $numpages = ceil($all_mails / $filenum);
                                 num_page($all_mails, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                 
echo " <label>"._CHOSENALL." <input type=\"checkbox\" name=\"set\" onclick=\"setChecked(this)\" /></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
                                   
echo "<button type=\"submit\" value=\"2\" name=\"status\" class=\"btn btn-success\">"._CANCEL2."</button>";  

                                ?>
                                
                                      </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

