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

$type=text_filter($_GET['type']);
if (!$type) $selmenu['main'] = "active"; else $selmenu[$type] = "active";

?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _ANALYTICS ?></h4>
</div>


 <ul class="nav nav-line nav-color-secondary">
	<li class="nav-item">
		<a class="nav-link  <?php echo $selmenu['main']; ?>" href="?m=a_analitika"> <i class="menu-icon fas fa-calendar"></i> <?php echo _COMMON ?></a>
	</li>
    <li class="nav-item">
		<a class="nav-link  <?php echo $selmenu['feeds']; ?>" href="?m=a_analitika&type=feeds"> <i class="menu-icon fas fa-calendar"></i> <?php echo _FEEDS ?></a>
	</li>
    </ul>
        
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                         <div class="card-body card-block">
<?php
if (!$type) {
     include("a_analitika_main.php");
} elseif ($type=='feeds') {
     include("a_analitika_feeds.php");
} 

?>
                            
