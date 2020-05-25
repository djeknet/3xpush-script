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
include("navbar.php");
?>

<div class="content mt-3">
    <div class="animated fadeIn">
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-body card-block">
                        <form name="filters" action="index.php" method="get" id="searchform" class="form-horizontal">
                            <input type="hidden" value="<?= $lang ?>" name="lang">

                            <?php

                            $start_date = text_filter($_GET['start_date']);
                            $end_date = text_filter($_GET['end_date']);
                            $update_from = text_filter($_GET['update_from']);
                            $update_to = text_filter($_GET['update_to']);
                            $sid = intval($_GET['sid']);
                            $sid2 = intval($_GET['sid2']);
                            if ($sid2) $sid = $sid2;
                            $ref = text_filter($_GET['ref']);
                            $sended = intval($_GET['sended']);
                            $sended_type = intval($_GET['sended_type']);
                            $clicks = intval($_GET['clicks']);
                            $clicks_type = intval($_GET['clicks_type']);
                            $status = intval($_GET['status']);

                            if (!$start_date) $start_date = gettime($settings['days_stat']);

                            if ($start_date && $end_date) {
                                $where = "AND createtime >= '" . $start_date . "' AND createtime <= '" . $end_date . "' ";
                            } elseif ($start_date) {
                                $where = "AND createtime >= '" . $start_date . "' ";
                            } elseif ($end_date) {
                                $where = "AND createtime <= '" . $end_date . "' ";
                            }
                            if ($update_from && $update_to) {
                                $where = "AND last_update >= '" . $update_from . "' AND last_update <= '" . $update_to . "' ";
                            } elseif ($update_from) {
                                $where = "AND last_update >= '" . $update_from . "' ";
                            } elseif ($update_to) {
                                $where = "AND last_update <= '" . $update_to . "' ";
                            }
                            if ($sid) {
                                $where .= "AND sid='$sid' ";
                            }
                            if ($ref) {
                                $where .= "AND referer LIKE '%$ref%' ";
                            }

                            if ($sended) {
                                if ($sended_type == 0) {
                                    $sended_types = ">=";
                                } elseif ($sended_type == 1) {
                                    $sended_types = "<=";
                                }
                                $where .= "AND sended $sended_types '$sended'  ";
                                $sended_typesel[$sended_type] = "selected";
                            }
                            if ($clicks) {
                                if ($clicks_type == 0) {
                                    $clicks_types = ">=";
                                } elseif ($clicks_type == 1) {
                                    $clicks_types = "<=";
                                }
                                $where .= "AND clicks $clicks_types '$clicks'  ";
                                $clicks_typesel[$clicks_type] = "selected";
                            }

                            if ($status == 1) {
                                $where .= "AND del=0  ";
                                $statussel[$status] = "selected";
                            } elseif ($status == 2) {
                                $where .= "AND del=1  ";
                                $statussel[$status] = "selected";
                            }
                              if ($check_login['root']==1) {
                             $only_my = intval($_GET['only_my']);
                             $admin_id = text_filter($_GET['admin_id']);
                             } else {
                              $only_my=1;  
                             }
                                     if ($only_my==1) {
                                      $sites=sites("AND admin_id=".$check_login['getid']."");
                                      } else {
                                      $sites=sites();
                                      }
                            
                             if ($check_login['root']==1) {
                                        $filters = array('only_my' => "$only_my", 'admin_id' => "$admin_id", 'sid2' => "$sid");
                                      echo admin_filters($filters);
                                      $admins = admins();
                                      }

                            ?>

                            <div class="row form-group">
                                <div class="col col-md-2"><?php echo _ADDED;
                                    echo " " . _FROM ?> <input type="text" name="start_date" id='datepicker'
                                                               value="<?php echo $start_date ?>"
                                                               class="form-control form-control-sm"></div>
                                <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date"
                                                                                     id='datepicker2'
                                                                                     value="<?php echo $end_date ?>"
                                                                                     class="form-control form-control-sm">
                                </div>
                                <div class="col col-md-2"><?php echo _UPDATE;
                                    echo " " . _FROM ?> <input type="text" name="update_from" id='datepicker3'
                                                               value="<?php echo $update_from ?>"
                                                               class="form-control form-control-sm"></div>
                                <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="update_to"
                                                                                     id='datepicker4'
                                                                                     value="<?php echo $update_to ?>"
                                                                                     class="form-control form-control-sm">
                                </div>
                                 <div class="col col-md-2 inlineblock"><?php echo _SITE ?><br />
                                     <select name="sid" class="form-control-sm form-control col col-md-12">
                                     <option value="0"><?php echo _EVERY ?></option>
                                     <?php
                                     if (is_array($sites)) {
                                       foreach ($sites as $ssid => $value) {
                                        if ($sid && $sid==$ssid) $ch='selected'; else $ch='';
                                        echo "<option value=\"".$ssid."\" ".$ch.">#".$ssid." ".$value['title']."</option>";
                                       }
                                        }

                                     ?>

                                     </select></div>
                                <div class="col col-md-2"><?php echo _REFERER ?> <input type="text" name="ref"
                                                                                        value="<?php echo $ref ?>"
                                                                                        class="form-control form-control-sm">
                                </div>
                                <div class="col col-md-2 inlineblock"><?php echo _STATUS ?><br/>
                                    <select name="status" class="form-control-sm form-control col col-md-12">
                                        <option value="0" <?php echo $statussel[0] ?>><?php echo _EVERY; ?></option>
                                        <option value="1" <?php echo $statussel[1] ?>><?php echo _STATUSACTIVE; ?></option>
                                        <option value="2" <?php echo $statussel[2] ?>><?php echo _STATUSUNSUBS; ?></option>
                                    </select></div>

                                <div class="col col-md-2 inlineblock"><?php echo _SENDED ?><br/>
                                    <select name="sended_type" class="form-control-sm form-control col col-md-4">
                                        <option value="0" <?php echo $sended_typesel[0] ?>>&gt;</option>
                                        <option value="1" <?php echo $sended_typesel[1] ?>>&lt;</option>
                                    </select>
                                    <input type="text" name="sended" value="<?php echo $sended ?>"
                                           class="form-control form-control-sm col col-md-4"></div>
                                <div class="col col-md-2 inlineblock"><?php echo _CLICKS ?><br/>
                                    <select name="clicks_type" class="form-control-sm form-control col col-md-4">
                                        <option value="0" <?php echo $clicks_typesel[0] ?>>&gt;</option>
                                        <option value="1" <?php echo $clicks_typesel[1] ?>>&lt;</option>
                                    </select>
                                    <input type="text" name="clicks" value="<?php echo $clicks ?>"
                                           class="form-control form-control-sm col col-md-4"></div>
                            </div>
                            <script type="text/javascript">
                                $('#datepicker').datepicker({
                                    format: 'yyyy-mm-dd'
                                });
                                $('#datepicker2').datepicker({
                                    format: 'yyyy-mm-dd'
                                });
                                $('#datepicker3').datepicker({
                                    format: 'yyyy-mm-dd'
                                });
                                $('#datepicker4').datepicker({
                                    format: 'yyyy-mm-dd'
                                });
                            </script>
                            <input name="m" type="hidden" value="groupstat">
                            <button class="btn btn-primary btn-sm">
                                <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                            </button>
                            <a href="?m=<?php echo $module ?>" class="btn btn-danger btn-sm"><i
                                        class="fa fa-ban"></i> <?php echo _RESET ?></a>
                        </form>
                    </div>

                    <div class="content mt-3">
                        <div class="animated fadeIn">
                            <div class="row">

                                <script type="text/javascript">

                                    function load_all_stats() {

                                        var items = ["regions", "devices", "os", "browser", "brand", "network"];

                                        var counter = 0;


                                        $.each(items, function (k, item) {
                                            $('#' + item).html('<div class="stat-loading"></div>');
                                        });

                                        function ajax_load_stat_data(index) {

                                            var type = items[index];

                                            var params = $('#searchform').serialize();

                                            $.ajax({
                                                url: 'ajax/groupstat.php?' + params + '&type=' + type,
                                                beforeSend: function () {
                                                },
                                                success: function (response) {

                                                    $('#' + type).empty();

                                                    if (response) {
                                                        $('#' + type).html(response);
                                                    }

                                                    if (++counter < items.length) {
                                                        ajax_load_stat_data(counter);
                                                    }
                                                }
                                            });

                                        }

                                        ajax_load_stat_data(counter);

                                    }

                                    $(document).ready(function () {
                                        load_all_stats();
                                    });

                                </script>

                                <!-- country stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _REGIONSTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="regions"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- device stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _DEVICESTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="devices"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- device stat -->
                                <!-- os stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _OSTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="os"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- os stat -->

                                <!-- browser stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _BROWSERSTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="browser"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- browser stat -->

                                <!-- brand stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _BRANDTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="brand"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- brand stat -->
                                <!-- network stat -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _NETWORKSTAT; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="network"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- network stat -->

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
