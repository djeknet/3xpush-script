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

$traf_exchange = traf_exchange("AND status=1");  
if (is_array($traf_exchange)) $traf_exchange_count = " <span class=\"badge badge-info\">".count($traf_exchange)."</span>"; else $traf_exchange_count='';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo $settings['sitename']; ?></title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../favicon.png" type="image/x-icon"/>
	
	<!-- Fonts and icons -->
    <script src="assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/ajax.js"></script>
    <link rel="stylesheet" href="js/flatpickr/flatpickr.min.css">
    <script src="js/flatpickr/flatpickr.js"></script>
    <script src="js/flatpickr/ru.js"></script>
    <script src="js/form_popup.js?v=0.0.1"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/jquery.blink.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="assets/js/plugin/select2/select2.full.min.js"></script>
    <script src="vendors/chosen/chosen.jquery.min.js"></script>
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>
    <script src="assets/js/init-scripts/chart-js/chartjs-init.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>
<script>
        
function setCookie (name, value, expires, path, domain, secure) {
      document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}     
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}
</script>
	<!-- CSS Files -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/atlantis.css">
	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="assets/css/demo.css">
    <link rel="stylesheet" href="css/main.css<?= '?v=' . $version; ?>">
    <link rel="stylesheet" href="css/tooltip.css">
    <link rel="stylesheet" href="css/datepicker.css">
    <link rel="stylesheet" href="css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="vendors/chosen/chosen.min.css">
</head>
<body>

	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				
				<a href="../index.html" class="logo">
					<img src="images/logo.png" alt="navbar brand" class="navbar-brand" style="width: 105px;">
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
				
				<div class="container-fluid">
                <div class="top-text">
                <font class=small3>&nbsp;&nbsp;&nbsp;<i class="fa fa-clock-o"></i> <?php

