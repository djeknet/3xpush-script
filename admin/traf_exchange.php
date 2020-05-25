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
$type = text_filter(($_POST['type'])) ? text_filter($_POST['type']) : text_filter($_GET['type']);
if (!$type) $selmenu['list'] = "active"; else $selmenu[$type] = "active";

$exh_new = traf_exchange_admins("AND owner_id=".$check_login['getid']." AND status=0");
if ($exh_new) $exh_new = "(".count($exh_new).")";

if ($settings['traf_exchange_on']!=1) {
    status(_MODULE_OFF, 'danger');
    exit;
}
?>

    <style>
        .button-actions {
            display: inline-block;
        }
    </style>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _TRAF_EXCH ?></h4>
</div>
                    
        
 <ul class="nav nav-line nav-color-secondary">
	<li class="nav-item">
		<a class="nav-link  <?php echo isset($selmenu['list']) ? $selmenu['list'] : ''; ?>" href="?m=traf_exchange"><?php echo _TRAF_EXCH_LIST; ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo isset($selmenu['my']) ? $selmenu['my'] : ''; ?>" href="?m=traf_exchange&type=my"><?php echo _TRAF_EXCH_MY." ".$exh_new; ?></a>
	</li>
</ul>
       
<?php
if (!$type) {
    include("traf_exchange_list.php");
} elseif ($type=='my') {
    include("traf_exchange_my.php");
} 



?>



