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
include("navbar.php");
if ($check_login['root']!=1) exit;
?>


        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">


                    <div class="col-md-12">
                        <div class="card">
                        <div class="card-body card-block">
                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                             $start_date = text_filter($_GET['start_date']);
                             $end_date = text_filter($_GET['end_date']);
                             $sid = intval($_GET['sid']);
                             if (!$sid) $sid = '';
                             $reason = text_filter($_GET['reason']);
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                             if (!$start_date) $start_date = gettime($settings['days_stat']);

                             if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }
                                     if ($sid) {
                                     $where .= "AND sid='$sid' ";
                                     }
                                     if ($reason) {
                                     $where .= "AND reason LIKE '%$reason%' ";
                                     }
                                     
                                     $filenum = 30;
                                      $offset = ($pagenum - 1) * $filenum;

                                     $stat = sites_nopay($where, "$offset, $filenum");
                                     $stat_all = sites_nopay($where);
                                     $allfound = count($stat_all);
                                     

                                     ?>
                             <div class="row form-group">
                                 <div class="col col-md-2"><?php echo _DATEFROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _SITE ?> ID <input type="text" name="sid" value="<?php echo $sid ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-3"><?php echo _REASON ?> <input type="text" name="reason" value="<?php echo $reason ?>"  class="form-control form-control-sm"></div>
                              </div>
                                <script type="text/javascript">
                                 $('#datepicker').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                  $('#datepicker2').datepicker({
                                 format: 'yyyy-mm-dd'
                                 });
                                   </script>
                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a> &nbsp;&nbsp; <?php echo _ALLFOUND.": ".$allfound; ?>
                             </form>
                             </div>
                            <div class="card-body">
                                <table  class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _SITE ?></th>
                                            <th><?php echo _REASON ?></th>
                                            <th><?php echo _CLICKS ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                              $sites = sites();
                                     if ($stat!=false) {
                                      foreach ($stat as $sid => $reasons) {
                                        $siteurl = $sites[$sid]['url'];


                                      	 echo "<tr>
                                            <td><strong>#".$sid." <a href=\"?m=sites&sid=$sid\" target=_blank>".$siteurl."</a></strong></td>
                                            <td>-</td>
                                            <td><strong>".$reasons['ALL']."</strong></td>
                                           </tr>";
                                           foreach ($reasons as $reason => $clicks) {
                                            if ($reason=='ALL') continue;
                                            echo "<tr>
                                            <td>-</td>
                                            <td>".$reason."</td>
                                            <td>".$clicks."</td>
                                           </tr>";
                                            }

                                      }

                                    
                                     }
                                    ?>
                                    </tbody>   
                                </table>
      <?php
                                $numpages = ceil($allfound / $filenum);
                                 num_page($allfound, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->