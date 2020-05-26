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

if (count(get_included_files()) == 1) exit("Direct access not permitted.");
if ($check_login['root']!=1) exit;

?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _LANDINGS1 ?></h4>
</div>
<?php


$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;
$send= intval($_REQUEST['send']);
$create_landing = intval($_REQUEST['create_landing']);
$category = text_filter($_REQUEST['category']);
$viewcategory = text_filter($_REQUEST['viewcategory']);
$lid = intval($_REQUEST['lid']);
$viewlid = intval($_REQUEST['viewlid']);
$lstatus = intval($_REQUEST['lstatus']);
$land_edit = intval($_REQUEST['land_edit']);
$land_update = intval($_REQUEST['land_update']);
$land_delete = intval($_REQUEST['land_delete']);

$newcategory = text_filter($_REQUEST['newcategory']);
if ($newcategory) $category = $newcategory;

$html = isset($_REQUEST['editorHTML']) ? text_filter($_REQUEST['editorHTML'], 3) : '';

if ($land_delete && $check_login['role'] == 1) {
    $db->sql_query("DELETE FROM landings WHERE id=" . $land_delete . "");// or $stop = mysqli_error();  

jset($check_login['id'], _LANDINGDEL.": $land_delete");
if ($check_login['id']!=$check_login['getid']) {
alert(_LANDINGDEL.": $land_delete (user: ".$check_login['login'].")", $check_login['getid']);
 }
 
     status(_LANDINGDEL, 'success'); 
    }
// change landing status
if ($lstatus && $check_login['role'] == 1) {
    if ($lstatus==2) $lstatus=0;
    $db->sql_query("UPDATE landings SET status='$lstatus' WHERE id=" . $lid . "");// or $stop = mysqli_error();  
    
jset($check_login['id'], _CHANGESTATUS.": $lstatus");
if ($check_login['id']!=$check_login['getid']) {
alert(_CHANGESTATUS.": $lstatus (user: ".$check_login['login'].")", $check_login['getid']);
}
 
 
     status(_CHANGESTATUS, 'success'); 
     
    }
    
