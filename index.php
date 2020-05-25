<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/SxGeo.php");
require_once("include/info.php");
require_once("include/stat.php");
header('Content-Type: text/html; charset=utf-8');

$settings = settings();

$lang = text_filter($_GET['lang']);
if (!$lang) $lang = get_lang();
if ($lang!='ru') $lang = 'en';
include("langs/".$lang.".php");
$stat = get_onerow('value', 'home_stat', 'name="general"');
if ($stat) $stat = json_decode($stat, true);
$stat['all_users'] = $stat['all_users'] + 10000;
$stat['all_subs'] = $stat['all_subs'] + 33000000;
$stat['today_subs'] = $stat['today_subs'] + 233899;
$stat['today_sended'] = $stat['today_sended'] + 55123877;

$faq = faq();
$news = news('AND date <= CURRENT_DATE()', 'date', 3);

$referer = htmlspecialchars(stripslashes(getenv("HTTP_REFERER")));
if ($referer) {
setcookie("ref", $referer, time() + 86000 * 360);
}

$referal = intval($_GET['r']);
if ($referal) {
setcookie("r", $referal, time() + 86000 * 360);  
}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo $settings['sitename']." - ".TITLE?></title>
         <meta name="description" content="<?php echo DESCRIPTION ?>"> 
		<link rel="icon" href="image/favicon.png" type="image/png">
        <link rel="alternate" hreflang="en" href="https://3xpush.com/?lang=en" />
        <link rel="alternate" hreflang="ru" href="https://3xpush.com/?lang=ru" />
		<link rel="stylesheet" href="css/fonts.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
		<link href='css/animate.min.css' type='text/css' rel='stylesheet'/>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="css/slick/slick-theme.css"/>
	</head>
	<body>
		<header>
			<div class="container">
				<div class="row">
					<div class="col-sm-6 col-xs-5">
						<a href="#" class="logo">
							<img src="image/logo-blue.png" alt="3xpush - Push notification" width="200">
						</a>
					</div>
					<div class="col-sm-6 col-xs-7">
						<ul class="soc_icons">
							<li>
								<a href="https://www.facebook.com/3xpush" target="_blank"><i class="fab fa-facebook-f"></i></a>
							</li>
							<li>
								<a href="https://twitter.com/3xpush" target="_blank"><i class="fab fa-twitter"></i></a>
							</li>
							<li>
								<a href="#"><i class="fab fa-youtube"></i></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</header>
		<div class="nav">
			<div class="container">
				<div id="nab_btn_open">
				  <span></span>
				  <span></span>
				  <span></span>
				  <span></span>
				</div>
				<div class="row open_menu">
					<nav class="col-sm-8">
						<ul class="menu_items">
							<li><a><?php echo HOME; ?></a></li>
							<li><a id="better"><?php echo ADVANTAGES; ?></a></li>
							<li><a id="news"><?php echo NEWS; ?></a></li>
							<li><a id="quest"><?php echo FAQ; ?></a></li>
						</ul>
					</nav>
					<div class="col-sm-3 col-xs-6">
						<div class="registr"><a href="admin/" style="color: white;"><img src="image/login.png" alt="login"><?php echo LOGIN; ?></a></div>
					</div>
					<div class="col-sm-1 col-xs-6">
						<div class="lang">
							<li class="dropdown nav-currency" itemprop="name">
                            <?php
                            if ($lang=='en') {
                            ?>
							    <a aria-expanded="false" href="#" class="dropdown-toggle" data-currency="EN" data-toggle="dropdown" itemprop="url" data-element-type="link" data-element-location="top_nav" data-element-label="EN">
							      <div class="img-flag"><img src="image/flag_bg.png"></div>EN<i class="fas fa-angle-down"></i>
							    </a>
                           <?php                              
                            } else {
                           ?> 
                           	    <a aria-expanded="false" href="#" class="dropdown-toggle" data-currency="RU" data-toggle="dropdown" itemprop="url" data-element-type="link" data-element-location="top_nav" data-element-label="RU">
							      <div class="img-flag"><img src="image/flag_ru.png"></div>RU<i class="fas fa-angle-down"></i>
							    </a>
                            <?php                              
                            }
                           ?>    
							    <ul class="dropdown-menu dropdown-menu-right" role="menu">
							        <li itemprop="name">
							          <a href="?lang=en" itemprop="url" data-currency="EN" data-element-type="link" data-element-location="top_nav" data-element-label="EN">
							            <div class="img-flag"><img src="image/flag_bg.png"></div>EN
							          </a>
							        </li>
							        <li itemprop="name">
							          <a href="?lang=ru" itemprop="url" data-currency="RU" data-element-type="link" data-element-location="top_nav" data-element-label="RU">
							            <div class="img-flag"><img src="image/flag_ru.png"></div>RU
							          </a>
							        </li>
							    </ul>
							</li>
						</div>
					</div>
				</div>
			</div>
		</div>
		<section class="first_bl">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;">
						<span class="logo_text"><?php echo _TOP_TITLE; ?></span>
						<div class="descr"><?php echo SHORTDESCR; ?></div>
						<div class="registr_btn"><a href="admin/index.php?m=register" class="whitebutton"><?php echo REGISTERGO; ?></a></div>
					</div>
				</div>
			</div>
		</section>
		<section class="better">
			<div class="container">
				<div class="title_bl wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;"><?php echo OURADVANTAGES; ?></div>
				<div class="descr_bl"><?php echo OURADVANTAGES_TEXT; ?> </div>
                
                <div class="row">
				
                    <div class="col-sm-6">
     	            <div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.5s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInLeft;">
						<img src="image/code.png" alt="code" class="pic_b">
						<div class="name_b">Code</div>
						<div class="text_b"><?php echo ADVANTAGES_1; ?> </div>
					</div>
                    	<div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.5s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;">
						<img src="image/Redirect.png" alt="Redirect" class="pic_b">
						<div class="name_b">Redirect</div>
						<div class="text_b"><?php echo ADVANTAGES_2; ?></div>
					</div>
                    <div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.7s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInLeft;">
						<img src="image/Landing.png" alt="Landing" class="pic_b">
						<div class="name_b">Landing</div>
						<div class="text_b"><?php echo ADVANTAGES_4; ?></div>
					</div>
 	
                        </div>
                        <div class="col-sm-6">
                        <div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.7s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;">
						<img src="image/Statistic.png" alt="code" class="pic_b">
						<div class="name_b">Statistic</div>
						<div class="text_b"><?php echo ADVANTAGES_5; ?></div>
					</div> 
                    <div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.8s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;">
						<img src="image/Setting.png" alt="Setting" class="pic_b">
						<div class="name_b">Setting</div>
						<div class="text_b"><?php echo ADVANTAGES_8; ?></div>
					</div>
                    <div class="col-sm-10 col-xs-6 wow slideInRight advantages" data-wow-duration="1s" data-wow-delay="0.8s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;">
						<img src="image/Trading.png" alt="Trading" class="pic_b">
						<div class="name_b">Exchange</div>
						<div class="text_b"><?php echo ADVANTAGES_9; ?></div>
					</div>                
                        </div>					
					</div>
                    
				<div class="row">
				
					
					
					
					
				</div>
			</div>
		</section>
        
		<section class="how_its_works">
			<div class="container">
				<div class="row">
					<div class="text_c">
						<div class="title_bl big_title wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;"><?php echo HOWITWORKS; ?></div>
						<ul class="colon">
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_1; ?></li>
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.5s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_2; ?></li>
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.6s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_3; ?></li>
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.7s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_4; ?></li>
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.8s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_5; ?></li>
							<li class="wow slideInRight" data-wow-duration="1s" data-wow-delay="0.9s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: slideInRight;"><?php echo HOWITWORKS_6; ?></li>
       
						</ul>
						<div class="registr_btn shadow"><a href="admin/index.php?m=register" class="whitebutton"><?php echo REGISTERGO; ?></a></div>
					</div>
				</div>
			</div>
		</section>

            
		<section class="questions">
			<div class="container">
				<div class="title_bl big_title wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.4s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;"><?php echo FAQ; ?></div>
				<div class="descr_bl wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s" style="visibility: visible; animation-duration: 1s; animation-delay: 0.4s; animation-name: fadeInUp;"><?php echo FAQ_DESCR; ?></div>
				<div class="row">
				
                    <?php
                    $all_faq = count($faq);
                    $half = round($all_faq / 2, 0);
                    $i=0;
                    foreach ($faq as $key => $value) {
                        if ($i==0 || $i==$half) {
                          echo "<div class=\"col-sm-6\">";  
                        }
                       $titles = json_decode($value['title'], true);
                       $answers = json_decode($value['answer'], true);
                    echo "<div class=\"answer_bl accordion\">
							<div class=\"card wow fadeInLeft\" data-wow-duration=\"1s\" data-wow-delay=\"0.4s\">
							    <div class=\"card-header\" id=\"heading".$key."\">
							        <button class=\" btn-link collapsed\" type=\"button\" data-toggle=\"collapse\" aria-expanded=\"false\" data-target=\"#collapse".$key."\" aria-controls=\"collapse".$key."\"> ".$titles[$lang]."</button>
							    </div>
							    <div id=\"collapse".$key."\" class=\"collapse col_text\" aria-labelledby=\"heading".$key."\">
							        ".$answers[$lang]."
							    </div>
							</div>
						</div>";
                        $i++;
                        if ($i==$half || $i==$all_faq) {
                          echo "</div>";  
                        }
                    }
                    ?>
					
					</div>
				</div>
			</div>
		</section>
		<section class="news">
			<div class="container">
				<div class="title_bl big_title"><?php echo NEWS ?> </div>
				<div class="row">
                
                <?php
                
                foreach ($news as $key => $value) {
                       $titles = json_decode($value['title'], true);
                       $content = json_decode($value['content'], true);
                      // $content[$lang] = htmlspecialchars($content[$lang]);
                       $contentwidth = stripslashes($content[$lang]);
            if (mb_strlen($contentwidth, "UTF-8") > 90) {
                $content[$lang] = mb_substr($content[$lang], 0, 90);
                $content[$lang] .= "...";
            }
            
                   echo "<div class=\"col-sm-4 col-xs-6\">
						<div class=\"item_news fadeInLeft wow\" data-toggle=\"modal\" data-target=\"#news".$key."\" data-wow-duration=\"1s\" data-wow-delay=\"0.2s\">
							<div class=\"date\">".$value['date']."</div>
							<div class=\"name_news\">".$titles[$lang]."</div>
							<div class=\"descr_news\">".$content[$lang]."</div>
							<div class=\"open_news\"><i class=\"fas fa-arrow-right\"></i></div>
						</div>
					</div>";
                    
                   }
                    
                ?>
					
				</div>
			</div>
		</section>

		<section class="subscr">
			<div class="container">
				<div class="row">
					<div class="title_s wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s"><?php echo SUBSCRIBE ?> </div>
					<form action='#' target="_blank" charset='UTF-8' method='post'  class="subscr wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.7s">
						<input type="email" name="recipient[email]" id="recipient_email" required placeholder="<?php echo YOURMAIL ?>">
						<button type="submit" class="start_sub"><?php echo GETSTARTED ?></button>
					</form>
				
                
                <div align="center"><h2><?php echo CONTACTS; ?></h2>
                <i class="fa fa-envelope"></i> E-mail: <strong><?php echo $settings['support_mail']; ?></strong><br />
                <i class="fa fa-rocket"></i> Telegram: <a href="https://telegram.me/<?php echo $settings['telegram']; ?>"><strong><?php echo $settings['telegram']; ?></strong></a><br /><br /></div>
                <div class="seo_text">
                <?php  
                $text = content("AND name='seo_text'");
                if (is_array($text)) {
                foreach ($text as $key => $value) {
                  $content = json_decode($value['content'], true);
                  echo htmlspecialchars_decode($content[$lang]); 
                }
                }
                  ?>   
                  </div>
                </div>
			</div>
		</section>
		<footer style="margin-top: 10px;">
			<div class="container">
				<div class="row">
					<div class="col-sm-4 col-xs-4">

					</div>
					<div class="col-sm-4 col-xs-4">
						<p class="copyr">© Copyright 2019 <?php echo $settings['sitename']; ?></p>
					</div>
					<div class="col-sm-4 col-xs-4">
						<a href="https://telegram.me/<?php echo $settings['telegram']; ?>" class="telegr">Telegram: <?php echo $settings['telegram']; ?></a>
					</div>
				</div>
			</div>
		</footer>
 <?php
                foreach ($news as $key => $value) {
                       $titles = json_decode($value['title'], true);
                       $content = json_decode($value['content'], true);
                    
        echo "<!-- modals news".$key." -->
		<div class=\"modal fade\" id=\"news".$key."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"news\" aria-hidden=\"true\">
		  <div class=\"modal-dialog\" role=\"document\">
		    <div class=\"modal-content\">
		      <div class=\"modal-header\">
		        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
		          <span aria-hidden=\"true\">&times;</span>
		        </button>
		        <div class=\"title_modal\"><span>".$titles[$lang]."</span></div>
		        <div class=\"descr_mod\">".$content[$lang]."</div>
		      </div>
		    </div>
		  </div>
		</div>\n";
                    
                   }
                   
      echo content_name('metriks', 'code');        
       ?>            


	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
     <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="css/slick/slick.min.js"></script>
                <script>
$(document).ready(function(){
$('.rotate').slick({
    accessibility: true,
    autoplay: true,
    autoplaySpeed: 1700,
    fade: false,
    pauseOnFocus: true,
    slidesPerRow: 5
  });
});
</script>

	<script src="js/wow.min.js"></script>
	<script> new WOW().init();			 </script>
	<script src="js/animate-css.js" charset="utf-8"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/script.js" charset="utf-8"></script>
</html>