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
<h4 class="page-title"><?php echo _CONTENT ?></h4>
</div>  
         <?php

$status = intval($_POST['status']);
$id = intval($_GET['id']);
$edit_form = intval($_GET['edit']);
$delete = intval($_GET['del']);
$double = intval($_GET['double']);
$edit = intval($_POST['edit']);
$save = intval($_POST['save']);
$section_id = intval($_POST['section_id']);

$date = text_filter($_POST['pub_date']);
$type = text_filter($_POST['type']);
$name = text_filter($_POST['name']);
$pageurl = text_filter($_POST['pageurl']);
$newtype = text_filter($_POST['newtype']);
if ($newtype) $type = $newtype;
$code = text_filter($_POST['code'], 3);
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;


// double content
if ($double && $check_login['role']==1) {
     $db->sql_query("INSERT INTO content (type, section_id, name, title, content, status, code, pub_date, cteate_date)
SELECT type, section_id, name, title, content, 0, code, pub_date, now()  
FROM content
WHERE id = $double");

jset($check_login['id'], "Content doubled: $double");
if ($check_login['id']!=$check_login['getid']) {
alert("Content doubled: $double (user: ".$check_login['login'].")", $check_login['getid']);
}
    status (_DOUBLEFIELD, 'success');   
    }
    
// delete
if ($delete && $check_login['role']==1) {
$db->sql_query("DELETE FROM content WHERE id=".$delete."") or $stop = mysql_error();
if ($stop) {
status($stop, 'danger');
} else {
jset($check_login['id'], "Content deleted: $delete");
if ($check_login['id']!=$check_login['getid']) {
alert("Content deleted: $delete (user: ".$check_login['login'].")", $check_login['getid']);
}
status(_DELETEFIELD, 'success');
}
}

// save
if ($save && $check_login['role']==1) {
$title = $_POST['title'];
$content = $_POST['content'];
if ($title) {
foreach ($title as $key => $value) {
$titles[$key] = text_filter($value);
}
$titles = json_encode($titles, JSON_UNESCAPED_UNICODE);
}
if ($content) {
foreach ($content as $key => $value) {
//$value= text_filter($value, 2);
$value = addslashes($value);
$value = str_replace("\r", "", $value);
$value = str_replace("\n", "", $value);
$value = str_replace("\r\n", "", $value);
$value = str_replace("	", "", $value);
$contents[$key] = $value;
}
$contents = json_encode($contents, JSON_UNESCAPED_UNICODE);
}


if ($date) {
    $upd = "pub_date='$date', ";
    $set = "'$date'";
} else $set = "now()";

 if (!$stop) {
    if ($edit) {
         
    $db->sql_query("UPDATE content SET ".$upd." section_id='$section_id', code='$code', type='$type', name='$name', status='$status', title='".$titles."', content='".$contents."', pageurl='$pageurl' WHERE id=".$edit."") or $stop = mysqli_error();
 jset($check_login['id'], "Content updated: $edit");
if ($check_login['id']!=$check_login['getid']) {
alert("Content updated: $edit (user: ".$check_login['login'].")", $check_login['getid']);
}
  status(_UPDATEFIELD, 'success');
   } else {
     $db->sql_query("INSERT INTO content (id, type, name, title, content, status, cteate_date, pub_date, pageurl, code, section_id) VALUES (NULL, '$type', '$name', '$titles', '$contents', '$status', now(), $set, '$pageurl', '$code', '$section_id')")  or $stop = mysqli_error();
  jset($check_login['id'], "Content added");
if ($check_login['id']!=$check_login['getid']) {
alert("Content added (user: ".$check_login['login'].")", $check_login['getid']);
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
                                      }
                                      else {
                                          $('#'+id).hide();
                                      }
                                }
                                </script> 
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
    <nav>
<div class="nav nav-tabs" id="nav-tab-1" role="tablist">
<a class="nav-item nav-link <?= $tab==1 ? 'active' : '' ?>" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-1" aria-selected="true" name="1"><?php echo _CONTENT ?></a>
<a class="nav-item nav-link <?= $tab==2 ? 'active' : '' ?>" id="nav-2-tab" data-toggle="tab" href="#nav-2" role="tab" aria-controls="nav-2" aria-selected="false" name="2"><?php echo _SECTIONS ?></a>
</div>
</nav>      
  <div class="tab-content pl-3 pt-2" id="nav-tabContent-1">
<div class="tab-pane fade show <?= $tab==1 ? 'active' : '' ?>" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab">              
                         <div class="card-body card-block">
                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $title = text_filter($_GET['title']);
                             $content = text_filter($_GET['content']);
                             $type = text_filter($_GET['type']);
                             $name = text_filter($_GET['name']);
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                                     if ($title) {
                                     $where .= "AND title LIKE '%$title%' ";
                                     $dopurl .= "&title=$title";
                                     }
                                     if ($name) {
                                     $where .= "AND name='$name' ";
                                     $dopurl .= "&name=$name";
                                     }
                                     if ($type) {
                                     $where .= "AND type = '$type' ";
                                     $dopurl .= "&type=$type";
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
                                     
                                      $data = content($where, 'id', "$offset, $filenum");
                                   
                                      $all_content = content($where);
                                      $type_list=array();
                                      if (is_array($all_content)) {
                                      foreach ($all_content as $key => $value) {
                                         $type_list[] = $value['type'];
                                      }
                                      $type_list=array_unique($type_list);
                                      }
                                      if (is_array($all_content)) $all_content = count($all_content); else $all_content = 0;
                                       $content_section = content_section();
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-2">ID <input type="text" name="id" value="<?php echo $id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TITLE ?>  <input type="text" name="title" value="<?php echo $title ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TEXT ?> <input type="text" name="content" value="<?php echo $content ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TYPE ?> <select name="type" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                        <?php
                                        foreach ($type_list as $key => $val) {
                                        if ($type && $type==$val) $sel ='selected'; else $sel='';
                                        echo "<option value=\"$val\" ".$sel.">".$val."</option>";
                                        }
                                        ?>
                                    </select>

                                    </div>
                                    <div class="col col-md-2">name <input type="text" name="name" value="<?php echo $name ?>"  class="form-control form-control-sm"></div>
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
                                list($section_id, $title, $type, $name, $content, $status, $views, $pub_date, $pageurl, $code) = $db->sql_fetchrow($db->sql_query("SELECT section_id, title, type, name, content, status, views, pub_date, pageurl, code FROM content WHERE id=".$edit_form.""));

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                $titles = json_decode($title, true);
                                $content = json_decode($content, true);
                                $statussel[$status] = 'checked';
                            } else {
                                $titles=array();$content=array();
                                $arr = array_flip(explode(',', $settings['langs']));
                                foreach ($arr as $key => $value) {
                                $titles[$key] = ''; 
                                $content[$key] = '';
                                }
                            }
               
                           
   ?>   
      <script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>                           
<a href="#" id="show_form" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._CONTENT1; ?></a>
<form action="?m=a_content" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_form ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                <tr><td width="20%" valign=top><?php echo _TYPE; ?></td><td> <select size="1" name="type">
                                            <option value="0"><?php echo _CHOSE; ?></option>
                                            <?php
                                            foreach ($type_list as $key => $val) {
                                                if ($type && $type==$val) $sel = "selected"; else $sel='';
                                                echo "<option value=\"$val\" ".$sel.">$val</option>";
                                            }
                                            ?>
                                        </select> <?php echo _OR; ?> <input name="newtype" type="text" value="" placeholder="<?php echo _ADDNEW; ?>">
                                        </td></tr>
  <tr><td width="20%" valign=top><?php echo _SECTION; ?></td><td> <select size="1" name="section_id">
                                            <option value="0"><?php echo _CHOSE; ?></option>
                                            <?php
                                            foreach ($content_section as $key => $val) {
                                                $titles1 = json_decode($val['titles'], true);
                                                if ($section_id && $section_id==$key) $sel = "selected"; else $sel='';
                                                echo "<option value=\"$key\" ".$sel.">".$titles1[$lang]."</option>";
                                            }
                                            ?>
                                        </select> 
                                        </td></tr>                                        
                <tr><td>name</td><td><input type="text" name="name" value="<?php echo $name; ?>" size="50" /></td></tr>
                <tr><td width="20%" valign=top><?php echo _TITLE; ?></td><td>
                 <?php
                 foreach ($titles as $key => $value) {
                 echo "<strong>$key: </strong><br><input type=\"text\" name=\"title[".$key."]\" value=\"".$value."\" size=\"50\" /><br>";
                 }
                ?>
                </td></tr>
                <tr><td  valign=top><?php echo _TEXT; ?></td><td>
                 <?php
                 $i=1;
                 if (is_array($content)) {
                 foreach ($content as $key => $value) {
                     $value = str_replace("<br>", "\n", $value);
                 echo "<strong>$key: </strong><br><textarea cols=\"50\" rows=\"5\" wrap=\"virtual\" name=\"content[".$key."]\" id=\"content".$i."\">".$value."</textarea><br>";
                 $i++;
                 }
                 }
                ?></td></tr>
                <tr><td><?php echo _CODE; ?></td><td><textarea cols="50" rows="5" wrap="virtual" name="code"><?php echo $code; ?></textarea></td></tr>
                <tr><td><?php echo _PAGEURL; ?></td><td><input type="text" name="pageurl" value="<?php echo $pageurl; ?>" size="50" /></td></tr>
                <tr><td><?php echo _DATE; ?></td><td><input type="text" id='datepicker' name="pub_date" value="<?php echo $pub_date; ?>" size="8" /></td></tr>  
                <tr><td><?php echo _STATUS; ?></td><td>  <input name="status" type="radio" value="1" <?php echo $statussel[1]; ?>> <?php echo _STATUSON; ?> &nbsp;&nbsp;
                            <input name="status" type="radio" value="0" <?php echo $statussel[0]; ?>> <?php echo _STATUSOFF; ?></td></tr>            
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_form; ?>">
                    <input type="hidden" name="save" value="1">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>
  <script>
  $( document ).ready(function() {
                                        CKEDITOR.replace( 'content1', {
                                            height: 400,
                                            width: 600,
                                            allowedContent: true,
                                            title: false
                                        });
                                            CKEDITOR.replace( 'content2', {
                                            height: 400,
                                            width: 600,
                                            allowedContent: true,
                                            title: false
                                        });
                                        
                                        });
                                    </script>
                                    
  <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>

                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th width=5%>№</th>
                                            <th width=10%><?php echo _DATE; ?></th>
                                            <th width=10%><?php echo _DATEPUB; ?></th>
                                            <th><?php echo _TYPE; ?></th>
                                            <th><?php echo _SECTION; ?></th>
                                            <th  width=20%><?php echo _TITLE; ?></th>
                                            <th  width=50%><?php echo _TEXT; ?></th>
                                            <th><?php echo _LANDHTML; ?></th>
                                            <th><?php echo _VIEWS; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($data!=false) {
                                      foreach ($data as $key => $value) {
                                        if ($value['status']==1) $status = "<span class=green>"._STATUSON."</span><br>"; else $status = "<span class=red>"._STATUSOFF."</span><br>";
                                        if ($value['pageurl']) $page = "<strong>page:</strong> ".$value['pageurl']."<br>"; else $page = '';
                                         $titl='';$cont='';
                                        $titles = json_decode($value['title'], true);
                                        $content = json_decode($value['content'], true);
                                     
                                        foreach ($titles as $key2 => $value2) {
                                           $titl .= "<strong>$key2</strong>: $value2<br>"; 
                                        }
                                        foreach ($content as $key2 => $value2) {
                                           $cont .= "<strong>$key2</strong>:<br> $value2<br><br>"; 
                                        }
                                        if ($value['section_id']) {
                                        $sec_titles = json_decode($content_section[$value['section_id']]['titles'], true);
                                        } else $sec_titles[$lang] = '-';

                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['cteate_date']."</td>
                                            <td>".$value['pub_date']."</td>
                                            <td>".$value['type']."</td>
                                            <td>".$sec_titles[$lang]."</td>
                                            <td>".$titl."</td>
                                            <td><a href=\"#\" onclick=\"viewblock('block".$key."', this); return false;\" class=small>Показать</a>
                                            <div id=\"block".$key."\" style='display: none;'>
                                            ".$cont."
                                            </div></td>
                                            <td><strong>name:</strong> ".$value['name']."<br>".$page."<br>
                                            </td>
                                            <td>".$value['views']."</td>
                                            <td>".$status."<a href=?m=".$module."&edit=".$key.">"._EDIT."</a><br>
                                            <a href=?m=".$module."&double=".$key.">"._DOUBLE."</a><br>
                                            <a href=?m=".$module."&del=".$key."  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_content / $filenum);
                                 num_page($all_content, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
       <div class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">                   
     <?php
     
$edit_section= intval($_GET['edit_section']);  
$del_section= intval($_GET['del_section']); 

$save = intval($_POST['save_section']);  
$save_edit = intval($_POST['edit']);      
$sorts= intval($_POST['sorts']);  

if ($del_section && $check_login['role']==1) {
$db->sql_query("DELETE FROM content_section WHERE id=".$del_section."") or $stop = mysql_error();
if ($stop) {
status($stop, 'danger');
} else {
status(_DELETEFIELD, 'success');
}
}

if ($save && $check_login['role']==1) {
$title = $_POST['title'];
if ($title) {
foreach ($title as $key => $value) {
$titles[$key] = text_filter($value);
}
$titles = json_encode($titles, JSON_UNESCAPED_UNICODE);
}

    if ($save_edit) {
         
    $db->sql_query("UPDATE content_section SET titles='$titles', sorts='$sorts' WHERE id=".$save_edit."") or $stop = mysqli_error();
  status(_UPDATEFIELD, 'success');
   } else {
     $db->sql_query("INSERT INTO content_section (id, titles, sorts) VALUES (NULL, '$titles', '$sorts')")  or $stop = mysqli_error();
     status(_INSERTFIELD, 'success');
   }
   
} 
     
     
    $content_section = content_section();
     ?>
     
      <?php
                            $title_form = _ADD;
                            $icon = 'plus';
                            if ($edit_section) {
                                $section_edit = content_section("AND id=$edit_section");

                                $title_form = _EDIT;
                                $icon = 'pencil-square-o';
                                $titles = json_decode($section_edit[$edit_section]['titles'], true);
                            } else {
                                $titles = array('ru' => '', 'en' => '');
                            }
   ?>                           
<a href="#" id="show_form2" class="btn btn-link"><i class="fa fa-<?php echo $icon ?>"></i>&nbsp;<?php echo $title_form." "._SECTION; ?></a>
<form action="?m=a_content" method="post" id=form  enctype="multipart/form-data"<?php echo !$save && !$edit_section ? ' style="display: none"' : ''?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="3" align="center">
                                
               
                <tr><td width="20%" valign=top><?php echo _TITLE; ?></td><td>
                 <?php
                 foreach ($titles as $key => $value) {
                 echo "<strong>$key: </strong><br><input type=\"text\" name=\"title[".$key."]\" value=\"".$value."\" size=\"50\" /><br>";
                 }
                ?>
                </td></tr>
               
                <tr><td><?php echo _POSITION; ?></td><td><input type="text" name="sorts" value="<?php echo $section_edit[$edit_section]['sorts']; ?>" size="4" /></td></tr>       
                </table>
                    <input type="hidden" name="edit" value="<?php echo $edit_section; ?>">
                    <input type="hidden" name="save_section" value="1">
                    <input type="hidden" name="tab" value="2">
                    <input type="submit" value="<?php echo _SEND; ?>" class="btn btn-success">             
</form>

                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width=5%>№</th>
                                            <th width=5%><?php echo _POSITION; ?></th>
                                            <th width=50%><?php echo _NAME; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
      <?php
                                    
                                     if ($content_section!=false) {
                                      foreach ($content_section as $key => $value) {
                                        $titl='';

                                        $titles = json_decode($value['titles'], true);
                                        foreach ($titles as $key2 => $value2) {
                                           $titl .= "<strong>$key2</strong>: $value2<br>"; 
                                        }

                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['sorts']."</td>
                                            <td>".$titl."</td>
                                            <td><a href=?m=".$module."&edit_section=".$key."&tab=2>"._EDIT."</a><br>
                                            <a href=?m=".$module."&del_section=".$key."&tab=2  ".confirm().">"._DELETE."</a><br></td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
     
     
            </div>               
                    </div>
     </div>     </div>

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
 $("#show_form2").on("click", function() {
                            var form = $(this).next();
                            form.css("display") == "none" ? form.show() : form.hide();
                            return false;
                        });                       
                        </script>