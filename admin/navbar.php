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
$activelink[$module] = "active"; 
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;

$start_date = text_filter($_GET['start_date']);
$end_date = text_filter($_GET['end_date']);
$sid = intval($_GET['sid']);

if ($start_date) $ad_url .= "&start_date=$start_date";
if ($end_date) $ad_url .= "&end_date=$end_date";
if ($sid) $ad_url .= "&sid=$sid";
                            
if ($check_login['root']==1) {
    $advid= intval($_GET['advid']);
    $sid2= intval($_GET['sid2']);
    $only_my = intval($_GET['only_my']);
    $admin_id = text_filter($_GET['admin_id']);
    
    if ($advid) $ad_url .= "&advid=$advid";
    if ($only_my) $ad_url .= "&only_my=$only_my";
    if ($admin_id) $ad_url .= "&admin_id=$admin_id";
    if ($sid2) {$ad_url .= "&sid2=$sid2"; $sid = $sid2;}
}
?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _WM_STAT ?></h4>
</div>

 <ul class="nav nav-line nav-color-secondary">
	<li class="nav-item">
		<a class="nav-link  <?php echo $activelink['daystat']; ?>" href="?m=daystat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-calendar"></i> <?php echo _DAYSTAT ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php echo $activelink['subid']; ?>" href="?m=subid<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-sitemap"></i> <?php echo _BYSUBID ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['landstat']; ?>" href="?m=landstat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-home"></i> <?php echo _BYLANDS ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['browser']; ?>" href="?m=browser<?php echo $ad_url; ?>"> <i class="menu-icon fab fa-chrome"></i> <?php echo _BYBROWSER ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['sitestat']; ?>" href="?m=sitestat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-chart-bar"></i> <?php echo _SITESSTAT ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['advstat']; ?>" href="?m=advstat<?php echo $ad_url; ?>"> <i class="menu-icon far fa-image"></i> <?php echo _ADVSTAT ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['regionstat']; ?>" href="?m=regionstat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-globe-americas"></i> <?php echo _REGIONSTAT ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['groupstat']; ?>" href="?m=groupstat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-layer-group"></i> <?php echo _GROUPSTAT ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['subscribers']; ?>" href="?m=subscribers<?php echo $ad_url; ?>"> <i class="menu-icon far fa-address-card"></i> <?php echo _SUBSCRIBERS ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['sended']; ?>" href="?m=sended<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-rocket"></i> <?php echo _SENDED ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['clicks']; ?>" href="?m=clicks<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-hand-point-up"></i> <?php echo _CLICKS ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['traf_exchange_stat']; ?>" href="?m=traf_exchange_stat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-exchange-alt"></i> <?php echo _EXCHANGE_TITLE_URL ?></a>
	</li>
     <?php
    if ($check_login['root']==1) {
     ?>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['a_nopaystat']; ?>" href="?m=a_nopaystat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-hand-point-up"></i> <?php echo _NOPAY_CLICKS ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link <?php echo $activelink['feedstat']; ?>" href="?m=feedstat<?php echo $ad_url; ?>"> <i class="menu-icon fas fa-sort-amount-down"></i> <?php echo _FEEDSTAT ?></a>
	</li>
     <?php
                    }
                    ?>
</ul>
 

