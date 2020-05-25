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

?>



<?php
$del = intval($_GET['del']);
$edit = intval($_REQUEST['edit']);
$sid = intval($_POST['sid']);
$offer = intval($_POST['offer']);
$max_send = intval($_POST['max_send']);
$new_request = intval($_POST['new_request']);
$traf_id = intval($_POST['traf_id']);
$status = intval($_GET['status']);
$id = intval($_GET['id']);
$sites = sites();
$sites_category = sites_category(); 

// удаление предложения
if ($del && $check_login['role']==1) {
           
$traf_exchange = traf_exchange_admins("AND owner_id=".$check_login['getid']." AND status=1 AND site_id=".$del."");  

if (is_array($traf_exchange)) {
    $stop = _TE_ERR_DEL;
}
if (!$stop) {
$db->sql_query("DELETE FROM traf_exchange WHERE id=".$del." AND admin_id=".$check_login['getid'].""); 
$db->sql_query("DELETE FROM traf_exchange_admins WHERE traf_id=".$del." AND owner_id=".$check_login['getid']."");         
jset($check_login['getid'], _TE_DELETED.": $del");  
status("#".$del." - "._TE_DELETED, 'success'); 
redirect('?m=traf_exchange');   
} else {
status($stop, 'warning');     
}  
}

// изменения статуса предложения
if ($status && $id && $check_login['role']==1) {
if ($status==1) {$status = 1; $info = _OFFER_PLAY;} else {$status = 0; $info = _OFFER_PAUSE;}
$db->sql_query("UPDATE traf_exchange SET status='".$status."' WHERE id=".$id." AND admin_id=".$check_login['getid'].""); 
        
jset($check_login['getid'], $info.": $id");  
status("#".$id." - ".$info, 'success'); 
         
    }
    
// заявка на обмен
if ($new_request==1 && $check_login['role']==1 && $sid && $traf_id) {

$traf_exchange = traf_exchange("AND id=$traf_id"); 
$traf_exchange = $traf_exchange[$traf_id];

$all_subscribers = all_subscribers("AND sid=".$sid."");

$subs_active_proc = subs_effect("AND sid=".$sid."");  // процент активности подписчиков   

// если у меня больше подписчиков, чем у субпартнера, то значит он может рассылать указанным им максимум рассылок
if ($all_subscribers==0 || $all_subscribers > $traf_exchange['max_send']) {
    $max_send = $traf_exchange['max_send'];
} elseif ($all_subscribers < $traf_exchange['max_send']) {
    $max_send = $all_subscribers;
}

// вычитаем из доступного колва рассылок эффективность моих подписок
if ($subs_active_proc) {
    $minus = round(($max_send/100)*$subs_active_proc, 0);
    $max_send = $max_send - $minus;
}

   
$db->sql_query("INSERT INTO traf_exchange_admins (id, traf_id, owner_id, admin_id, site_id, admin_site, max_send) VALUES (NULL, '".$traf_id."', '".$traf_exchange['admin_id']."', '".$check_login['getid']."', '".$traf_exchange['site_id']."', '".$sid."', '".$max_send."')"); 
$admins_lang = admins_lang(); 

$lang = $admins_lang[$traf_exchange['admin_id']];
$url = $sites[$sid]['url'];
$cid = $sites[$sid]['category'];
$category = $sites_category[$cid]['title'][$lang];
if ($lang=='ru') $text = "Новая заявка на обмен трафиком с сайтом: ".$url." [".$category."] http://".$settings['siteurl']."/index.php?m=traf_exchange&type=my"; else $text = "New request for traffic exchange with the site: ".$url."  [".$category."] http://".$settings['siteurl']."/index.php?m=traf_exchange&type=my"; 

alert($text, $traf_exchange['admin_id'], 'info');    // отправляем владельцу оповещение о новой заявке
jset($check_login['getid'], _TRAF_SEND_REQUEST_JOUR.": sid $sid");  
status(_TRAF_SEND_REQUEST_SENDED, 'success'); 
redirect('?m=traf_exchange');        
    }
    
