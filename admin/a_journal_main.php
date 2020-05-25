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


?>


                            <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                             <?php

                            
                             $ip = text_filter($_GET['ip']);
                             $pageurl = text_filter($_GET['pageurl']);
                             $actions = text_filter($_GET['actions']);
                             $user = intval($_GET['user']);
                             $error = intval($_GET['error']);
                             if ($user==0) $user ='';
                             $pagenum  = intval($_GET['page']);
                             

                                     if ($ip) {
                                     $where .= "AND ip LIKE '%$ip%' ";
                                     $dopurl .= "&ip=$ip";
                                     }
                                     if ($pageurl) {
                                     $where .= "AND page LIKE '%$pageurl%' ";
                                     $dopurl .= "&page=$pageurl";
                                     }
                                     if ($actions) {
                                     $where .= "AND action LIKE '%$actions%' ";
                                     $dopurl .= "&action=$actions";
                                     }
                                     if ($user) {
                                     $where .= "AND admin_id='$user' ";
                                     $dopurl .= "&cron_id=$user";
                                     }
                                     if ($error) {
                                     $where .= "AND error='1' ";
                                     $dopurl .= "&error=$error";
                                     $errorch = 'checked';
                                     }
                                     
                                     if (!$pagenum) $pagenum = 1;

                                     $filenum = 50;
                                     $offset = ($pagenum - 1) * $filenum;
                                     
                                      $journal = journal($where, "$offset, $filenum");
                                      $admins = admins();
                                      $all_data = journal($where);
                                      if (is_array($all_data)) $all_data = count($all_data); else $all_data = 0;
                                      
                                     ?>
                             <div class="row form-group">
                             <div class="col col-md-2">user id <input type="text" name="user" value="<?php echo $user ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2">ip <input type="text" name="ip" value="<?php echo $ip ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2"><?php echo _THEPAGE; ?>  <input type="text" name="pageurl" value="<?php echo $pageurl ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2"><?php echo _ACTION; ?>  <input type="text" name="actions" value="<?php echo $actions ?>"  class="form-control form-control-sm"></div>
                             <div class="col col-md-2">error<br /> <input type="checkbox" name="error" value="1" <?php echo $errorch ?> /></div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a>  &nbsp;&nbsp;      <?php echo _ALLFOUND.": <strong>".$all_data."</strong>"; ?> <br />
                             </form>
                             </div>

                            <div class="card-body">

                                <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th>№</th>
                                            <th width=15%><?php echo _DATE; ?></th>
                                            <th width=10%>user</th>
                                            <th width=20%>ip, <?php echo _MACROS12; ?></th>
                                            <th><?php echo _PAGE; ?></th>
                                            <th width=20%><?php echo _ACTION; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    
                                     if ($journal!=false) {
                                      foreach ($journal as $key => $value) {
                                     if ($value['error']==1) {
                                        $error = "<br><span class=red><strong>ERROR</strong></span>";
                                     } else $error='';
                                     
                                     if (mb_strlen($value['page'], "UTF-8") > 100) {
                                     $url = mb_substr($value['page'], 0, 100);
                                     $url .= "... <i class=\"fa fa-angle-double-right\"></i>";
                                     } else $url = $value['page'];
                                     $login = $admins[$value['admin_id']]['login'];
                                     
                                           echo "<tr>
                                            <td>".$key." ".$error."</td>
                                            <td>".$value['date']."</td>
                                            <td>#".$value['admin_id']." (<a href=\"?m=a_users&admin_id=".$value['admin_id']."\" target=\"_blank\">".$login."</a>)</td>
                                            <td>".$value['ip']." (".$value['cc'].")<br><span class=small>".$value['agent']."</span></td>
                                            <td><a href=".$value['page'].">".$url."</a></td>
                                            <td>".$value['action']."</td>
                                        </tr>";
                                      }
                                     }
                                    ?>
                                    </tbody>
                                </table>
                                  <?php
                                $numpages = ceil($all_data / $filenum);
                                 num_page($all_data, $numpages, $filenum, "?m=".$module."" . $dopurl . "&");
                                ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