// add lending
if ($send==1 && $check_login['role'] == 1 && $category) {
    
    if (intval($_FILES['img']['size'])) {
        $directory = "../pic";
        $imgtype = "jpg,jpeg,png";
        $filename = upload($directory, $imgtype, 800000, "", 1000, 1000, 'img');
        $img = "".$directory."/".$filename."";
        $newimg = "preview='$img', ";
    }
   
    if ($land_update) {
        
     $db->sql_query("UPDATE landings SET ".$newimg." category='".$category."', html='" . $html ."' WHERE id=" . $land_update . "") or $stop = mysqli_error();  
    jset($check_login['id'], _LANDUPDATED.": $land_update");
if ($check_login['id']!=$check_login['getid']) {
alert(_LANDUPDATED.": $land_update (user: ".$check_login['login'].")", $check_login['getid']);
}
    
     status(_LANDUPDATED, 'success'); 
     if ($stop) {
      status($stop, 'warning');   
     }
    } else {

    $db->sql_query("INSERT INTO landings (id, category, preview, html, cteated)
VALUES (NULL, '".$category."', '".$img."', '".$html."', now())") or $stop = mysqli_error();  
$land_update = $db->sql_nextid();

    jset($check_login['id'], _LANDADDED.": $category");
if ($check_login['id']!=$check_login['getid']) {
alert(_LANDADDED.": $category (user: ".$check_login['login'].")", $check_login['getid']);
}

 status(_LANDADDED, 'success'); 
     if ($stop) {
      status($stop, 'warning');   
     }
    }
    $land_edit = $land_update;
}

$landings = landings();

$cat_list=array();
if (is_array($landings)) {
foreach ($landings as $key => $value) {
   $cat_list[$value['category']] = $value['category'];
   }
 }                          
?>

<script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>



<div class="content mt-3">
    <div class="animated fadeIn">
        <div class="row">

            <div class="col-md-12">
                <div class="card">

                    <div class="default-tab">
                        <nav class="nav nav-tabs">
                            <a class="nav-link nav-item <?= $tab==1 ? 'active' : '' ?>" name="1" data-toggle="tab" href="#editor"><?php echo _EDITOR; ?></a>
                            <a class="nav-link nav-item <?= $tab==2 ? 'active' : '' ?>" name="2" data-toggle="tab" href="#tocopy"><?php echo _COPY; ?></a>
                            <a class="nav-link nav-item <?= $tab==4 ? 'active' : '' ?>" name="4" data-toggle="tab" href="#landings"><?php echo _LANDINGS; ?></a>
                        </nav>
                    </div>

                    <div class="tab-content" role="tabpanel">
                        <div role="tabpanel" class="tab-pane fade show <?= $tab==1 ? 'active' : '' ?>" id="editor">
                            <div class="card-body">

                                <form id="htmlEditor" name="html" enctype="multipart/form-data" action="?m=a_landings" method="post">
                                                                
                                <?php

                             if ($land_edit) {
                             $category = $landings[$land_edit]['category'];   
                             $html = $landings[$land_edit]['html'];   
                                echo "<strong>"._LANDEDIT." ".$land_edit."</strong> - <a href=\"https://" . $settings['domain_link'] . "/land.php?lid=".$land_edit."&test=1&subid=&tag=&price=0\" target=\"_blank\">"._OPEN."</a><br>";
                                } else {
                                echo "<strong>"._CREATELANDING."</strong><br>";
                                }
                                echo _CATEGORY." <select size=\"1\" name=\"category\">
                                            <option value=\"0\">"._CHOSE."</option>";
                                            
                                            foreach ($cat_list as $key => $val) {
                                                if ($category && $category==$key) $sel = "selected"; else $sel='';
                                                echo "<option value=\"$key\" ".$sel.">$val</option>";
                                            }
                                        
                                 echo "</select> "._OR." <input name=\"newcategory\" type=\"text\" value=\"\" placeholder=\""._ADDNEW."\"><br>";
                                 echo _PREVIEW.": <input type=\"file\" name=\"img\"><br><br>";
                                 
                                  if (!$land_edit) {
                                 $copy_html = get_onerow('value', 'temp_table', "name='copypage'");
                                 $db->sql_query("UPDATE temp_table SET value='' WHERE name='copypage'");

                                 if ($copy_html) $html = $copy_html;
                                 }
                                ?>


                            <textarea name="editorHTML" id="txtDefaultHtmlArea" rows=50 cols=15
                                      style='width: 100%; height: 600px;'
                                      wrap="off"><?php echo $html; ?></textarea>
                                      
                                    <script>
                                        CKEDITOR.replace( 'editorHTML', {
                                            height: 600,
                                            allowedContent: true,
                                            title: false
                                        });
                                    </script>
                                    <input id="landingId" name="land_update" type="hidden" value="<?php echo $land_edit; ?>">
                                    <input name="send" type="hidden" value="1">
                                    <br/>
                                    <br/>
                                    <button type="submit"
                                            class="btn btn-primary btn-landing-save"><?php echo _SEND; ?></button>
                                </form>

                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>" id="tocopy">

                            <div style="padding: 15px">

                                <form method="post" id="page-form">
                                    <input type="hidden" name="landingId" value="<?= $land_edit; ?>"/>
                                    <div class="form-group row">
                                        <label for="pageUrl"
                                               class="col-sm-2 col-form-label"><?php echo _PAGEURL; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="pageUrl" placeholder=""
                                                   name="pageUrl">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPassword"
                                               class="col-sm-2 col-form-label"><?php echo _PROXY; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="proxy" placeholder="ip:port"
                                                   name="proxy">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">&nbsp;</label>
                                        <div class="col-sm-10">
                                            <input style="margin: 0" class="form-check-input position-static"
                                                   type="checkbox" id="removeFiles" name="removeFiles"
                                                   aria-label="<?php echo _DELETE; ?>" value="0">
                                            <label for="removeFiles"
                                                   class="col-sm-3 col-form-label"><?php echo _DELETEALL; ?>
                                                JavaScript</label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">&nbsp;</label>
                                        <div class="col-sm-10">
                                            <input type="submit" class="copy_page btn btn-primary btn-sm"
                                                   value="<?php echo _COPY; ?>"/>

                                            <div class="page-loading"
                                                 style="display: none; margin-top: 10px"><?php echo _COPYING; ?>....
                                            </div>
                                        </div>
                                    </div>
                                </form>


                                <div class="page-status">
                                    <?php status(_COPYLANDOK, 'success'); ?>
                                    <?php status(_COPYLANDNO, 'danger'); ?>
                                    <script type="text/javascript">
                                        $('.page-status .alert').hide();
                                    </script>
                                </div>

                                <div class="page-errors"></div>

                                <script type="text/javascript">

                                    function showStatus(status) {
                                        if (status == 1) {
                                            $(".page-status .alert-success").show();
                                        } else {
                                            $(".page-status .alert-danger").show();
                                        }

                                        $('.page-loading').hide();
                                        $('.copy_page').removeAttr('disabled');
                                    }

                                    $('#removeFiles').on('change', function () {
                                        this.value = this.checked ? 1 : 0;
                                    }).change();

                                    $("#page-form").on("submit", function (event) {

                                        event.preventDefault();
                                        var params = $(this).serialize();

                                        $.ajax({
                                            url: 'copypage.php?type=1',
                                            data: params,
                                            beforeSend: function () {
                                                $('.page-errors').empty();
                                                $('.page-loading').show();
                                                $('.copy_page').attr('disabled', 'disabled');
                                            },
                                            success: function (response) {

                                                response = JSON.parse(response);

                                                if (response && response['pageId']) {

                                                    showStatus(1);
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 2000);
                                                    return;

                                                }

                                                showStatus(0);

                                                if (response['errors'].length > 0) {
                                                    $.each(response['errors'], function (k, error) {
                                                        var element = $('<div class="alert alert-danger"></div>').text(error);
                                                        $('.page-errors').append(element);
                                                    })
                                                }

                                            },
                                            error: function () {
                                                showStatus(0);
                                            }
                                        });
                                    });


                                </script>

                                <style>
                                    #landOptionsBlock {
                                        width: 300px;
                                        margin-right: 100px;
                                    }
                                </style>

                            </div>

                        </div>
                       
                        <div role="tabpanel" class="tab-pane fade show <?= $tab==4 ? 'active' : '' ?>" id="landings">
