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
<h4 class="page-title"><?php echo _FEEDS ?></h4>
</div>
         <?php

$delete = intval($_POST['delete']);
$edit = intval($_POST['edit']);
$addfeed = intval($_POST['addfeed']);
$name = text_filter($_POST['name']);
$url = text_filter($_POST['url']);
$feed_title = text_filter($_POST['feed_title']);
$feed_body = text_filter($_POST['feed_body']);
$feed_link_click_action = text_filter($_POST['feed_link_click_action']);
$feed_link_icon = text_filter($_POST['feed_link_icon']);
$feed_link_image = text_filter($_POST['feed_link_image']);
$feed_bid = text_filter($_POST['feed_bid']);
$feed_winurl = text_filter($_POST['feed_winurl']);
$convert_rate = text_filter($_POST['convert_rate']);
$feed_button1 = text_filter($_POST['feed_button1']);
$feed_button2 = text_filter($_POST['feed_button2']);

$params = $_POST['params'];
if ($params) {
    $params_arr = array();
    foreach ($params as $key => $value) {
       $key = text_filter($key); 
       $value = text_filter($value); 
     $url = str_replace("{".$key."}", $value, $url);
    }
}
if (!empty($_POST['regions'])) {$regions = implode(',' , $_POST['regions']); $regions = text_filter($regions);}
$site = text_filter($_POST['site']);
$max_send = intval($_POST['max_send']);
$status = intval($_POST['status']);
$bidcoef = intval($_POST['bidcoef']);