// добавление и обновление предложения
if ($offer==1 && $check_login['role']==1) {
    
  if (!$max_send) $stop = _TRAF_EXCH_WARN1." >1000"; elseif($max_send > 99999999) $max_send = 99999999; 
  
  if (!$stop) {
        if ($edit) {
            
        $db->sql_query("UPDATE traf_exchange SET max_send='".$max_send."', max_send_changed=now() WHERE id=".$edit." AND admin_id=".$check_login['getid'].""); 
        
        jset($check_login['getid'], _TRAF_EXCH_UPD.": $edit");  
        status(_TRAF_EXCH_UPD, 'success'); 
            
        } elseif($sid) {
        $db->sql_query("INSERT INTO traf_exchange (id, admin_id, site_id, max_send, max_send_changed) VALUES (NULL, '".$check_login['getid']."', '".$sid."', '".$max_send."', now())"); 
     
$sites_all = sites("AND type=1 AND admin_id!=".$check_login['getid']."");
$url = $sites[$sid]['url'];
$cid = $sites[$sid]['category'];
                   
// отправляем уведомления всем владельцам сайтов
if (is_array($sites_all)) {
    $admins_lang = admins_lang();
    foreach ($sites_all as $key => $value) {
       $admins[$value['admin_id']] = $value['admin_id'];
    }
  
   foreach ($admins as $key => $value) {
       $lang = $admins_lang[$key];
       $category = $sites_category[$cid]['title'][$lang];
       if ($lang=='ru') $text = "В обмен трафиком добавлен новый сайт: ".$url." [".$category."]"; else $text = "A new site has been added to traffic exchange: ".$url."  [".$category."]"; 

      alert($text, $key, 'info');      
   }
}
        jset($check_login['getid'], _TRAF_EXCH_CREATED.": sid $sid");  
        status(_TRAF_EXCH_CREATED, 'success'); 
        redirect('?m=traf_exchange');
        }

  } else {
    status($stop, 'warning');
  }
    
}

                                if (!$limit) $limit=50; else {
                                       $dopurl .= "&limit=$limit"; 
                                      }
                                     $pagenum  = intval($_GET['page']);                 
                                      if (!$pagenum) $pagenum = 1;
                                     $offset = ($pagenum - 1) * $limit;
                                     
                                     if ($only_my==1) {
                                     $where.= "AND admin_id=".$check_login['getid']." ";
                                     } else {
                                     $where .= "AND (status=1 OR admin_id=".$check_login['getid'].") ";   
                                     }
                                 
                                     $traf_exchange = traf_exchange($where, "$offset, $limit");  
                                     $traf_exchange_all = traf_exchange($where);
                                     if (is_array($traf_exchange_all)) {
                                        
                                     $allfound = count($traf_exchange_all);   
                                     } else  $allfound = 0;
                                     

                                     ?>
         <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">             
                    <div class="card-body card-block">                           
<?php echo status(_TE_INFO, 'info', 'rtafex'); ?>  
<div class="addbutton">
<button type="button" class="btn btn-success mb-1"  data-toggle="modal" data-target="#addnew"  onclick="aj('traf_exch.php','1',1); return false;"><i class="fa fa-plus-square-o"></i>&nbsp; <?php echo _TRAF_EXCH_NEW; ?></button>
</div>                           
<?php echo _OFFERS_ALL.": <strong>".$allfound."</strong>"; ?>

<form action="index.php?m=<?php echo $module; ?>" method="post">

<table class=table  width=100%>
<tbody>
     <thead>
      <tr>
