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

                                     ?>
                             <div class="row form-group">
                              <div class="col col-md-2"><?php echo _DATE; echo " "._FROM ?> <input type="text" name="start_date" id='datepicker' value="<?php echo $start_date ?>"  class="form-control form-control-sm"></div>
                                 <div class="col col-md-2"><?php echo _TILL ?> <input type="text" name="end_date" id='datepicker2' value="<?php echo $end_date ?>" class="form-control form-control-sm"></div>
                                </div>

                                 <input name="m" type="hidden" value="<?php echo $module ?>">
                                 <input name="type" type="hidden" value="feeds">
                                  <button class="btn btn-primary btn-sm">
                                  <i class="fa fa-search"></i> <?php echo _SEARCH ?>
                                 </button>
                                  <a href="?m=<?php echo $module ?>&type=feeds" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> <?php echo _RESET ?></a><br />
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
  <script type="text/javascript">

                                    function load_all_stats() {

                                        var items = ["all"];

                                        var counter = 0;


                                        $.each(items, function (k, item) {
                                            $('#' + item).html('<div class="stat-loading"></div>');
                                        });

                                        function ajax_load_stat_data(index) {

                                            var type = items[index];

                                            var params = $('#searchform').serialize();

                                            $.ajax({
                                                url: 'ajax/sysstat_feeds.php?' + params + '&type=' + type,
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
                                <!-- all stat -->
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong class="card-title"><?php echo _ANALIT_TEXT21; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="all"></div>
                                        </div>
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