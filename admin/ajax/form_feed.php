<?php
ini_set('display_errors', 0);
error_reporting(0);
require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/stat.php");
require_once("../../include/info.php");
require_once("../forms.php");
header('Content-Type: text/html; charset=utf-8');
$check_login = check_login();
if ($check_login==false) {
exit;
}
$isolist = isolist();
$settings = settings("AND admin_id=".$check_login['getid']."");
$lang = $settings['lang'];
include("../langs/".$lang.".php");
$param = text_filter($_GET['param']);
$params = explode("|", $param);
$id = $params[0];
$type = $params[1];
$temple_form = $params[2];

$feeds = array();
if ($id) {
    if ($type==2) {
$feeds = feeds_templ("AND id=".$id."");   
if ($feeds[$id]['status']==1) $ch = 'checked';     

$params = json_decode($feeds[$id]['params'], true);
    } else {
$feeds = feeds("AND admin_id=".$check_login['getid']." AND id=".$id."");   
if ($feeds[$id]['status']==1) $ch = 'checked'; 
$edit = $id;
}               
}
if (!$id) {$id = '0'; $block_id='1';} else $block_id='2';
if (!$type) $type = '0';

$feeds_templ = feeds_templ("AND status=1");  
if ($temple_form) {
 $edit = $id;   
}
?>
<div id="ajax">
<table>
<tbody>
<tr><td class=col1><?php echo _FEEDTEMPLE; ?> <?php echo tooltip(_FEEDTEMPLE_TOOLTIP, 'right'); ?> </th><td><select name="tfeed" class="form-control-sm form-control col col-md-11" onchange="aj('form_feed.php', this.value+'|2', <?php echo $block_id; ?>);">
<option value="0">-</option>
<?php
                                        foreach ($feeds_templ as $key => $arr) {
                                        if ($id && $id==$key && $type==2) $sel ='selected'; else $sel='';
                                        echo "<option value=\"$key\" ".$sel.">".$arr['name']."</option>";
                                        }
                                        ?>
</select> </td> </tr>
<tr><td class=col1><b><?php echo _NAME; ?></b></th><td><input name="name" type="text" value="<?php echo $feeds[$id]['name']; ?>" class=longinput></td> </tr>
<tr><td><?php echo _SITE; ?></th><td><input name="site" type="text" value="<?php echo $feeds[$id]['site']; ?>" class=longinput></td>  </tr>
<tr><td valign=top><b>URL</b></th><td><input name="url" type="text" value="<?php echo $feeds[$id]['url']; ?>" class=longinput><br />
<?php
if ($type==2) {
    if ($temple_form==1) {
      
      echo "Param 1:<input name=\"params[param1]\" type=\"text\" value=\"".$params['param1']."\" size=10><br>
      Param 2:<input name=\"params[param2]\" type=\"text\" value=\"".$params['param2']."\" size=10><br>
      Param 3:<input name=\"params[param3]\" type=\"text\" value=\"".$params['param3']."\" size=10><br>";  
        
    } else {
        echo $params['param1'].": <input name=\"params[".$params['param1']."]\" type=\"text\" placeholder=\""._FEEDTEMPLE_PARAM1." ".$params['param1']."\" size=25><br>";
        if ($params['param2']) echo $params['param2'].": <input name=\"params[".$params['param2']."]\" type=\"text\" placeholder=\""._FEEDTEMPLE_PARAM1." ".$params['param2']."\" size=25><br>";  
        if ($params['param3']) echo $params['param3'].": <input name=\"params[".$params['param3']."]\" type=\"text\" placeholder=\""._FEEDTEMPLE_PARAM1." ".$params['param3']."\" size=25><br>"; 
    }
    
    }
?>

