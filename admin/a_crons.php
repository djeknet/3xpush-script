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
<h4 class="page-title"><?php echo _CRONS ?></h4>
</div>
         <?php

$location = text_filter($_POST['location']);
$cronfile = text_filter($_POST['cronfile']);
$frequency = text_filter($_POST['frequency']);
$is_stable = intval($_POST['is_stable']);
$description= text_filter($_POST['description']);
$time_from = text_filter($_POST['time_from']);
$time_to = text_filter($_POST['time_to']);
$reset = intval($_POST['reset']);

$id = intval($_GET['id']);
$edit_form = intval($_GET['edit']);
$delete = intval($_GET['del']);
$edit = intval($_POST['edit']);
$save = intval($_POST['save']);

if ($delete && $check_login['role']==1) {
$db->sql_query("DELETE FROM crons WHERE id=".$delete."") or $stop = mysqli_error();
if ($stop) {
status($stop, 'danger');
} else {
jset($check_login['id'], "Cron deleted: $delete");
if ($check_login['id']!=$check_login['getid']) {
alert("Cron deleted: $delete (user: ".$check_login['login'].")", $check_login['getid']);
}
status(_DELETEFIELD, 'success');
}
}

if ($save && $check_login['role']==1) {
if (!$cronfile) $stop .= "cron name, ";
if (!$frequency) $stop .= "cron frequency, ";
if (!$description) $stop .= "description, ";

 if (!$stop) {
    if ($edit) {
     if ($reset==1) $q = "count_errors=0, last_start='', ";   
    $db->sql_query("UPDATE crons SET ".$q." frequency='".$frequency."', location='".$location."', cronfile='".$cronfile."', is_stable='".$is_stable."', description='".$description."', time_from='".$time_from."', time_to='".$time_to."' WHERE id=".$edit."") or $stop = mysqli_error();
 jset($check_login['id'], "Cron updated: $edit");
if ($check_login['id']!=$check_login['getid']) {
alert("Cron updated: $edit (user: ".$check_login['login'].")", $check_login['getid']);
}
  status(_UPDATEFIELD, 'success');
   } else {
     $db->sql_query("INSERT INTO crons (id, location, cronfile, frequency, is_stable, description, time_from, time_to) VALUES (NULL, '$location', '$cronfile', '$frequency', '$is_stable', '$description', '$time_from', '$time_to')")  or $stop = mysqli_error();
  jset($check_login['id'], "Cron added");
if ($check_login['id']!=$check_login['getid']) {
alert("Cron added (user: ".$check_login['login'].")", $check_login['getid']);
}
     status(_INSERTFIELD, 'success');
   }
if ($stop) {
status($stop, 'danger');
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

                            <div class="card-body">
  <?php
                            $title_form = _ADD;
                            $icon = 'plus';
                          
                            $edit_cron = array();
                            if ($edit_form) {
                                $edit_cron = crons("AND id='$edit_form'");

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                if ($edit_cron[$edit_form]['location']=='master') $sel[0] = 'selected'; else $sel[1] = 'selected'; 
                                if ($edit_cron[$edit_form]['is_stable']==1) $is_stable = 'checked';
                            } 
                            if (!$edit_cron[$edit_form]['time_from']) $edit_cron[$edit_form]['time_from'] = "00:00:00";
                            if (!$edit_cron[$edit_form]['time_to']) $edit_cron[$edit_form]['time_to'] = "23:59:00";
                            
   ?>                           
<a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._CRON; ?></a>
<form action="?m=a_crons" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                
<tr><td width="20%" valign=top><?php echo _LOCATION ?></td><td>
<select size="1" name="location">
<option value="master" <?php echo $sel[0] ?>>master</option>
<option value="slave" <?php echo $sel[1] ?>>slave</option>
</select>
                </td></tr>
                <tr><td><?php echo _NAME ?></td><td><input type="text" name="cronfile" value="<?php echo $edit_cron[$edit_form]['cronfile']; ?>" size="10" /></td></tr>
                <tr><td><?php echo _DESCR ?></td><td><textarea cols="35" name="description" rows="5" wrap="virtual" maxlength="150"><?php echo $edit_cron[$edit_form]['description']; ?></textarea></td></tr>   
                <tr><td><?php echo _FREQUENCY ?></td><td><input type="text" name="frequency" value="<?php echo $edit_cron[$edit_form]['frequency']; ?>" size="10" /> sek</td></tr>        
                <tr><td><?php echo _LAUNCH ?></td><td><input type="text" name="time_from" value="<?php echo $edit_cron[$edit_form]['time_from']; ?>" size="10" /> <?php echo _TILL ?> <input type="text" name="time_to" value="<?php echo $edit_cron[$edit_form]['time_to']; ?>" size="10" /></td></tr>
                <tr><td><?php echo _STATUSON ?></td><td><input type="checkbox" name="is_stable" value="1" <?php echo $is_stable; ?> /></td></tr> 
                <tr><td><?php echo _RESETERROR ?></td><td><input type="checkbox" name="reset" value="1" /></td></tr> 
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>

                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _LOCATION ?></th>
                                            <th><?php echo _NAME ?></th>
                                            <th><?php echo _DESCR ?></th>
                                            <th><?php echo _FREQUENCY ?></th>
                                            <th><?php echo _LAUNCHED ?></th>
                                            <th><?php echo _ERRORS ?></th>
                                            <th><?php echo _AVGTIME ?></th>
                                            <th><?php echo _TIME ?></th>
                                            <th><?php echo _ACTIONS ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                              $crons = crons($where);
                                     if ($crons!=false) {
                                      foreach ($crons as $key => $value) {
                                        if ($value['count'] >0 && $value['time'] >0 ) $avg_time = round($value['time'] / $value['count'], 2); else $avg_time=0;
                                        if ($value['is_stable']==1) $status = "<span class=green>"._STATUSON."</span>"; else  $status = "<span class=red>"._STATUSOFF."</span>";
                                        if ($value['count_errors'] >0 ) $err[0] = warn_symb(); else $err[0] = '';
                                        if ($value['last_start'] > $value['last_end']) $err[1] = warn_symb(); else $err[1] = '';
                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['location']."</td>
                                            <td>".$value['cronfile']."<br> ".$status."</td>
                                            <td>".$value['description']."</td>
                                            <td>".$value['frequency']." sek</td>
                                            <td>".$value['count']."</td>
                                            <td>".$value['count_errors']." ".$err[0]."</td>
                                            <td>".$avg_time." sek</td>
                                            <td>".$value['time_from']." - ".$value['time_to']."<br>
                                            <span class=small>"._LAST_LAUNCH.": ".$value['last_start']."<br>
                                            "._COMPLETE.": ".$value['last_end']." ".$err[1]."</span></td>
                                            <td><a href=?m=a_reports&cron_id=".$key.">"._LAUNCH_LOG."</a><br>
                                            <a href=?m=a_crons&edit=".$key.">"._EDIT."</a><br>
                                            <a href=?m=a_crons&del=".$key."  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

<script>
$("#show_form").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });
                        </script>