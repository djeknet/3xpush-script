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


                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $start_date = text_filter($_GET['start_date']);
                             $end_date = text_filter($_GET['end_date']);
                              if (!$start_date) $start_date = gettime($settings['days_stat']);

                              if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }

                             
                                      $stat = mails_stat($where, "date");

                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-3"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                 <input name="type" type="hidden" value="stat">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>&type=stat" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>
                             </form>
                               <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                             </div>
                            <div class="card-body">
  
                             <form name="filters" action="index.php?m=<?php echo $module; ?>" method="post" id="searchform" class="form-horizontal">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo _DATE; ?></th>
                                            <th><?php echo _SENDEDALL; ?></th>
                                            <th><?php echo _SEND_ERROR; ?></th>
                                            <th><?php echo _SEND_VIEWS; ?></th>
                                            <th>% <?php echo _VIEWS_PROC; ?></th>
                                            <th><?php echo _CLICKS; ?></th>
                                            <th><?php echo _UNSUBS; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if (is_array($stat)) {
                                      foreach ($stat as $key => $value) {  
                                        if ($key=='ALL') continue;
                                        if ($value['views'] >0 && $value['sended'] > 0) {
                                            $proc = round(($value['views']/$value['sended'])*100,0);
                                        } else $proc=0;
                                        
                                        if ($value['sended']>0 && $value['unsubs']>0) $unsubs_proc = round(($value['unsubs'] / $value['sended'])*100, 0); else $unsubs_proc=0;
                                        if ($value['views']>0 && $value['clicks']>0) $ctr = round(($value['clicks'] / $value['views'])*100, 0); else $ctr=0;


                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['sended']."</td>
                                            <td>".$value['error_send']."</td>
                                            <td>".$value['views']."</td>
                                            <td>".$proc."%</td>
                                            <td>".$value['clicks']."  <span class=table_proc>".$ctr."%</span></td>
                                            <td>".$value['unsubs']."  <span class=table_proc title=\""._UNSUBSPROC."\">".$unsubs_proc."%</span></td>
                                        </tr>";
                                      }
                                      
                                      if ($stat['ALL']['views'] >0 && $stat['ALL']['sended'] > 0) {
                                            $proc = round(($stat['ALL']['views']/$stat['ALL']['sended'])*100,0);
                                        } else $proc=0;
                                      if ($stat['ALL']['unsubs']>0 && $stat['ALL']['sended']>0) $unsubs_proc = round(($stat['ALL']['unsubs'] / $stat['ALL']['sended'])*100, 0); else $unsubs_proc=0;
                                      if ($stat['ALL']['views']>0 && $stat['ALL']['clicks']>0) $ctr = round(($stat['ALL']['clicks'] / $stat['ALL']['views'])*100, 0); else $ctr=0;
  
                                     }
                                    ?>
                                    </tbody>
                                      <tfoot>
                                        <tr>
                                            <th><?php echo _ALL ?></th>
                                            <th><?php echo isset($stat['ALL']['sended']) ? $stat['ALL']['sended'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['error_send']) ? $stat['ALL']['error_send'] : 0; ?></th>
                                            <th><?php echo isset($stat['ALL']['views']) ? $stat['ALL']['views'] : 0; ?></th>
                                            <th><?php echo $proc; ?>%</th>
                                            <th><?php echo isset($stat['ALL']['clicks']) ? $stat['ALL']['clicks'] : 0; ?>  <span class=table_proc><?php echo $ctr; ?>%</span></th>
                                            <th><?php echo isset($stat['ALL']['unsubs']) ? $stat['ALL']['unsubs'] : 0; ?> <span class=table_proc title="<?php echo _UNSUBSPROC; ?>"><?php echo $unsubs_proc; ?>%</span></th>
                                        </tr>
                                    </tfoot>
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

