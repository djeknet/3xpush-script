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

$approve = intval($_GET['approve']);
$cancel = intval($_GET['cancel']);
$sites = sites();
 
 // отмена заявки
if ($cancel && $check_login['role']==1) {
$traf_exchange_admins = traf_exchange_admins("AND id=".$cancel." AND owner_id=".$check_login['getid'].""); 
if (is_array($traf_exchange_admins)) {
  
   // сколько получил рассылок
   $in = $traf_exchange_admins[$cancel]['sended']; 
   $admin_site = $traf_exchange_admins[$cancel]['admin_site'];
   $sid = $traf_exchange_admins[$cancel]['site_id'];
   $my_url = $sites[$sid]['url']; 
   $url = $sites[$admin_site]['url'];
   
   // сколько отправил рассылок
   $out = traf_exchange_admins("AND site_id=".$traf_exchange_admins[$cancel]['admin_site']." AND admin_site=".$traf_exchange_admins[$cancel]['site_id']."");
    if (is_array($out)) {
                         foreach ($out as $key1 => $value1) {
                            $out = $value1['sended'];
                            break;
                         }
                         } else { $out = 0; }
                         
   if ($out > $in) {
    $stop = _TRAF_EXCH_STOP1;
   }  
   
   if (!$stop) {
$text = str_replace('URLS', "$my_url - $url", _TRAF_EXCH_CLOSED); 
$text2 = str_replace('URLS', "$my_url - $url", _TRAF_EXCH_CLOSED2);    
   
$db->sql_query("UPDATE traf_exchange_admins SET status='2' WHERE id=".$cancel." AND owner_id=".$check_login['getid'].""); 
  
alert($text, $traf_exchange_admins[$cancel]['admin_id'], 'info');        
jset($check_login['getid'], _TRAF_EXCH_CLOSED);  
status($text2, 'success');  
    
   } else {
  status($stop, 'warning');      
   }
    } else {
  status('Error ID', 'danger');        
    }
    
}

// подтверждение заявки
if ($approve && $check_login['role']==1) {

$traf_exchange_admins = traf_exchange_admins("AND id=".$approve." AND owner_id=".$check_login['getid'].""); 
if (is_array($traf_exchange_admins)) {
$sid = $traf_exchange_admins[$approve]['site_id']; 
$admin_site = $traf_exchange_admins[$approve]['admin_site']; 
$traf_id = $traf_exchange_admins[$approve]['traf_id']; 
$url = $sites[$sid]['url'];    

$traf_exchange = traf_exchange("AND id=$traf_id"); 
$traf_exchange = $traf_exchange[$traf_id];

$all_subscribers = all_subscribers("AND sid=".$admin_site."");

$subs_active_proc = subs_effect("AND sid=".$admin_site."");  // процент активности подписчиков   

// если у него больше подписчиков, чем я указал лимит по своему сайту, то значит он может рассылать указанный мной максимум рассылок
if ($all_subscribers==0 || $all_subscribers > $traf_exchange['max_send']) {
    $max_send = $traf_exchange['max_send'];
} elseif ($all_subscribers < $traf_exchange['max_send']) {
    $max_send = $all_subscribers;
}

// прибавляем процент в заивисмости от эффективности его подписок
if ($max_send > 0 && $subs_active_proc) {
    $plus = round(($max_send/100)*$subs_active_proc, 0);
    $max_send = $max_send + $plus;
}
if ($max_send < 0) {
    $stop = 'MAX SEND error';
}
if (!$stop) { 
$db->sql_query("UPDATE traf_exchange_admins SET status='1' WHERE id=".$approve." AND owner_id=".$check_login['getid']."");     
$db->sql_query("INSERT INTO traf_exchange_admins (id, traf_id, owner_id, admin_id, site_id, admin_site, max_send, status) VALUES (NULL, '".$traf_id."', '".$traf_exchange['admin_id']."', '".$check_login['getid']."', '".$admin_site."', '".$sid."', '".$max_send."', 1)"); 
    
alert(_TRAF_EXCH_APPROVE.". "._SITE.": ".$url, $traf_exchange_admins[$approve]['admin_id'], 'info');        
jset($check_login['getid'], _TRAF_EXCH_APPROVE.": $approve");  
status(_TRAF_EXCH_APPROVE, 'success');  
} else {
   status($stop, 'danger');  
}   
} else {
    status('Error ID', 'danger');
}
}


                                     if (!$limit) $limit=50; else {
                                       $dopurl .= "&limit=$limit"; 
                                      }
                                     $pagenum  = intval($_GET['page']);                 
                                      if (!$pagenum) $pagenum = 1;
                                     $offset = ($pagenum - 1) * $limit;
                                     
                                     $where.= "AND owner_id=".$check_login['getid']."";
                                 
                                     $traf_exchange = traf_exchange_admins($where, "$offset, $limit");  
                                     $creativ_list_all = traf_exchange_admins($where);
                                     if (is_array($creativ_list_all)) {
                                        
                                     $allfound = count($creativ_list_all);   
                                     } else  $allfound = 0;
                                     
                                     
                                     $sites_category = sites_category(); 


                                     ?>
         <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">             
                    <div class="card-body card-block">                           
                          

