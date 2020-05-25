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
<h4 class="page-title"><?php echo _NEWS ?></h4>
</div>
         <?php

$status = intval($_GET['status']);
$id = intval($_GET['id']);
$edit_form = intval($_GET['edit']);
$delete = intval($_GET['del']);
$edit = intval($_POST['edit']);
$save = intval($_POST['save']);
$date = text_filter($_POST['date']);
$send_alert = intval($_POST['send_alert']);

// delete news
if ($delete && $check_login['role']==1) {
$db->sql_query("DELETE FROM news WHERE id=".$delete."") or $stop = mysql_error();
if ($stop) {
status($stop, 'danger');
} else {
jset($check_login['id'], "NEWS delete: $delete");
if ($check_login['id']!=$check_login['getid']) {
alert("NEWS delete: $delete (user: ".$check_login['login'].")", $check_login['getid']);
}
status(_DELETEFIELD, 'success');
}
}

// save news
if ($save && $check_login['role']==1) {
$title = $_POST['title'];
$content = $_POST['content'];
foreach ($title as $key => $value) {
$titles[$key] = text_filter($value);
}
foreach ($content as $key => $value) {
$value= text_filter($value, 2);
$value = str_replace("\r\n", "<br>", $value);
$contents[$key] = $value;
}
$titles = json_encode($titles, JSON_UNESCAPED_UNICODE);
$contents = json_encode($contents, JSON_UNESCAPED_UNICODE);
if ($date) {
    $upd = "date='$date', ";
    $set = "'$date'";
} else $set = "now()";

 if (!$stop) {
    if ($edit) {
    $db->sql_query("UPDATE news SET ".$upd." send_alert='$send_alert', title='".$titles."', content='".$contents."' WHERE id=".$edit."") or $stop = mysqli_error();
  jset($check_login['id'], "NEWS update: $edit");
if ($check_login['id']!=$check_login['getid']) {
alert("NEWS update: $edit (user: ".$check_login['login'].")", $check_login['getid']);
}
  status(_UPDATEFIELD, 'success');
   } else {
     $db->sql_query("INSERT INTO news (id, date, title, content, send_alert) VALUES (NULL, $set, '$titles', '$contents', '$send_alert')")  or $stop = mysqli_error();
  jset($check_login['id'], "NEWS added");
if ($check_login['id']!=$check_login['getid']) {
alert("NEWS added (user: ".$check_login['login'].")", $check_login['getid']);
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
                         <div class="card-body card-block">

                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $title = text_filter($_GET['title']);
                             $content = text_filter($_GET['content']);
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                                     if ($title) {
                                     $where .= "AND title LIKE '%$title%' ";
                                     $dopurl .= "&title=$title";
                                     }
                                     if ($content) {
                                     $where .= "AND content LIKE '%$content%' ";
                                     $dopurl .= "&ip=$content";
                                     }
                                     if ($id) {
                                     $where .= "AND id=$id ";
                                     }
                                     $filenum = 30;
                                     $offset = ($pagenum - 1) * $filenum;
                                     
                                      $news = news($where, 'id', "$offset, $filenum");
                                   
                                      $all_news = news($where);
                                      if (is_array($all_news)) $all_news = count($all_news); else $all_news = 0;
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-3">ID <input type="text" name="id" value="<?php echo $id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TITLE ?>  <input type="text" name="title" value="<?php echo $title ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TEXT ?> <input type="text" name="content" value="<?php echo $content ?>"  class="form-control form-control-sm"></div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                             </div>

                            <div class="card-body">
  <?php
                            $title_form = _ADD;
                            $icon = 'plus';
                            if ($edit_form) {
                                
                                $edit_data = news("AND id=$edit_form", 'id');
                                
                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                $titles = json_decode($edit_data[$edit_form]['title'], true);
                                $content = json_decode($edit_data[$edit_form]['content'], true);
                                if ($edit_data[$edit_form]['send_alert']==1) $send_alert = "checked";
                                if ($edit_data[$edit_form]['send_chat']==1) $send_chat = "checked";
                                 
                            } else {
                                $titles='';$content='';
                                $arr = array_flip(explode(',', $settings['langs']));
                                foreach ($arr as $key => $value) {
                                $titles[$key] = ''; 
                                $content[$key] = '';
                                }
                            }
                            if (!$date) $date = date("Y-m-d");
   ?>                           
<a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._NEWS1; ?></a>
<form action="?m=a_news" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                
                <tr><td width="20%" valign=top><?php echo _TITLE; ?></td><td>
                 <?php
                 foreach ($titles as $key => $value) {
                 echo "<strong>$key: </strong><input type=\"text\" name=\"title[".$key."]\" value=\"".$value."\" size=\"50\" /><br>";
                 }
                ?>
                </td></tr>
                <tr><td  valign=top><?php echo _TEXT; ?></td><td>
                 <?php
                 foreach ($content as $key => $value) {
                     $value = str_replace("<br>", "\n", $value);
                 echo "<strong>$key: </strong><textarea cols=\"50\" rows=\"5\" wrap=\"virtual\" name=\"content[".$key."]\">".$value."</textarea><br>";
                 }
                ?></td></tr>
                <tr><td><?php echo _DATE; ?></td><td><input type="text" name="date" id='datepicker' value="<?php echo $edit_data[$edit_form]['date']; ?>" size="8" /></td></tr> 
                <tr><td><?php echo _NEWS_TEXT1; ?></td><td><input type="checkbox" name="send_alert" value="1" <?php echo $send_alert; ?> /></td></tr>    
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>
<script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _DATE; ?></th>
                                            <th><?php echo _TITLE; ?></th>
                                            <th><?php echo _TEXT; ?></th>
                                            <th><?php echo _NEWS_TEXT2; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($news!=false) {
                                      foreach ($news as $key => $value) {
                                        $titl='';$cont='';
                                        $titles = json_decode($value['title'], true);
                                        $content = json_decode($value['content'], true);
                                        foreach ($titles as $key2 => $value2) {
                                           $titl .= "<strong>$key2</strong>: $value2<br>"; 
                                        }
                                        foreach ($content as $key2 => $value2) {
                                           $cont .= "<strong>$key2</strong>: $value2<br>"; 
                                        }
                                        $send_alert = '-';
                                        if ($value['send_alert']==1) {
                                            $send_alert = "alert<br>";
                                        }
                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['date']."</td>
                                            <td>".$titl."</td>
                                            <td>".$cont."</td>
                                            <td><span class=green>".$send_alert."</span></td>
                                            <td><a href=?m=a_news&edit=".$key.">"._EDIT."</a><br>
                                            <a href=?m=a_news&del=".$key."  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_news / $filenum);
                                 num_page($all_news, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
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