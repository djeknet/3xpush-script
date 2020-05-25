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
$type = text_filter(($_POST['type'])) ? text_filter($_POST['type']) : text_filter($_GET['type']);
if (!$type) $activelink['log'] = "active"; else $activelink[$type] = "active";

$all_log = get_onerow('COUNT(id)', 'payment', "1");
if (!$all_log) $all_log =0;
$all_out = get_onerow('COUNT(id)', 'payment', "type=3 AND status=0");
if (!$all_out) $all_out =0;
?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _FINANCE ?></h4>
</div>
      

        
         <ul class="nav nav-line nav-color-secondary">
	<li class="nav-item">
    		<a class="nav-link  <?php echo $activelink['log']; ?>" href="?m=a_payment"> <i class="menu-icon fas fa-list"></i> <?php echo _ALLOPERATIONS." ".$all_log; ?></a>
	</li>
    <li class="nav-item">
    		<a class="nav-link  <?php echo $activelink['out']; ?>" href="?m=a_payment&type=out"> <i class="menu-icon fas fa-money-check-alt"></i> <?php echo _WITHDROWALS." ".$all_out; ?></a>
	</li>
    </ul>
    
         <?php
if (!$type) {
    include("a_payment_log.php");
} elseif ($type=='out') {
    include("a_payment_out.php");
} 
