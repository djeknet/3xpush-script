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
 if ($settings['allow_referal']==0) {
    status('module off', 'warning');
    exit;
 }
?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _REFERALS ?></h4>
</div>      
        
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">


                            <div class="card-body">
                            <?php
                            status(_REF_INFO, 'info');
                            
                            if ($check_login['ref_active']==1 || $settings['referal_manual']==0) {
                                echo _REF_LINK.": ";
                           echo " <input name=\"Name\" size=\"50\" onFocus=\"this.select()\" onClick=\"this.select()\" type=\"text\" value=\"https://".$settings['siteurl']."/?r=".$check_login['getid']."\"><br>";     
                            echo "<strong>"._REF_PROC."</strong>: < 30 = 5%, > 30 = 6%, > 50 = 7%, > 70 = 8%, > 100 = 10%<br><br>";
                            }
                            
                             $today = date("Y-m-d");
                             $active=0;
                            $referals = referals("AND a.owner=".$check_login['getid']."");
                            $refstat = refstat("AND admin_id=".$check_login['getid']."");
                             if ($referals!=false) {
                                $allref = count($referals);
                                      foreach ($referals as $key => $value) {
                                         if ($value['status']==1 && stripos($value['balance_edit'], $today) !== false) {
                                            $active++;
                                         }
                                        }
                                        $proc = round(($active/$allref)*100, 0);
                                        } else {
                                            $allref=0;
                                        }
                            echo _ALL_REFS.": ".$allref." &nbsp;&nbsp; "._ACTIVE.": ".$active." (".$proc."%)<br><br>";                         
                            ?>
                            
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th width=5%>№</th>
                                            <th width=5%>ID</th>
                                            <th width=7%><?php echo _DATE; ?></th>
                                            <th width=30%><?php echo _REFERER; ?></th>
                                            <th width=10%><?php echo _MONEY; ?></th>
                                            <th width=10%><?php echo _STATUS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                     if ($referals!=false) {
                                      foreach ($referals as $key => $value) {
                                        
                                       if ($value['date']==$today) $new = '<span class="badge badge-danger">new</span>'; else $new = '';
                            
                                       if ($value['status']==1 && stripos($value['last_login'], $today) !== false) $status = "<span class=green>"._WORKS."</span>"; else $status = "<span class=gray>"._NOTWORKS."</span>";
                                       if (!$value['reg_from']) $value['reg_from'] = "-";
                                       $value['money'] = moneyformat($value['money']);
                                      	 echo "<tr>
                                         <td>".$key."</td>
                                         <td>".$value['admin_id']."</td>
                                         <td>".$value['date']." ".$new."</td>
                                         <td>".$value['reg_from']."</td>
                                         <td>".$value['money']."$</td>
                                         <td>".$status." ".tooltip(_NOTWORKS_TOOLTIP, 'left')."</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table><br /><br />
                                <?php echo "<b>"._REF_DAYSTAT."</b>"; ?>
                                  <br /><table id="bootstrap-data-table-export2" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width=20%><?php echo _DATE; ?></th>
                                            <th width=20%><?php echo _ALL_REFS; ?></th>
                                            <th width=20%><?php echo _ACTIVE; ?></th>
                                            <th width=20%><?php echo _PROC; ?></th>
                                            <th width=20%><?php echo _MONEY; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php
                                     if ($refstat!=false) {
                                      foreach ($refstat as $key => $value) {

                                       $value['money'] = moneyformat($value['money']);
                                      	 echo "<tr>
                                         <td>".$value['date']."</td>
                                         <td>".$value['all_users']."</td>
                                         <td>".$value['active_users']."</td>
                                         <td>".$value['proc']."%</td>
                                         <td>".$value['money']."$</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table> 
                                
                              <script>
                              $(document).ready( function () {
    $('#bootstrap-data-table-export2').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'pdf', 'colvis'],
         language: {
            buttons: {
                colvis: '<?php echo _COLUMS; ?>'
            }
        },
        stateSave: true,
           scrollX: true
        
    });
} 

);
                              </script>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->