<th  width=10%>№</th>
<th width=15%><?php echo _SITE; ?></th>
<th><?php echo _SUBSCRIBERS2; ?></th>
<th><?php echo _EFFECT." ".tooltip(_TOOLTIPEFFECTSUBS, 'right'); ?></th>
<th><?php echo _MAXSEND." ".tooltip(_TRAF_EXCH_MAXSEND_TOOLTIP2, 'right'); ; ?></th>
<th><?php echo _TRAF_EXCH_ADMINS; ?></th>
<th></th>
</tr>
 </thead>
   <tbody>
   
                        <?php
                        
                        if (is_array($traf_exchange)) {
                            $i=0;
                            foreach ($traf_exchange as $key => $value) {
                                
                         $url = $sites[$value['site_id']]['url'];
                         $cid = $sites[$value['site_id']]['category'];
                         $category = $sites_category[$cid]['title'][$lang];
                         
                         //$subs = all_subscribers("AND sid=".$value['site_id']." AND del=0");
                         $subs = $sites[$value['site_id']]['subscribers'] - $sites[$value['site_id']]['unsubs'];
                         $subs_active_proc = subs_effect("AND sid=".$value['site_id']."");
                      
                         if ($subs_active_proc < 20) {
                            $subs_active_proc = "<span class=red>".$subs_active_proc."%</span>";
                         } elseif ($subs_active_proc > 50) {
                            $subs_active_proc = "<span class=green>".$subs_active_proc."%</span>";
                         } else {
                            $subs_active_proc = "".$subs_active_proc."%";
                         }
                         
                         $exh = traf_exchange_admins("AND site_id=".$value['site_id']." AND status=1");
                         if (is_array($exh)) {
                            $exh_count = count($exh);
                            $exh_count = $exh_count." <a href=#  data-toggle=\"modal\" data-target=\"#exch_stat\" onclick=\"aj('exchange_stat.php','".$value['site_id']."',3); return false;\" title=\""._STAT."\"><i class=\"fa fa-info-circle\"></i></a>";
                            
                             }else $exh_count=0;
                         
                         if ($value['admin_id']==$check_login['getid']) {
                            $pause='';
                            if ($value['status']==1) {
                            $pause = "<a href=\"?m=".$module."&status=2&id=".$key."\" title=\""._OFF."\" class=bigicon><i class=\"fa fa-pause\"></i></a>&nbsp;&nbsp;";
                            } else {
                            $pause = "<a href=\"?m=".$module."&status=1&id=".$key."\" title=\""._ON."\" class=bigicon><i class=\"fa fa-play\"></i></a>&nbsp;&nbsp;";    
                            }
                         $links = $pause."
                         <a href=#  data-toggle=\"modal\" data-target=\"#addnew\" onclick=\"aj('traf_exch.php','1|".$key."',1); return false;\" title=\""._EDIT."\" class=bigicon><i class=\"fa fa-edit\"></i></a>&nbsp;&nbsp;
<a href=?m=".$module."&del=".$key." title=\""._DELETE."\" class=bigicon ".confirm()."><i class=\"fa fa-trash-o\"></i></a>";
                          } else {
                          $links = "<a href=# data-toggle=\"modal\" data-target=\"#request\"  onclick=\"aj('traf_exch.php','2|".$key."',2); return false;\"><span class=\"badge badge-success\"><i class=\"fa fa-exchange\"></i>&nbsp; "._TRAF_SEND_REQUEST."</span></a>";  
                          }
                          
echo "<tr><td width=5%>".$key."</td>
<td><a href=\"/url.php?u=".$url."\" target=\"_blank\">".$url."</a><br><span class=small>".$category."</span></td>
<td>".$subs."</td>
<td>".$subs_active_proc."</td>
<td>".$value['max_send']." / ".$value['today_send']."</td>
<td>".$exh_count."</td>
<td>".$links."</td>
</tr>";     
                           
                            }
                            
                            echo "<div class=numpages>";
                                $numpages = ceil($allfound / $limit);
                                 num_page($allfound, $numpages, $limit, "?m=".$module."" . $dopurl . "&");
                            echo "</div>";       
       
                        } 
                        ?>
 </tbody>
</table>   


  

                        </form>
                         </div>

                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->


</div><!-- /#right-panel -->

<!-- Right Panel -->

      <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _TRAF_EXCH_NEWUPD; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="infomodal" action="?m=<?php echo $module; ?>" method="post">
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
                
       <div class="modal fade" id="request" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _TRAF_SEND_REQUEST2; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="infomodal" action="?m=<?php echo $module; ?>" method="post">
                          <div id="block-2"></div>
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
                
       <div class="modal fade" id="exch_stat" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mediumModalLabel"><?php echo _EXCH_STAT; ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                             <form name="infomodal" action="?m=<?php echo $module; ?>" method="post">
                          <div id="block-3"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _CANCEL; ?></button>
                            </div>
                            <input name="addsite" type="hidden" value="1">
                              </form>
                        </div>
                    </div>
                </div>
                </div>                           