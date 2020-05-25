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
if($settings['allow_admins']!=1 && $check_login['root']!=1)  {
    exit;
}
?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _ADMINS ?></h4>
</div>  
         <?php

$delete = text_filter($_POST['delete']);
$edit = text_filter($_POST['edit']);
$addadmin = intval($_POST['addadmin']);
$login = text_filter($_POST['login']);
$pass = text_filter($_POST['pass']);
$email = text_filter($_POST['email']);
$role = intval($_POST['role']);
$status = intval($_POST['status']);
$isroot = intval($_POST['root']);

if ($addadmin==1 && $check_login['role']==1) {
if ($check_login['root']!=1) $isroot = 0; // only root can add root

if (!$login || !$pass || !$email || !$role) $stop = _FEEDSTEXT1."<br>";
$is_login = get_onerow('id', 'admins', "login='".$login."'");
$is_email = get_onerow('id', 'admins', "email='".$email."'");

if ($is_login)  $stop .= _ISLOGIN."<br>";
if ($is_email)  $stop .= _IS_MAIL."<br>";
$login_check = preg_match( '/^[a-z\d]{4,20}$/i', trim($login));
if ($login_check==0) $stop .= _LOGINWRONG."<br>";

if (!$stop) {

$db->sql_query("INSERT INTO admins (id, login, pass,  email, role, owner_id, date, root)
VALUES (NULL, '".$login."', '".md5($pass)."', '".$email."', '".$role."', '".$check_login['id']."', now(), '".$isroot."')") or $error = mysqli_error();
if ($error) {
jset($check_login['id'], $error, 1);    
status($error, 'danger');
} else {
jset($check_login['id'], _USER." "._SMALL_ADDED.": $login");  
if ($check_login['id']!=$check_login['getid']) {
 alert(_USER." "._SMALL_ADDED.": $login (user: ".$check_login['login'].")", $check_login['getid']); 
  }
status(_USER." "._SMALL_ADDED, 'success');
}
} else {
status($stop, 'danger');
}
}

if ($delete && $check_login['role']==1) {
$db->sql_query('DELETE FROM admins WHERE id="'.$delete.'" AND owner_id='.$check_login['id'].'') or $error = mysqli_error();
if ($error) {
jset($check_login['id'], $error, 1);  
status($error, 'danger');
} else {
jset($check_login['id'], _USER." "._SMALL_DELETED.": $login"); 
if ($check_login['id']!=$check_login['getid']) {
 alert(_USER." "._SMALL_DELETED.": #".$delete." (user: ".$check_login['login'].")", $check_login['getid']); 
  } 
status(_USER." "._SMALL_DELETED, 'success');
}
}

if ($edit && $check_login['role']==1) {

if (!$email) $stop = _FEEDSTEXT1;

 if (!$stop) {
 if ($pass) $newpass = "pass='".md5($pass)."',";
 if ($role) $newrole = "role='".$role."',";
$db->sql_query("UPDATE admins SET ".$newpass." ".$newrole." email='".$email."', role='".$role."', status=".$status." WHERE id='".$edit."' AND owner_id='".$check_login['id']."'") or $error = mysqli_error();
if ($error) {
jset($check_login['id'], $error, 1);  
status($error, 'danger');
} else {
jset($check_login['id'], _USER." "._SMALL_UPDATED.": $edit"); 
if ($check_login['id']!=$check_login['getid']) {
 alert(_USER." "._SMALL_UPDATED.": #".$edit." (user: ".$check_login['login'].")", $check_login['getid']); 
  } 
status("#$edit - "._USER." "._SMALL_UPDATED, 'success');
}
} else {
status($stop, 'danger');
}
 }

?>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
                         <?php
                  if ($check_login['role']==1) {
                  ?>
                         <div class="addbutton">
                         <button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addform"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _ADDADMIN; ?></button>
                          </div><br />
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo _LOGIN; ?></th>
                                            <th><?php echo _EMAIL; ?></th>
                                            <th><?php echo _ROLE; ?></th>
                                            <th><?php echo _LASTACTIVE; ?></th>
                                            <th><?php echo _STATUS; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                       $admins = admins("AND owner_id=".$check_login['id']."");
                                       $roles = array(1 => 'ADMIN', 2 => 'GUEST');
                                     if ($admins!=false) {
                                      foreach ($admins as $key => $value) {
                                       if ($value['status']==1) $status = "<b class=green>"._STATUSON."</b>"; else $status = "<b class=red>"._STATUSOFF."</b>";
                                       if ($value['root']==1) $isroot = "<br><b class=red>root</b>"; else $isroot = '';
                                      	 echo "<tr>
                                            <td>".$value['login']."</td>
                                            <td>".$value['email']."</td>
                                            <td>".$roles[$value['role']]."".$isroot."</td>
                                            <td>".$value['last_login']."</td>
                                            <td>".$status."</td>
                                            <td>
                                            <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#edit".$key."\">"._EDIT."</button><br />";
                                            if ($key!='admin') {
                                            echo "<button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#del".$key."\">"._DELETE."</button>";
                                             }
                                             echo "</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                               <?php
                } else {
                status(_NOACCESS, 'warning');
                }
                  ?>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->
</div>
    <!-- Right Panel -->
      <?php

         if ($admins!=false) {
             foreach ($admins as $key => $value) {
              modal(_DELETEADMIN." ".$value['login'], _DELETEADMIN." ".$value['login']."? <input name=\"delete\" type=\"hidden\" value=\"".$key."\">", 1, "del".$key, "?m=admins");
              if ($value['status']==1) $status1 = "checked"; else $status2 = "checked";
              $form = "<table><tbody>
                       <tr><td>"._LOGIN."</th><td><b>".$value['login']."</b></td> </tr>
                       <tr><td>"._PASS."</th><td><input name=\"pass\" type=\"text\" value=\"\" class=longinput></td></tr>
                       <tr><td>"._EMAIL."</th><td><input name=\"email\" type=\"text\" value=\"".$value['email']."\" class=longinput></td></tr>
                       <tr><td>"._ROLE."</th><td>";
                        if ($key!='admin') {
                              foreach ($roles as $key1 => $val) {
                              	if ($value['role']==$key1) $ch = 'checked'; else $ch='';
                                  $form .= "<input name=\"role\" type=\"radio\" value=\"".$key1."\" ".$ch."> ".$val." &nbsp;";
                                }
                                } else $form .= 'ADMIN';
                       $form .= "</td></tr>
                       <tr><td>"._STATUS."</th><td><input name=\"status\" type=\"radio\" value=\"1\" ".$status1."> "._ACTIVE2." &nbsp; <input name=\"status\" type=\"radio\" value=\"2\" ".$status2."> "._BLOCKED."</td></tr>
                       </tbody>
                       </table><input name=\"edit\" type=\"hidden\" value=\"".$key."\">";
              modal(_EDITADMIN." ".$value['name'], $form, 2, "edit".$key, "?m=admins");

              }
              }

      ?>


      <div class="modal fade" id="addform" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _ADDADMIN; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form action="?m=admins" method="post">
                               <table>
                                    <tbody>
                                        <tr><td class=col1><?php echo _LOGIN; ?></th><td><input name="login" type="text" value="" class=longinput></td> </tr>
                                        <tr><td><?php echo _PASS; ?></th><td><input name="pass" type="text" value="" class=longinput></td>  </tr>
                                        <tr><td><?php echo _EMAIL; ?></th><td><input name="email" type="text" value="" class=longinput></td>  </tr>
                                        <tr><td><?php echo _ROLE; ?></th><td>
                                        <?php
                                        foreach ($roles as $key => $val) {
                                        echo "<input name=\"role\" type=\"radio\" value=\"".$key."\"> ".$val." &nbsp;";
                                        }
                                        ?>
                                        </td>  </tr>
                                        <?php if ($check_login['root']==1) {  ?>
                                        <tr><td>ROOT</th><td><input type="checkbox" name="root" value="1" /></td>  </tr>
                                        <?php }  ?>
                                    </tbody>
                                </table>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="addadmin" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>