echo date("Y-m-d H:i:s");
?></font>
<div class="hello">
            <?php

            if ($check_login['name']) $user_name = $check_login['name']; else  $user_name = $check_login['login'];
            echo _HELLO.", ".$user_name."&nbsp;&nbsp;&nbsp;";
            if ($check_login['is_virtual']) {
                $user_login = admins("AND id=".$check_login['getid']."");
            echo  "[ "._UNDER_LOGIN." <a href=?m=a_users&admin_id=".$check_login['getid']."><strong>".$user_login[$check_login['getid']]['login']."</strong></a> - <a href=?virtual_exit=1>"._LOGOUT."</a> ]&nbsp;&nbsp;&nbsp;";
            }
            $balance = moneyformat($balance);
            echo "<b><a href=?m=balance>"._BALANCE."</a>: $".$balance."</b>";

            ?>

    <script>var userBalance = parseFloat("<?= $balance; ?>");</script>
    </div>
    </div>
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item toggle-nav-search hidden-caret">
							<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
								<i class="fa fa-search"></i>
							</a>
						</li>
						   <?php

                          $alerts = alerts("AND view=0 AND admin_id=".$check_login['id']."", 20);
                          if (is_array($alerts)) {
                          $count = count($alerts);
                          $count_view = "<span class=\"notification\">".$count."</span>";

                          } else { $count=0; }
                          ?>
						<li class="nav-item dropdown hidden-caret">
							<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-bell"></i>
								<?php echo $count_view; ?>
							</a>
							<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
								<li>
									<div class="dropdown-title">You have <?php echo $count; ?> new notification</div>
								</li>
								<li>
									<div class="notif-center">
                                    
                                 <?php

                                 if (is_array($alerts)) {
                                 foreach ($alerts as $key => $value) {
                                   if ($value['type']=='warning') {$type_icon = 'exclamation-triangle'; }else {$type_icon = 'info-circle';}
                                   $value['text'] = text_filter($value['text']);
                                     if (mb_strlen($value['text'], "UTF-8") > 100) {
                $value['text'] = mb_substr($value['text'], 0, 100);
                $value['text'] .= "... <i class=\"fa fa-angle-double-right\"></i>";
            }


                              echo "<a href=\"?m=alerts\">
											<div class=\"notif-icon\"> <i class=\"fa fa-".$type_icon."\"></i> </div>
											<div class=\"notif-content\">
												<span class=\"block\">
												".$value['text']."
												</span>
												<span class=\"time\">".$value['date']."</span> 
											</div>
										</a>";
                                 }
                                 }
                                 ?>
                                 
									
									
									</div>
								</li>
								<li>
									<a class="see-all" href="?m=alerts"><?php echo _ALL_NOTIF; ?><i class="fa fa-angle-right"></i> </a>
								</li>
							</ul>
						</li>

						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
								<div class="avatar-sm">
									<img src="images/user.png" alt="..." class="avatar-img rounded-circle">
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="images/user.png" alt="image profile" class="avatar-img rounded"></div>
											<div class="u-text">
												<h4><?php echo "<span class=small>#".$check_login['id']."</span> ".$check_login['login']; ?></h4>
												<p class="text-muted"><?php echo $check_login['email']; ?></p><a href="?m=account&tab=1" class="btn btn-xs btn-secondary btn-sm"><?php echo _ACCOUNTOPTIONS; ?></a>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="?m=account"><?php echo _SENDOPTIONS; ?></a>
										<a class="dropdown-item" href="?m=account&tab=4"><?php echo _FINANCE; ?></a>
                                        <?php if($settings['allow_domains']==1) { ?>
										<a class="dropdown-item" href="?m=account&tab=5"><?php echo _DOMAINS; ?></a>
                                        <?php } ?>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="index.php?logout=1"><?php echo _LOGOUT; ?></a>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>
		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
				<?php
                $support = admins("AND status=1 AND is_support=1 AND root=1", 'id', 1);
                if (is_array($support)) {
                    foreach ($support as $key => $value) {
                       $s_name = $value['name'];
                       $s_telegram = $value['telegram'];
                       $s_skype = $value['skype'];
                       $s_email = $value['email'];
                    }
                    
                ?>
                	<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									<?php echo $s_name; ?>
									<span class="user-level">Support</span>
									<span class="caret"></span>
								</span>
							</a>
							<div class="clearfix"></div>

							<div class="collapse in" id="collapseExample">
								<ul class="nav">
									
                                      <?php 
                                      if ($s_telegram) {
										echo "<li><a href=\"tg:".$s_telegram."\">
											<span class=\"link-collapse\"><i class=\"fab fa-telegram-plane\"></i> Telegram: ".$s_telegram."</span>
										</a></li>";
                                       } 
                                       if ($s_skype) {
										echo "<li><a href=\"skype:".$s_skype."\">
											<span class=\"link-collapse\"><i class=\"fab fa-skype\"></i> Skype: ".$s_skype."</span>
										</a></li>";
                                       } 
    
                                      if ($s_email) {
										echo "<li><a href=\"mailto:".$s_email."\">
											<span class=\"link-collapse\"><i class=\"far fa-envelope\"></i> Email: ".$s_email."</span>
										</a>	</li>";
                                       } 
                                        ?>
								
								</ul>
							</div>
						</div>
					</div>
                 <?php
                 }
                 ?>   
					<ul class="nav nav-primary">	
						<li class="nav-item">
							<a href="index.php">
								<i class="fas fa-home"></i>
								<p><?php echo _DASHBOARD ?></p>
							</a>
						</li>
                        <li class="nav-item">
							<a href="index.php?m=sites">
								<i class="fas fa-th-list"></i>
								<p><?php echo _SITES ?></p>
							</a>
						</li>
                        <?php if ($settings['traf_exchange_on']==1) { ?>
                        <li class="nav-item">
							<a href="index.php?m=traf_exchange">
								<i class="fas fa-exchange-alt"></i>
								<p><?php echo _EXCHANGE_TITLE_URL ?></p>
								<?php echo $traf_exchange_count; ?>
							</a>
						</li>
                        <?php } ?>
                        <li class="nav-item">
							<a href="index.php?m=my_send">
								<i class="fas fa-bullhorn"></i>
								<p><?php echo _SEND_PUSH ?></p>
							</a>
						</li>
                        <li class="nav-item">
							<a href="index.php?m=daystat">
								<i class="fas fa-chart-pie"></i>
								<p><?php echo _STAT ?></p>
							</a>
						</li>
                        <?php if($settings['allow_admins']==1 || $check_login['root']==1) { ?>
                        <li class="nav-item">
							<a href="index.php?m=admins">
								<i class="fas fa-user-friends"></i>
								<p><?php echo _GUESTS ?></p>
							</a>
						</li>
                        <?php } ?>
                        <?php if ($settings['allow_referal']==1) { ?>
                        <li class="nav-item">
							<a href="index.php?m=refs">
								<i class="fas fa-users"></i>
								<p><?php echo _REFERALS ?></p>
							</a>
						</li>
                        <?php } ?>
                        <li class="nav-item">
							<a href="index.php?m=account">
								<i class="fas fa-cog"></i>
								<p><?php echo _OPTIONS ?></p>
							</a>
						</li>
                        <li class="nav-item">
							<a href="index.php?m=faq">
								<i class="fas fa-question-circle"></i>
								<p><?php echo _FAQ ?></p>
							</a>
						</li>
 <?php
                if ($check_login['root']==1) {

                 $mail_wait = get_onerow('COUNT(id)', 'mails', 'status=0');
                 if ($mail_wait) $mail_wait = " ($mail_wait)";

                  $err_crons = get_onerow('id', 'crons', 'count_errors>0');
                 if ($err_crons) $err_crons = warn_symb();
                 
                 $all_out = get_onerow('COUNT(id)', 'payment', "type=3 AND status=0");
                 if (!$all_out) $all_out =''; else $all_out = "($all_out)";


                ?>
                 
                 <li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section"><?php echo _MYADV1 ?></h4>
						</li>
                 <li class="nav-item">
                        <a href="index.php?m=a_users"><?php echo _USERS ?></a>
                    </li>
                 <li class="nav-item">
                        <a href="index.php?m=a_payment"><?php echo _WITHDROWALS." ".$all_out ?></a>
                    </li>   
                  <li class="nav-item">
                 <a href="index.php?m=a_mails"><?php echo _MAILS.$mail_wait ?></a>
                </li>
                 <li class="nav-item">
                 <a href="index.php?m=a_faq"><?php echo _FAQ ?></a>
                </li>
                   <li class="nav-item">
                 <a href="index.php?m=a_news"><?php echo _NEWS ?></a>
                </li>
                 <li class="nav-item">
                 <a href="index.php?m=a_landings"><?php echo _LANDINGS1 ?></a>
                </li>
                 <li class="nav-item">
                 <a href="index.php?m=a_reports"><?php echo _REPORTS ?></a>
                </li>
                  <li class="nav-item">
                 <a href="index.php?m=a_journal"><?php echo _JOURNAL ?></a>
                </li>
                <li class="nav-item">
                 <a href="index.php?m=a_content"><?php echo _CONTENT ?></a>
                </li>
                <li class="nav-item">
                 <a href="index.php?m=a_crons"><?php echo _CRONS." ".$err_crons ?></a>
                </li>
                <li class="nav-item">
                 <a href="index.php?m=a_analitika"><?php echo _ANALYTICS ?></a>
                </li>
                 <li class="nav-item">
                  <a href="index.php?m=feeds"><?php echo _FEEDS ?></a>
                  </li>
                <li class="nav-item">
                 <a href="index.php?m=options"><?php echo _AOPTIONS ?></a>
                </li>

               <?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="main-panel">
			<div class="container">
            

