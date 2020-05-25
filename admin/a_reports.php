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
<h4 class="page-title"><?php echo _REPORTS1 ?></h4>
</div>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">

                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                            
                             $description = text_filter($_GET['description']);
                             $status = text_filter($_GET['status']);
                             $type = text_filter($_GET['type']);
                             $cron_id = intval($_GET['cron_id']);
                             if ($admin_id==0) $admin_id ='';
                             $pagenum  = intval($_GET['page']);
                             if (!$pagenum) $pagenum = 1;

                                     if ($description) {
                                     $where .= "AND description LIKE '%$description%' ";
                                     $dopurl .= "&description=$description";
                                     }
                                     if ($cron_id) {
                                     $where .= "AND cron_id='$cron_id' ";
                                     $dopurl .= "&cron_id=$cron_id";
                                     }
                                     if ($status) {
                                     $where .= "AND status='$status' ";
                                     $dopurl .= "&status=$status";
                                     }
                                     if ($type) {
                                     $where .= "AND type='$type' ";
                                     $dopurl .= "&type=$type";
                                     }
                                     if ($id) {
                                     $where .= "AND id=$id ";
                                     }
                                     $filenum = 30;
                                     $offset = ($pagenum - 1) * $filenum;
                                     
                                      $reports = reports($where, 'id', "$offset, $filenum");
                                   
                                      $all_reports = reports($where);
                                      if (is_array($all_reports)) $all_reports = count($all_reports); else $all_reports = 0;
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-2">ID <input type="text" name="id" value="<?php echo $id ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2">Cron id <input type="text" name="cron_id" value="<?php echo $cron_id ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _DESCR ?>  <input type="text" name="description" value="<?php echo $description ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _STATUS ?> 
                                 <select name="status" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                    <option value="success" <?= $status=='success' ? 'selected' : '' ?>>success</option>
                                    <option value="error" <?= $status=='error' ? 'selected' : '' ?>>error</option>
                                    </select>
                                    </div>
                                     <div class="col col-md-2"><?php echo _TYPE ?> 
                                 <select name="type" class="form-control-sm form-control col col-md-12">
                                    <option value="0"><?php echo _EVERY; ?></option>
                                    <option value="cron" <?= $type=='cron' ? 'selected' : '' ?>>cron</option>
                                    <option value="system" <?= $type=='system' ? 'selected' : '' ?>>system</option>
                                    </select>
                                    </div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>  &nbsp;&nbsp;      <?php echo _ALLFOUND.": <strong>".$all_reports."</strong>"; ?> <br />
                             </form>
                             </div>

                            <div class="card-body">

                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th><?php echo _DATE; ?></th>
                                            <th><?php echo _TYPE; ?></th>
                                            <th><?php echo _STATUS; ?></th>
                                            <th><?php echo _DESCR; ?></th>
                                            <th><?php echo _REPORTSTIME; ?></th>
                                            <th>cron id</th>
                                            <th>ip</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($reports!=false) {
                                      foreach ($reports as $key => $value) {
                                     
                                           echo "<tr>
                                            <td>".$key."</td>
                                            <td>".$value['date']."</td>
                                            <td>".$value['type']."</td>
                                            <td>".$value['status']."</td>
                                            <td>".$value['description']."</td>
                                            <td>".$value['how_long']."</td>
                                            <td>".$value['cron_id']."</td>
                                            <td>".$value['ip']."</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_reports / $filenum);
                                 num_page($all_reports, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
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