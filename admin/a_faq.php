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
<h4 class="page-title"><?php echo _FAQ ?></h4>
</div>
 
         <?php

$status = intval($_GET['status']);
$id = intval($_GET['id']);
$edit_form = intval($_GET['edit']);
$delete = intval($_GET['del']);
$edit = intval($_POST['edit']);
$save = intval($_POST['save']);
$sorts = intval($_POST['sorts']);
$type = text_filter($_POST['type']);

if ($delete && $check_login['role']==1) {
$db->sql_query("DELETE FROM faq WHERE id=".$delete."") or $stop = mysql_error();
if ($stop) {
status($stop, 'danger');
} else {
jset($check_login['id'], "FAQ delete: $delete");
if ($check_login['id']!=$check_login['getid']) {
alert("FAQ delete: $delete (user: ".$check_login['login'].")", $check_login['getid']);
}
status(_DELETEFIELD, 'success');
}
}

if ($save && $check_login['role']==1) {
$title = $_POST['title'];
$answer = $_POST['answer'];
foreach ($title as $key => $value) {
    $value = str_replace("\r\n", "", $value);
$titles[$key] = text_filter($value, 2);
}
foreach ($answer as $key => $value) {
 $value= text_filter($value, 2);
        $value = str_replace("\r\n", "<br>", $value);
$answers[$key] = $value;
}
$titles = json_encode($titles, JSON_UNESCAPED_UNICODE);
$answers = json_encode($answers, JSON_UNESCAPED_UNICODE);

 if (!$stop) {
    if ($edit) {
    $db->sql_query("UPDATE faq SET type='".$type."', title='".$titles."', answer='".$answers."', sorts='".$sorts."' WHERE id=".$edit."") or $stop = mysqli_error();
  jset($check_login['id'], "FAQ update: $edit");
if ($check_login['id']!=$check_login['getid']) {
alert("FAQ update: $edit (user: ".$check_login['login'].")", $check_login['getid']);
}
  status(_UPDATEFIELD, 'success');
   } else {
     $db->sql_query("INSERT INTO faq (id, title, answer, sorts, type) VALUES (NULL, '".$titles."', '".$answers."', '".$sorts."', '".$type."')")  or $stop = mysqli_error();
  jset($check_login['id'], "FAQ add");
if ($check_login['id']!=$check_login['getid']) {
alert("FAQ add (user: ".$check_login['login'].")", $check_login['getid']);
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
                             $answer = text_filter($_GET['answer']);
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                                     if ($title) {
                                     $where .= "AND title LIKE '%$title%' ";
                                     $dopurl .= "&title=$title";
                                     }
                                     if ($answer) {
                                     $where .= "AND answer LIKE '%$answer%' ";
                                     $dopurl .= "&ip=$answer";
                                     }
                                     if ($id) {
                                     $where .= "AND id=$id ";
                                     }
                                     $filenum = 30;
                                     $offset = ($pagenum - 1) * $filenum;
                                     
                                      $faq = faq($where, "$offset, $filenum");
                                      $all_faq = faq($where);
                                      if (is_array($all_faq)) $all_faq = count($all_faq); else $all_faq = 0;
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-3">ID <input type="text" name="id" value="<?php echo $id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _QUESTION ?>  <input type="text" name="title" value="<?php echo $title ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _ANSWER ?> <input type="text" name="answer" value="<?php echo $answer ?>"  class="form-control form-control-sm"></div>
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
                                list($type, $title, $answer, $sorts) = $db->sql_fetchrow($db->sql_query("SELECT type, title, answer, sorts FROM faq WHERE id=".$edit_form.""));

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                
                                $titles = json_decode($title, true);
                                $answers = json_decode($answer, true);
                                $type_sel[$type] = 'selected';
                                
                            } else {
                                $titles=array();$answers=array();
                                $arr = array_flip(explode(',', $settings['langs']));
                                foreach ($arr as $key => $value) {
                                $titles[$key] = ''; 
                                $answers[$key] = '';
                                }
                                
                                $type_sel['all'] = 'selected';
                            }
   ?>                           
<a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._ANSWER; ?></a>
<form action="?m=a_faq" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                <tr><td width="20%" valign=top><?php echo _QUESTION; ?></td><td>
                 <?php
                 foreach ($titles as $key => $value) {
                 echo "<strong>$key: </strong><input type=\"text\" name=\"title[".$key."]\" value=\"".$value."\" size=\"50\" /><br>";
                 }
                ?>
                </td></tr>
                <tr><td  valign=top><?php echo _ANSWER; ?></td><td>
                 <?php
                 foreach ($answers as $key => $value) {
                         $value = str_replace("<br>", "\n", $value);
                 echo "<strong>$key: </strong><textarea cols=\"50\" rows=\"5\" wrap=\"virtual\" name=\"answer[".$key."]\">".$value."</textarea><br>";
                 }
                ?></td></tr>

                <tr><td><?php echo _SORTS; ?></td><td><input type="text" name="sorts" value="<?php echo $sorts; ?>" size="5" /></td></tr>           
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th width=20%><?php echo _FOR_WHO; ?></th>
                                            <th width=20%><?php echo _QUESTION; ?></th>
                                            <th width=50%><?php echo _ANSWER; ?></th>
                                            <th><?php echo _SORTS; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($faq!=false) {
                                      foreach ($faq as $key => $value) {
                                        $titl='';$answ='';
                                        $titles = json_decode($value['title'], true);
                                        $answers = json_decode($value['answer'], true);
                                        foreach ($titles as $key2 => $value2) {
                                           $titl .= "<strong>$key2</strong>: $value2<br>"; 
                                        }
                                        
                                        foreach ($answers as $key2 => $value2) {
                                           $answ .= "<strong>$key2</strong>: $value2<br>"; 
                                        }
                                        if ($value['type']=='all') {
                                            $type = _EVERY;
                                        } elseif ($value['type']=='adv') {
                                            $type = _ADVERT;
                                        } elseif ($value['type']=='wm') {
                                            $type = _WEBMASTER;
                                        }
                                        
                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$type."</td>
                                            <td>".$titl."</td>
                                            <td>".$answ."</td>
                                            <td>".$value['sorts']."</td>
                                            <td><a href=?m=a_faq&edit=".$key.">"._EDIT."</a><br>
                                            <a href=?m=a_faq&del=".$key."  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_faq / $filenum);
                                 num_page($all_faq, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
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