<form action="index.php?m=<?php echo $module; ?>" method="post">

<table class=table  width=100%>
<tbody>
     <thead>
      <tr>
<th  width=5%>№</th>
<th width=10%><?php echo _SITE; ?></th>
<th width=15%><?php echo _PARTNER_SITE; ?></th>
<th><?php echo _SUBSCRIBERS2; ?></th>
<th><?php echo _EFFECT." ".tooltip(_TOOLTIPEFFECTSUBS, 'right'); ?></th>
<th><?php echo _MAXSEND." ".tooltip(_TRAF_EXCH_MAXSEND_TOOLTIP3, 'right'); ; ?></th>
<th><?php echo _TRAF_EXCH_ADMINS; ?></th>
<th><?php echo _TRAF_EXCH_STATS; ?></th>
<th><?php echo _ACTIONS; ?></th>
<th></th>
</tr>
 </thead>
   <tbody>
   
                        <?php
                        
                        if (is_array($traf_exchange)) {
                            $i=0;
                            foreach ($traf_exchange as $key => $value) {
                         
                         $my_url = $sites[$value['site_id']]['url']; 
                               
                         $url = $sites[$value['admin_site']]['url'];
                         $cid = $sites[$value['admin_site']]['category'];
                         $category = $sites_category[$cid]['title'][$lang];
                         
                         $subs = $sites[$value['admin_site']]['subscribers'] - $sites[$value['admin_site']]['unsubs'];
                         $subs_active_proc = subs_effect("AND sid=".$value['admin_site']."");
                      
                         if ($subs_active_proc < 20) {
                            $subs_active_proc = "<span class=red>".$subs_active_proc."%</span>";
                         } elseif ($subs_active_proc > 50) {
                            $subs_active_proc = "<span class=green>".$subs_active_proc."%</span>";
                         } else {
                            $subs_active_proc = "".$subs_active_proc."%";
                         }
                         
                         $exh = traf_exchange_admins("AND site_id=".$value['admin_site']." AND status=1");
                         $out = traf_exchange_admins("AND site_id=".$value['admin_site']." AND admin_site=".$value['site_id']."");
                         if (is_array($out)) {
                         foreach ($out as $key1 => $value1) {
                            $out = $value1['sended'];
                            break;
                         }
                         } else { $out = 0; }
                         
                         if (is_array($exh)) {
                            $exh_count = count($exh);
                            $exh_count = $exh_count." <a href=#  data-toggle=\"modal\" data-target=\"#exch_stat\" onclick=\"aj('exchange_stat.php','".$value['admin_site']."',3); return false;\" title=\""._STAT."\"><i class=\"fa fa-info-circle\"></i></a>";
                            
                             }else $exh_count=0;
                         
                        if ($value['status']==0) {
                         $links = "
<a href=?m=".$module."&type=my&approve=".$key."><span class=\"badge badge-success upper\"><i class=\"fa fa-check\"></i>&nbsp;"._APPROVE."</span></a> <br>
<a href=?m=".$module."&type=my&decline=".$key."><span class=\"badge badge-danger upper\"><i class=\"fa fa-times\"></i>&nbsp;"._DENY."</span></a>";

                         } elseif ($value['status']==1){
                         $links = "
<a href=?m=".$module."&type=my&cancel=".$key."><span class=\"badge badge-secondary upper\" ".confirm()."><i class=\"fa fa-times-circle\"></i>&nbsp;"._CANCEL2."</span></a>";                            
                         } else {
 $links = "<span class=\"badge badge-secondary upper\"><i class=\"fa fa-times-circle\"></i>&nbsp;"._MAILSCANCEL."</span><br>
 <a href=?m=".$module."&type=my&approve=".$key."><span class=\"badge badge-success upper\"><i class=\"fa fa-check\"></i>&nbsp;"._APPROVE."</span></a>";                            
                         }
                          
echo "<tr><td width=5%>".$key."</td>
<td  width=10%>".$my_url."</td>
<td><a href=\"/url.php?u=".$url."\" target=\"_blank\">".$url."</a><br><span class=small>".$category."</span></td>
<td>".$subs."</td>
<td>".$subs_active_proc."</td>
<td>".$value['max_send']." / ".$value['sended_today']."</td>
<td>".$exh_count."</td>
<td>In: ".$value['sended']."<br>
Out: ".$out."<br>
<a href=\"?m=traf_exchange_stat&sid=".$value['site_id']."&partner_sid=".$value['admin_site']."\" target=\"_blank\">"._DAYSTAT."</a></td>
<td>".$links."</td>
</tr>";     
                           
                            }
                            
                            echo "<div class=numpages>";
                                $numpages = ceil($allfound / $limit);
                                 num_page($allfound, $numpages, $limit, "?m=".$module."" . $dopurl . "&");
                            echo "</div>";       
       
                        } else {
                            status(_NOTHINGFOUND, 'info');
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