if ($addfeed==1 && $check_login['role']==1) {

if (!$name || !$url || !$feed_title || !$feed_body || !$feed_link_click_action || !$feed_link_icon || !$feed_bid) $stop = _FEEDSTEXT1;

if (!$stop) {

$db->sql_query('INSERT INTO feeds (id, admin_id, name, url,  status, feed_title, feed_body, feed_link_click_action, feed_link_icon, feed_link_image, feed_bid, convert_rate, feed_winurl, regions, max_send, coef, site, feed_button1, feed_button2)
VALUES (NULL, '.$check_login['getid'].', "'.$name.'", "'.$url.'", '.$status.', "'.$feed_title.'", "'.$feed_body.'", "'.$feed_link_click_action.'", "'.$feed_link_icon.'", "'.$feed_link_image.'", "'.$feed_bid.'", "'.$convert_rate.'", "'.$feed_winurl.'", "'.$regions.'", '.$max_send.', '.$bidcoef.', "'.$site.'", "'.$feed_button1.'", "'.$feed_button2.'")') or $error = mysqli_error();
if ($error) {
jset($check_login['id'], $error, 1);   
status($error, 'danger');
} else {
jset($check_login['id'], _FEED." "._SMALL_UPDATED);
if ($check_login['id']!=$check_login['getid']) {
 alert(_FEED." "._SMALL_UPDATED.": ".$name." (user: ".$check_login['login'].")", $check_login['getid']); 
  } 
status(_FEED." "._SMALL_UPDATED, 'success');
}
} else {
status($stop, 'danger');
}
}

if ($delete && $check_login['role']==1) {
$db->sql_query('DELETE FROM feeds WHERE id='.$delete.' AND admin_id='.$check_login['getid'].'') or $error = mysql_error();
if ($error) {
jset($check_login['id'], $error, 1);  
status($error, 'danger');
} else {
jset($check_login['id'], _FEED." "._SMALL_DELETED); 
if ($check_login['id']!=$check_login['getid']) {
 alert(_FEED." "._SMALL_DELETED.": #".$delete." (user: ".$check_login['login'].")", $check_login['getid']); 
  }  
status(_FEED." "._SMALL_DELETED, 'success');
}
}

if ($edit && $check_login['role']==1) {

if (!$name || !$url || !$feed_title || !$feed_body || !$feed_link_click_action || !$feed_link_icon || !$feed_bid) $stop = _FEEDSTEXT1;

 if (!$stop) {
$db->sql_query('UPDATE feeds SET feed_button1="'.$feed_button1.'", feed_button2="'.$feed_button2.'", site="'.$site.'", coef="'.$bidcoef.'", name="'.$name.'", url="'.$url.'", status='.$status.', feed_title="'.$feed_title.'", feed_body="'.$feed_body.'", feed_link_click_action="'.$feed_link_click_action.'", feed_link_icon="'.$feed_link_icon.'", feed_link_image="'.$feed_link_image.'", feed_bid="'.$feed_bid.'", convert_rate="'.$convert_rate.'", feed_winurl="'.$feed_winurl.'", regions="'.$regions.'", max_send="'.$max_send.'" WHERE id='.$edit.' AND admin_id='.$check_login['getid'].'') or $error = mysqli_error();
if ($error) {
jset($check_login['id'], $error, 1);    
status($error, 'danger');
} else {
jset($check_login['id'], "#$edit - "._FEED." "._SMALL_UPDATED);   
if ($check_login['id']!=$check_login['getid']) {
 alert(_FEED." "._SMALL_UPDATED.": #".$edit." (user: ".$check_login['login'].")", $check_login['getid']); 
  }  
status("#$edit - "._FEED." "._SMALL_UPDATED, 'success');
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
                         <div class="addbutton">
                         <button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addform"  onclick="aj('form_feed.php','0',1); return false;"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _ADDFEED; ?></button>
                          </div><br />
                          
                                          <?php
                
                                   if ($check_login['root']==1) {
                                       $only_my = intval($_GET['only_my']);
                                       $admin_id = text_filter($_GET['admin_id']);
                                       } else {
                                       $only_my=1;  
                                      }
                                     if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     $dopurl .= "&admin_id=$admin_id";
                                     }
                                      if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      } 
                                     $feeds = feeds($where);
                     
                     
                    if ($check_login['root']==1) {
    echo "<form name=\"filters\" action=\"index.php\" method=\"get\" id=\"searchform\" class=\"form-horizontal\">";
                   $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id");
                       echo admin_filters($filters);
                     $admins = admins();
         echo "<input name=\"m\" type=\"hidden\" value=\"feeds\">
             <button class=\"btn btn-primary btn-sm\">
                                  <i class=\"fa fa-search\"></i> "._SEARCH."
                                 </button>
       <a href=\"?m=$module\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-ban\"></i> "._RESET."</a>";             
          echo "</form>";           
                        }
                                      
                       ?> 
                            <div class="card-body">
                            
                            <a href="#" data-toggle="modal" data-target="#test-feed" onclick="aj('feed_test.php','',3); return false;"><?php echo _FEEDS_TEXT1; ?></a>
                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _NAME; ?></th>
                                            <th><?php echo _LINK; ?></th>
                                            <th><?php echo _OPTIONS; ?></th>
                                            <th><?php echo _STAT; ?></th>
                                            <th><?php echo _STATUS; ?></th>
                                            <th><?php echo _ACTIONS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                  
                                     if ($feeds!=false) {
                                      foreach ($feeds as $key => $value) {
                                       if ($value['status']==1) $status = "<b class=green>"._STATUSON."</b>"; else $status = "<b class=red>"._STATUSOFF."</b>";
                                       if (!$value['convert_rate']) $value['convert_rate']='-';
                                       if ($value['site']) $value['name'] = "<a href=/url.php?u=".$value['site']." target=_blank>".$value['name']."</a>";
                                      	 echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['name']."</td>
                                            <td><input name=\"Name\" type=\"text\" value=\"".$value['url']."\" size=40></td>
                                            <td><b>title:</b> ".$value['feed_title'].", <b>body:</b> ".$value['feed_body'].", <b>link:</b> ".$value['feed_link_click_action'].",
                                            <b>icon:</b> ".$value['feed_link_icon'].", <b>image:</b> ".$value['feed_link_image'].", <b>winurl:</b> ".$value['feed_winurl'].",
                                            <b>bid:</b> ".$value['feed_bid']."<br />";
                                            if ($value['convert_rate']) echo "<b>"._RATE.":</b> ".$value['convert_rate']."<br />";
                                            if ($value['regions']) echo "<b>"._REGIONS.":</b> ".$value['regions']."<br />";
                                            if ($value['coef']) echo "<b>"._BIDCOEF.":</b> -".$value['coef']."%<br />";

                                            echo "</td>
                                            <td>"._SENDEDALL.": ".$value['total_sended']."<br />
                                            "._SENDEDMAX.": ".$value['max_send']."<br />
                                            <a href=?m=feedstat&fid=".$key." target=_blank>"._DAYSTAT."</a><br />
                                            <a href=?m=advstat&feed_id=".$key." target=_blank>"._ADVSTAT."</a><br />
                                            <a href=?m=sended&fid=".$key." target=_blank>"._SENDED."</a></td>
                                            <td>".$status."</td>
                                            <td>
                                            <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#edit\" onclick=\"aj('form_feed.php', '".$key."', '2'); return false;\">"._EDIT."</button><br />
                                            <button type=\"button\" class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#del".$key."\">"._DELETE."</button><br /><br />
                                            <button type=\"button\" class=\"btn btn-link\" onclick=\"aj('testfeed.php', '".$key."', '99".$key."'); return false;\">"._TEST."</button>
                                            <div id=\"block-99".$key."\"></div></td>
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
      <?php
$isolist = isolist();
         if ($feeds!=false) {
             foreach ($feeds as $key => $value) {
              modal(_DELETEFEED." ".$value['name'], _DELETEFEED." ".$value['name']."? <input name=\"delete\" type=\"hidden\" value=\"".$key."\">", 1, "del".$key, "?m=feeds");
              }
              }

      ?>

 <script src="vendors/chosen/chosen.jquery.min.js"></script>
 
      <div class="modal fade" id="addform" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _ADDFEED; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=feeds" method="post">
                               <div id="block-1">...</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                            <input name="addfeed" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                
            <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _EDITFEED; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=feeds" method="post">
                               <div id="block-2">...</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                                <button type="submit" class="btn btn-primary"><?php echo _SEND; ?></button>
                            </div>
                              </form>
                        </div>
                    </div>
                </div>   
                
                <div class="modal fade" id="test-feed" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _FEEDS_TEXT1; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="addsite" action="?m=feeds" method="post">
                               <div id="block-3">...</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                            </div>
                              </form>
                        </div>
                    </div>
                </div>       
 <script>
    jQuery(document).ready(function() {
        jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
         });

 $('#block-1').on('click', '#ajax', function(){
     jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});
 $('#block-2').on('click', '#ajax', function(){
     jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});
$( document ).ajaxStop(function() {
  jQuery(".select2").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "400px"
        });
});

</script>