<div class="card-body card-block">
                        <?php
                        if (is_array($landings)) {
  
                           echo "<form name=\"form\" action=\"?m=".$module."&tab=4\" method=\"post\">";
                           echo "<div class=\"row form-group\">
                           
                            "._CATEGORY.": &nbsp;&nbsp;<select name=\"viewcategory\">
                           <option value=\"0\">"._EVERY."</option>\n";
                           
                         foreach ($cat_list as $key => $value) {
                           if ($viewcategory && $viewcategory==$key) $sel ='selected'; else $sel='';
                           echo "<option value=\"$key\" ".$sel.">".$value."</option>";
                         }
                         if ($viewlid==0) $viewlid='';
                         echo "</select>&nbsp;&nbsp; <input type=\"text\" name=\"viewlid\" placeholder=\"ID\" value=\"".$viewlid."\" size=\"2\" />&nbsp;&nbsp;
                     
                        <button class=\"btn btn-primary btn-sm\">
                                  <i class=\"fa fa-search\"></i>"._SEARCH."
                                 </button>
                                 </div>
                                 </form>";
                            $i=0;
                            foreach ($landings as $key => $value) {
                                if ($viewcategory && $viewcategory!=$value['category']) continue;
                                if ($viewlid && $viewlid!=$key) continue;
                                
                              if ($value['views'] > 0 && $value['subs'] > 0 ) $cr = round(($value['subs'] / $value['views'])*100, 1); else $cr = '-';  
                              
                            if ($i==0) echo "<div class=\"row\">";
                            $style='';

                                if ($value['status']==0) {$act = "<a href=?m=".$module."&tab=4&lstatus=1&lid=".$key.">"._ON."</a>";
                                 $style = "style='background-color: #ffded2;'";
                                 } else $act = "<a href=?m=".$module."&tab=4&lstatus=2&lid=".$key.">"._OFF."</a>";
                                $links = "<br>".$act."<br><a href=?m=".$module."&tab=1&land_edit=".$key.">"._EDIT."</a><br>
                                <a href=?m=".$module."&tab=4&land_delete=".$key." ".confirm().">"._DELETE."</a>";
                   
                    
                      echo "<div class=\"col-lg-3\" >
                        <section class=\"card\" ".$style.">
                            <div class=\"card-body text-secondary\">
                           № ".$key." &nbsp;&nbsp;  "._CATEGORY.": ".$value['category']."<br>
                            <a href=\"https://" . $settings['domain_link'] . "/land.php?lid=".$key."&test=1&subid=&tag=&price=0\" target=\"_blank\"><img src=".$value['preview']." border=0 width=100% class=landimg></a><br>
                            CR: ".$cr."%
                           <div align=\"center\">".$links."</div>
                          
                            </div>
                        </section>
                    </div>";

                            $i++;
                             if ($i==4) {echo "</div>"; $i=0; }
                            }
                            
                        }
                        ?>
                         </div>
                        </div>

                    </div>


                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->


</div><!-- /#right-panel -->

<!-- Right Panel -->