<a href="#" onclick="viewblock('macross', this); return false;" class=small><i class="fa  fa-tags"></i> <?php echo _MACROS; ?></a><div id="macross" style='display: none;'>
<font class=small>
IP - <?php echo _MACROS2; ?><br />
AGENT - <?php echo _MACROS12; ?><br />
SITE_ID - <?php echo _MACROS1; ?><br />
TOKEN - <?php echo _MACROS3; ?><br />
SUB - <?php echo _MACROS4; ?><br />
LANG - <?php echo _MACROS5; ?><br />
DEVICE - <?php echo _MACROS6; ?><br />
BRAND - <?php echo _MACROS7; ?><br />
MODEL - <?php echo _MACROS8; ?><br />
REF - <?php echo _MACROS9; ?><br />
UID - <?php echo _MACROS10; ?><br />
DATE - <?php echo _MACROS11; ?><br />
</font></td>  </tr>
<tr><td><b>title</b></th><td><input name="feed_title" type="text" value="<?php echo $feeds[$id]['feed_title']; ?>" class=longinput></td>  </tr>
<tr><td><b>body</b></th><td><input name="feed_body" type="text" value="<?php echo $feeds[$id]['feed_body']; ?>" class=longinput></td>  </tr>
<tr><td><b>link</b></th><td><input name="feed_link_click_action" type="text" value="<?php echo $feeds[$id]['feed_link_click_action']; ?>" class=longinput></td>  </tr>
<tr><td><b>icon</b></th><td><input name="feed_link_icon" type="text" value="<?php echo $feeds[$id]['feed_link_icon']; ?>" class=longinput></td>  </tr>
<tr><td>image</th><td><input name="feed_link_image" type="text" value="<?php echo $feeds[$id]['feed_link_image']; ?>" class=longinput></td>  </tr>
<tr><td><b>bid</b></th><td><input name="feed_bid" type="text" value="<?php echo $feeds[$id]['feed_bid']; ?>" class=longinput></td>  </tr>
<tr><td>winurl</th><td><input name="feed_winurl" type="text" value="<?php echo $feeds[$id]['feed_winurl']; ?>" class=longinput></td>  </tr>
<tr><td>button 1</th><td><input name="feed_button1" type="text" value="<?php echo $feeds[$id]['feed_button1']; ?>" class=longinput></td>  </tr>
<tr><td>button 2</th><td><input name="feed_button2" type="text" value="<?php echo $feeds[$id]['feed_button2']; ?>" class=longinput></td>  </tr>
<tr><td><?php echo _RATE; ?></th><td><input name="convert_rate" type="text" value="<?php echo $feeds[$id]['convert_rate']; ?>" class=longinput> <?php echo tooltip(_RATE_TOOLTIP); ?> </td> </tr>
<tr><td><?php echo _BIDCOEF; ?></th><td><input name="bidcoef" type="text" value="<?php echo $feeds[$id]['coef']; ?>" class=longinput> % <?php echo tooltip(_BIDCOEF_TOOLTIP); ?></td></tr>
<tr><td><?php echo _REGIONS; ?></th><td> <select data-placeholder="<?php echo _CHOSE; ?>" multiple class="select2" name="regions[]">
                                <option value=""></option>
                                <?php
                                if ($feeds[$id]['regions']) {
                                    $regionsarr = explode(",",$feeds[$id]['regions']);
                                }
                                foreach ($isolist as $key => $arr) {
                                    if ($regionsarr && in_array($arr['iso'], $regionsarr)) $sel ='selected'; else $sel='';
                                    echo "<option value=\"".$arr['iso']."\" ".$sel.">".$arr[$lang]." [".$arr['iso']."]</option>";
                                }
                                ?>
                            </select> <?php echo tooltip(_REGIONS_TOOLTIP); ?></td>  </tr>
<tr><td><?php echo _SENDEDMAX; ?></th><td><input name="max_send" type="text" value="<?php echo $feeds[$id]['max_send']; ?>" class=longinput></td>  </tr>
<tr><td><?php echo _STATUSON; ?></th><td><input name="status" type="checkbox" value="1" <?php echo $ch; ?>></td>  </tr>
</tbody>
</table> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>" />
</div>