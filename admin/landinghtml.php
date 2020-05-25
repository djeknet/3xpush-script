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


?>

<div class="page-inner">
<div class="page-header">
<h4 class="page-title"><?php echo _LANDOPTION ?></h4>
</div>
<?php
$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 1;
$iframesave = intval($_REQUEST['iframesave']);
$create_landing = intval($_REQUEST['create_landing']);
$category = intval($_REQUEST['category']);
$lid = intval($_REQUEST['lid']);
$lstatus = intval($_REQUEST['lstatus']);
$land_edit = intval($_REQUEST['land_edit']);
$land_update = intval($_REQUEST['land_update']);
$newcategory = text_filter($_REQUEST['newcategory']);
if ($newcategory) $category = $newcategory;

$id = intval($_REQUEST['id']);
if (!$id) {
   status('no id', 'danger');
    exit;
}
if ($check_login['root']!=1) {
    $where_admin = "AND admin_id=".$check_login['getid']."";
}
//$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'editor';
$html = isset($_REQUEST['html']) ? text_filter($_REQUEST['html'], 2) : '';

if ($iframesave && $check_login['role'] == 1 && $id) {
    $iframe_options = json_encode($_POST['iframe_options'], JSON_UNESCAPED_UNICODE);
    $db->sql_query("UPDATE sites SET iframe_options='" . $iframe_options ."' WHERE id=" . $id . "") or $error = mysqli_error();
    if ($error) {
        jset($check_login['id'], $error, 1);
        status($error, 'danger');
    } else {
        jset($check_login['id'], _IFRUPDATE.": ".$id."");
                if ($check_login['id']!=$check_login['getid']) {
                   alert(_IFRUPDATE.": $id (user: ".$check_login['login'].")", $check_login['getid']);
                }
        status(_IFRUPDATE, 'success');
    }
}
$sellanding = intval($_GET['sellanding']);
$dellanding = intval($_GET['dellanding']);
$category = text_filter($_POST['category']);

// выбор лендинга
if ($sellanding) {
   $is_lands = get_onerow('land_id', 'sites', "id=" . $id . ""); 
   if ($is_lands) $is_lands = $sellanding.",".$is_lands; else $is_lands = $sellanding;
  $db->sql_query("UPDATE landings SET used=used+1 WHERE id=" . $sellanding . "");
  $db->sql_query("UPDATE sites SET land_id='" . $is_lands ."' WHERE id=" . $id . " ".$where_admin."");// or $stop = mysqli_error();
   jset($check_login['id'], _LANDINGSET.": ".$sellanding.", sid: $id");
                if ($check_login['id']!=$check_login['getid']) {
                   alert(_LANDINGSET.": $sellanding, sid: $id (user: ".$check_login['login'].")", $check_login['getid']);
                }
   status(_LANDINGSET, 'success');
}

// отключение лендинга
if ($dellanding) {
    $is_lands = get_onerow('land_id', 'sites', "id=" . $id . ""); 
    $is_lands_arr = explode(',', $is_lands);
    if (($key = array_search($dellanding, $is_lands_arr)) !== false) {
    unset($is_lands_arr[$key]);
    }
    $is_lands = implode(',', $is_lands_arr);
   $db->sql_query("UPDATE sites SET land_id='".$is_lands."' WHERE id=" . $id . " ".$where_admin."");// or $stop = mysqli_error();
    status(_LANDINDEL, 'success');
    jset($check_login['id'], _LANDINDEL." sid: $id");
if ($check_login['id']!=$check_login['getid']) {
alert(_LANDINDEL." sid: $id (user: ".$check_login['login'].")", $check_login['getid']);
 }
}


$sites = sites("AND id='$id' ".$where_admin."");
if (!$sites) {
   status('site error', 'danger');
    exit;
}
if ($sites[$id]['html']) {
    $sites[$id]['html'] = htmlspecialchars($sites[$id]['html']);
}
if ($sites[$id]['land_options']) {
    $sites[$id]['land_options'] = htmlspecialchars_decode($sites[$id]['land_options']);
    $land_options = json_decode($sites[$id]['land_options'], true);
    //var_dump($land_options);
}
$iframe_options=array();
if ($sites[$id]['iframe_options']) {

    $iframe_options = json_decode($sites[$id]['iframe_options'], true);
    if ($iframe_options['nogettext']==0) $text_link = $iframe_options['text'];
}
if (!$iframe_options['url']) $iframe_options['url'] = 'URL';
$types = array(0 => _REDIRTYPE0, 1 => _REDIRTYPE1, 2 => _REDIRTYPE2, 3 => _REDIRTYPE3);
$domains = domains("AND admin_id=".$check_login['getid']."  AND ssl_ready=1");
$landings = landings("AND status=1");

                         $cat_list=array();
                         if (is_array($landings)) {
                         foreach ($landings as $key => $value) {
                         $cat_list[$value['category']] = $value['category'];
                           }
                           }

if ($sites[$id]['land_id']!=0) $https_check = 'checked'; else $https_check  = '';
?>

<script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>



<div class="content mt-3">
    <div class="animated fadeIn">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body card-block">
                        <h4><?php echo _LANDING . " " . $sites[$id]['title']; ?></h4><br />
                    <span class="small"><?php echo _DOMAINFORLINKS; ?>:</span>
                                            <select size="1" name="domain" class="smallinput">
                                            <option value="<?php echo $settings['domain_link']; ?>"><?php echo $settings['domain_link']; ?></option>

                                            <?php
                                            foreach ($domains as $key => $value) {
                                            echo "<option value=\"".$value['domain']."\">".$value['domain']."</option>";
                                            }
                                            ?>
                                            </select>
                    <?php
                    $linkUrl_1  = "/land.php?id=" . $id . "&subid=&tag=&price=0&url=";
                    $linkUrl_2  = "/frame.php?sid=".$id."&subid=&tag=&price=0&noredirect=".$iframe_options['noredirect']."&r=".$iframe_options['repeat']."&text=".$text_link."#".$iframe_options['url'];
                    $linkUrl_3 = "/link.php?sid=".$id."&subid=&tag=&price=0&text=".$text_link."&url=".$iframe_options['url'];

                    status_small (_LINK . ": <a class='link_url' data-url=\"$linkUrl_1\" href=\"https://" . $settings['domain_link'] . "/land.php?id=" . $id . "&subid=&tag=&price=0&url=\" target=\"_blank\">https://" . $settings['domain_link'] . "/land.php?id=" . $id . "&subid=&tag=&price=0&url=</a>", 'info');
                     status_small ("Iframe: <a class='link_url' data-url=\"$linkUrl_2\" href=\"https://".$settings['domain_link']."/frame.php?sid=".$id."&subid=&tag=&price=0&noredirect=".$iframe_options['noredirect']."&r=".$iframe_options['repeat']."&text=".$text_link."#".$iframe_options['url']."\" target=_blank>https://".$settings['domain_link']."/frame.php?sid=".$id."&subid=&tag=&price=0&noredirect=".$iframe_options['noredirect']."&r=".$iframe_options['repeat']."&text=".$text_link."#".$iframe_options['url']."</a>", 'info');
                    status_small ("Redirect: <a class='link_url' data-url=\"$linkUrl_3\" href=\"https://".$settings['domain_link']."/link.php?sid=".$id."&subid=&tag=&price=0&text=".$text_link."&url=".$iframe_options['url']."\" target=_blank>https://".$settings['domain_link']."/link.php?sid=".$id."&subid=&tag=&price=0&text=".$text_link."&url=".$iframe_options['url']."</a>", 'info');
                     echo "<span class=small>"._LINKS_INFO."</span>";
                     ?>
                    </div>

                    <script>
                        $(document).ready(function () {
                            $('select[name="domain"]').change(function () {
                                var value = $(this).val();
                                $('.link_url').each(function () {
                                    var url = value + $(this).data('url');
                                    $(this).attr('href', 'https://' + url).text('https://'+url);
                                });
                            });
                        })
                    </script>

                    <div class="default-tab">
                        <nav class="nav nav-tabs">
                            <a class="nav-link nav-item <?= $tab==1 ? 'active' : '' ?>" name="1" data-toggle="tab" href="#editor"><?php echo _EDITOR; ?></a>
                            <?php if ($settings['allow_copyland']==1 || $check_login['root']==1) { ?>
                            <a class="nav-link nav-item <?= $tab==2 ? 'active' : '' ?>" name="2" data-toggle="tab" href="#tocopy"><?php echo _COPY; ?></a>
                            <?php } ?>
                            <a class="nav-link nav-item <?= $tab==3 ? 'active' : '' ?>" name="3" data-toggle="tab" href="#iframe"><?php echo _OPTIONS; ?> Iframe / Redirect</a>
                            <a class="nav-link nav-item <?= $tab==4 ? 'active' : '' ?>" name="4" data-toggle="tab" href="#landings"><?php echo _LANDINGS; ?></a>
                        </nav>
                    </div>

                    <div class="tab-content" role="tabpanel">
                        <div role="tabpanel" class="tab-pane fade show <?= $tab==1 ? 'active' : '' ?>" id="editor">
                            <div class="card-body">

                                <div class="page-editor-status" style="margin-bottom: 10px">
                                    <?php status(_LANDUPDATE, 'success');; ?>
                                    <?php status(_LANDUPDATENO, 'danger'); ?>
                                    <script type="text/javascript">
                                        $('.page-editor-status .alert').hide();
                                    </script>
                                </div>

                                <form id="htmlEditor" name="html" action="?m=landinghtml" method="post">

                                <?php
                                if ($sites[$id]['land_id']!=0) {
                                   status(_LANDINDSELECTED, 'info');
                                  echo "<style>#cke_txtDefaultHtmlArea {display: none;} .loadimgblock {display: none;}</style>";
                                }

                                ?>


                            <textarea name="editorHTML" id="txtDefaultHtmlArea" rows=50 cols=15
                                      style='width: 100%; height: 600px; <?php echo $hide; ?>'
                                      wrap="off"><?php echo $sites[$id]['html']; ?></textarea>

                                    <script>
                                        CKEDITOR.replace( 'editorHTML', {
                                            height: 600,
                                            allowedContent: true,
                                            title: false
                                        });
                                    </script>
                                    <input id="landingId" name="id" type="hidden" value="<?php echo $id; ?>">
                                    <div class="loadimgblock">
                                        <input type="file" name="upload_image" id="upload_image" multiple>
                                    <button id="upload_image_button" type="button" class="btn btn-success" style="margin-top: 5px;;"><i class="fa fa-magic"></i>&nbsp; <?php echo _LOADIMG; ?></button>
                                    <!-- в этом блоке выводить картинки из папки лендинга landings/ID/data в виде списка -->
                                    <div id="images-block">

                                    </div>
                                    </div>
                                    <br/>

                                    <?php

                                    function get_image_files($landingId) {

                                        global $root;

                                        $uploaddir = 'landings/' . $landingId . '/data';
                                        $uploaddir_full = $root . $uploaddir;

                                        if(!is_dir($uploaddir_full)) {
                                            return array();
                                        }


                                        $files = scandir($uploaddir_full);

                                        if(!$files) {
                                            return array();
                                        }

                                        unset($files[0], $files[1]);

                                        foreach ($files as $k => &$file) {
                                            $file = '/' . $uploaddir . '/' . $file;
                                        }

                                        return $files;
                                    }

                                    $image_files = get_image_files($id);


                                    ?>

                                    <script type="text/javascript">

                                        function getElement() {
                                            return document.getElementById('txtDefaultHtmlArea');
                                        }

                                        function insertTextAtCursor(el, text, offset) {
                                            var val = el.value, endIndex, range, doc = el.ownerDocument;
                                            if (typeof el.selectionStart == "number"
                                                && typeof el.selectionEnd == "number") {
                                                endIndex = el.selectionEnd;
                                                el.value = val.slice(0, endIndex) + text + val.slice(endIndex);
                                                start = el.selectionStart = el.selectionEnd = endIndex + text.length+(offset?offset:0);
                                            } else if (doc.selection != "undefined" && doc.selection.createRange) {
                                                el.focus();
                                                range = doc.selection.createRange();
                                                range.collapse(false);
                                                range.text = text;
                                                range.select();
                                            }
                                        }

                                        $(document).ready(function (e) {

                                            var image_files = <?= json_encode($image_files); ?>;

                                            $.each(image_files, function (k, image) {
                                                show_image(image);
                                            });

                                            $(document).on('click', '.imgblock .imgblock_close', function (e) {
                                                e.preventDefault();
                                                e.stopPropagation();

                                                var parent = $(this).closest('.imgblock');
                                                var filename = parent.find('img').attr('src');

                                                $.ajax({
                                                    url: 'ajax/upload_image.php?action=remove&filename='+filename,
                                                    success: function (response) {
                                                        response = JSON.parse(response);

                                                        if(response && response['success']) {
                                                            parent.remove();
                                                        }
                                                    }
                                                });

                                            });

                                            $(document).on('click', '.imgblock', function (e) {
                                                e.preventDefault();

                                                var imgElement = $(this).find('img').clone();
                                                imgElement.removeAttr('style');

                                                var src = imgElement[0].outerHTML;
                                                CKEDITOR.instances.txtDefaultHtmlArea.insertHtml(src);
                                            });

                                            $('#upload_image_button').on('click', function () {
                                                $("#upload_image").trigger('click');
                                            });

                                            $("#upload_image").on('change', function (e) {
                                                $.each(this.files, function (key, file) {
                                                    upload_file(file);
                                                });
                                            });

                                            function upload_file(file) {

                                                var landingId = $('#landingId').val();
                                                var formData = new FormData();
                                                formData.append("image", file);
                                                formData.append("landingId", landingId);

                                                $.ajax({
                                                    url: 'ajax/upload_image.php',
                                                    type: "POST",
                                                    data: formData,
                                                    contentType: false,
                                                    processData: false,
                                                    success: function (response) {
                                                        response = JSON.parse(response);

                                                        if(response && typeof response['file'] !== 'undefined') {
                                                            show_image(response['file']);
                                                        }

                                                        if(response && typeof response['error'] !== 'undefined') {
                                                            show_error(response['error']);
                                                        }
                                                    }
                                                });
                                            }

                                            function show_error(error) {
                                                alert(error);
                                            }

                                            function show_image(image) {
                                                var content = '<div class="imgblock"><a href="' + image + '"><img style="max-width: 200px;" src="' + image + '" /></a><span class="imgblock_close"></span></div>';
                                                $('#images-block').append(content);
                                            }

                                        });
                                    </script>

                                    <b><?php echo _INCLUDESCRIPTS; ?></b> <br/>
                                    <br/>


                                    <div class="row">

                                        <div class="nav flex-column nav-pills" role="tablist"
                                             aria-orientation="vertical" id="landOptionsBlock">
                                            <label class="nav-link"
                                                   id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home"
                                                   role="tab" aria-controls="v-pills-home" aria-selected="true">
                                                <input type="checkbox" name="land_options[0][tab]"
                                                       id="land_options_0_tab" class=""
                                                       value="0" <?php echo $land_options[0]['tab'] ? 'checked' : $https_check; ?>
                                                />
                                                HTTPS
                                            </label>
                                            <?php
                                            if ($sites[$id]['land_id']==0) {
                                            ?>
                                            <label class="nav-link"
                                                   id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile"
                                                   role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                                <input type="checkbox" name="land_options[1][tab]"
                                                       id="land_options_1_tab" class=""
                                                       value="0" <?php echo $land_options[1]['tab'] ? 'checked' : ''; ?>
                                                />
                                                Redirect
                                            </label>
                                            <label class="nav-link"
                                                   id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages"
                                                   role="tab" aria-controls="v-pills-messages" aria-selected="false">
                                                <input type="checkbox" name="land_options[2][tab]"
                                                       id="land_options_2_tab"
                                                       value="0" <?php echo $land_options[2]['tab'] ? 'checked' : ''; ?>
                                                />
                                                Block Content
                                            </label>
                                             <?php
                                           }
                                            ?>
                                        </div>
                                        <div class="tab-content" style="width: 53%" id="v-pills-tabContent">
                                            <div class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">

                                                <div class="form-group form-inline">
                                                    <label for="land_options_0_psx_time"><?php echo _LANDOPTIONS1; ?></label>
                                                    <input name="land_options[0][psx_time]" id="land_options_0_psx_time" type="text" class="form-control"
                                                           value="<?php echo $land_options[0]['psx_time'] ?>"
                                                           maxlength="" style="margin: 0 5px" placeholder="0"> <?php echo _SEK; ?><br/>
                                                </div>
                                                 <div class="form-group form-inline">
                                                    <label for="land_options_0_repeat"><?php echo _LANDOPTIONS5; ?></label>
                                                    <input name="land_options[0][repeat]" id="land_options_0_repeat" type="text" class="form-control"
                                                           value="<?php echo $land_options[0]['repeat'] ?>"
                                                           maxlength="2" style="margin: 0 5px"  placeholder="0"><br/>
                                                </div>
                                                <div class="">
                                                    <input type="checkbox" value="1"
                                                           id="land_options_0_blocksite"
                                                           name="land_options[0][blocksite]" <?php echo $land_options[0]['blocksite'] ? 'checked' : ''; ?> />
                                                    <label for="land_options_0_blocksite"><?php echo _LANDOPTIONS2; ?></label>
                                                </div>
                                                <div class="">
                                                    <input type="checkbox" name="land_options[0][hasBlockCross]" id="land_options_0_hasBlockCross"
                                                           value="1" <?php echo $land_options[0]['hasBlockCross'] ? 'checked' : ''; ?> />
                                                    <label for="land_options_0_hasBlockCross"><?php echo _LANDOPTIONS3; ?></label>

                                                </div>
                                                <div class="form-group form-inline">
                                                    <label for="land_options_0_blockText"><?php echo _LANDOPTIONS4; ?></label>
                                                    <input name="land_options[0][blockText]" id="land_options_0_blockText" type="text"
                                                           value="<?php echo $land_options[0]['blockText'] ?>"
                                                           maxlength="300" placeholder="<?php echo _DEFAULT; ?>"
                                                           class="form-control" style="margin: 0 5px">
                                                </div>
                                                <div class="form-group form-inline">
                                                    <label for="land_options_0_blockText"><?php echo _REDIRECTURL; ?></label>
                                                    <input name="land_options[0][link]" id="land_options_0_link" type="text"
                                                           value="<?php echo $land_options[0]['link'] ?>"
                                                           maxlength="300" placeholder="<?php echo _NOT; ?>"
                                                           class="form-control" style="margin: 0 5px">
                                                            <input type="checkbox" name="land_options[0][nourllink]" id="land_options_0_urllink"
                                   value="1" <?php echo $land_options[0]['nourllink'] ? 'checked' : ''; ?> />
                                <label for="land_options_0_urllink"><?php echo _IFRAMEOPTIONS3; ?></label> <?php echo tooltip(_NOLINK_EMPTY); ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show <?= $tab==3 ? 'active' : '' ?>" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">

                                                            <div class="alert alert-warning" role="alert">
                                       <?php
	echo _LANDOPTIONS7;
                                   ?>
                                    </div>
                                                <div class="form-group form-inline">
                                                    <label for="land_options_1_repeat"><?php echo _LANDOPTIONS5; ?></label>
                                                    <input name="land_options[1][repeat]" id="land_options_1_repeat" type="text" class="form-control"
                                                           value="<?php echo $land_options[1]['repeat'] ?>"
                                                           maxlength="2" style="margin: 0 5px"  placeholder="0"><br/>
                                                </div>
                                                <div class="">
                                                    <label for="land_options_1_text"><?php echo _CODETEXT21; ?></label>
                                                  <input name="land_options[1][text]" id="land_options_1_text" type="text"
                                                           value="<?php echo $land_options[1]['text'] ?>"
                                                           maxlength="300" placeholder="<?php echo _DEFAULT; ?>"
                                                           class="form-control" style="margin: 0 5px">
                                                </div>
                                                <div class="form-group form-inline">
                                                   <?php echo _LANDOPTIONS6; ?><br />


                                                      <select name="land_options[1][type]" id="land_options_1_type" style="margin: 9px 5px" class="form-control-sm form-control col col-md-8">
                                                      <?php
                                                      foreach ($types as $key => $val) {
                                                        if ($key==$land_options[1]['type']) $sel = 'selected'; else  $sel='';
                                                      echo "<option value=\"$key\" ".$sel.">".$val."</option>";
                                                      }
                                                      ?>

                                                 </select>

                                                </div>
                                            </div>
                                            <div class="tab-pane fade show " id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                                                                            <div class="alert alert-warning" role="alert">
                                       <?php
                                      	echo _CODETEXT15;
                                           ?>
                                            <textarea readonly='false' class='code-text' style='height: 220px'>
                                            &lt;div class=&quot;closing-content&quot;&gt;
                                            -- <?php echo _CODETEXT16; ?> --
                                            &lt;div class=&quot;closing-content__overlay&quot;&gt;
                                            &lt;span class=&quot;closing-content__text&quot;&gt;<?php echo _CODETEXT17; ?>
                                            &lt;span class=&quot;closing-content__subscribe&quot;&gt;<?php echo _CODETEXT12; ?>&lt;/span&gt;
                                            &lt;/span&gt;
                                            &lt;/div&gt;
                                            &lt;/div&gt;
                                            </textarea>
                                            <script>
    $(".tab-pane .code-text").each(function (k, item) {
        var val = $(this).val();
        val = val.replace(/\s{2,}/g, '\n');
        val = val.trim();
        $(this).val(val);
        $(this).click(function () {
            $(this).select();
        });
    });
</script>
                                    </div>
                                            </div>
                                        </div>


                                    </div>

                                    <br/>
                                    <button type="submit"
                                            class="btn btn-primary btn-landing-save"><?php echo _SEND; ?></button>
                                </form>


                                <script>
                                    $(document).ready(function () {

                                        var landOptions = <?php echo (empty($land_options)) ? '[]' : json_encode($land_options) ?>;

                                        var tabShowIndex = 0;
                                        var tabShowCount = 0;

                                        $.each(landOptions, function (k, item) {
                                            if (item.tab == 1) {
                                                tabShowIndex = k;
                                                tabShowCount++;
                                            }
                                        });

                                        tabShowIndex = tabShowCount > 1 ? 0 : tabShowIndex;
                                        $('#landOptionsBlock .nav-link:eq(' + tabShowIndex + ')').click();

                                        var checkBoxes = $('#landOptionsBlock .nav-link input');

                                        checkBoxes.on('change', function () {
                                            this.value = this.checked ? 1 : 0;
                                        }).change();

                                        $('#landOptionsBlock .nav-link input').on('click', function (event) {
                                            event.stopPropagation();
                                        });

                                        function showEditorStatus(status) {
                                            if (status == 1) {
                                                $(".page-editor-status .alert-success").show();
                                            } else {
                                                $(".page-editor-status .alert-danger").show();
                                            }

                                            $('.page-loading').hide();
                                            $('.btn-landing-save').removeAttr('disabled');
                                        }

                                        $('#htmlEditor').on('submit', function (e) {
                                            e.preventDefault();

                                            var  html = CKEDITOR.instances.txtDefaultHtmlArea.getData();
                                            var id = $('#landingId').val();

                                            var serialize = $(this).serializeArray();
                                            serialize = serialize.concat(
                                                jQuery('#htmlEditor input[type=checkbox]:not(:checked)').map(
                                                    function () {
                                                        return {"name": this.name, "value": 0}
                                                    }).get()
                                            );
                                            serialize[0] = {
                                                name: 'html',
                                                value: html
                                            };
                                            // console.log(serialize);

                                            $.ajax({
                                                url: 'ajax/sites.php?id=' + id,
                                                method: 'POST',
                                                data: serialize,
                                                beforeSend: function () {
                                                    $('.btn-landing-save').attr('disabled', 'disabled');
                                                },
                                                success: function (response) {
                                                    if (response == 'ok') {

                                                        showEditorStatus(1);

                                                        setTimeout(function () {
                                                            location.reload();
                                                        }, 1000);
                                                    } else {
                                                        showEditorStatus(0);
                                                    }
                                                }
                                            });

                                        })
                                    });
                                </script>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane fade show <?= $tab==2 ? 'active' : '' ?>" id="tocopy">

                            <div style="padding: 15px">

                                <form method="post" id="page-form">
                                    <input type="hidden" name="landingId" value="<?= $id; ?>"/>
                                    <div class="form-group row">
                                        <label for="pageUrl"
                                               class="col-sm-2 col-form-label"><?php echo _PAGEURL; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="pageUrl" placeholder=""
                                                   name="pageUrl">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPassword"
                                               class="col-sm-2 col-form-label"><?php echo _PROXY; ?></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="proxy" placeholder="ip:port"
                                                   name="proxy">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">&nbsp;</label>
                                        <div class="col-sm-10">
                                            <input style="margin: 0" class="form-check-input position-static"
                                                   type="checkbox" id="removeFiles" name="removeFiles"
                                                   aria-label="<?php echo _DELETE; ?>" value="0">
                                            <label for="removeFiles"
                                                   class="col-sm-3 col-form-label"><?php echo _DELETEALL; ?>
                                                JavaScript</label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">&nbsp;</label>
                                        <div class="col-sm-10">
                                            <input type="submit" class="copy_page btn btn-primary btn-sm"
                                                   value="<?php echo _COPY; ?>"/>

                                            <div class="page-loading"
                                                 style="display: none; margin-top: 10px"><?php echo _COPYING; ?>....
                                            </div>
                                        </div>
                                    </div>
                                </form>


                                <div class="page-status">
                                    <?php status(_COPYLANDOK, 'success'); ?>
                                    <?php status(_COPYLANDNO, 'danger'); ?>
                                    <script type="text/javascript">
                                        $('.page-status .alert').hide();
                                    </script>
                                </div>

                                <div class="page-errors"></div>
<?php if ($settings['allow_copyland']==1 || $check_login['root']==1) { ?>
                                <script type="text/javascript">

                                    function showStatus(status) {
                                        if (status == 1) {
                                            $(".page-status .alert-success").show();
                                        } else {
                                            $(".page-status .alert-danger").show();
                                        }

                                        $('.page-loading').hide();
                                        $('.copy_page').removeAttr('disabled');
                                    }

                                    $('#removeFiles').on('change', function () {
                                        this.value = this.checked ? 1 : 0;
                                    }).change();

                                    $("#page-form").on("submit", function (event) {

                                        event.preventDefault();
                                        var params = $(this).serialize();

                                        $.ajax({
                                            url: 'copypage.php',
                                            data: params,
                                            beforeSend: function () {
                                                $('.page-errors').empty();
                                                $('.page-loading').show();
                                                $('.copy_page').attr('disabled', 'disabled');
                                            },
                                            success: function (response) {

                                                response = JSON.parse(response);

                                                if (response && response['pageId']) {

                                                    showStatus(1);
                                                    setTimeout(function () {
                                                        location.reload();
                                                    }, 2000);
                                                    return;

                                                }

                                                showStatus(0);

                                                if (response['errors'].length > 0) {
                                                    $.each(response['errors'], function (k, error) {
                                                        var element = $('<div class="alert alert-danger"></div>').text(error);
                                                        $('.page-errors').append(element);
                                                    })
                                                }

                                            },
                                            error: function () {
                                                showStatus(0);
                                            }
                                        });
                                    });


                                </script>
   <?php } ?>
                                <style>
                                    #landOptionsBlock {
                                        width: 300px;
                                        margin-right: 100px;
                                    }
                                </style>

                            </div>

                        </div>
                
                        
                        <div role="tabpanel" class="tab-pane fade show <?= $tab==3 ? 'active' : '' ?>" id="iframe">
                            <div class="card-body">
                            <form method="post" action="?m=landinghtml&id=<?php echo $id; ?>">
                             <div class="form-group form-inline">
                                                    <label for="iframe_options_repeat"><?php echo _IFRAMEOPTIONS1; ?></label>
                                                    <input name="iframe_options[repeat]" id="iframe_options_repeat" type="text"
                                                           value="<?php echo $iframe_options['repeat'] ?>"
                                                           maxlength="2" placeholder="1"
                                                           class="form-control" style="margin: 0 5px">
                                                </div>
                                 <div class="">
                                 <input type="checkbox" name="iframe_options[noredirect]" id="iframe_options_noredirect"
                                   value="1" <?php echo $iframe_options['noredirect'] ? 'checked' : ''; ?> />
                                <label for="iframe_options_noredirect"><?php echo _IFRAMEOPTIONS2; ?></label>
                              </div>
                                        <div class="form-group form-inline">
                                                    <label for="iframe_options_text"><?php echo _LANDOPTIONS4; ?></label>
                                                    <input name="iframe_options[text]" id="iframe_options_text" type="text"
                                                           value="<?php echo $iframe_options['text'] ?>"
                                                           maxlength="300" placeholder="<?php echo _DEFAULT; ?>"
                                                           class="form-control" style="margin: 0 5px; width: 400px;">&nbsp;&nbsp;
                                                            <input type="checkbox" name="iframe_options[nogettext]" id="iframe_options_noredirect"
                                   value="1" <?php echo $iframe_options['nogettext'] ? 'checked' : ''; ?> />
                                <label for="iframe_options_noredirect"><?php echo _IFRAMEOPTIONS3; ?></label> <?php echo tooltip(_NOLINK_EMPTY); ?>
                                                </div>
                                    <div class="form-group form-inline">
                                                    <label for="iframe_options_url"><?php echo _LINK; ?></label>
                                                    <input name="iframe_options[url]" id="iframe_options_url" type="text"
                                                           value="<?php echo $iframe_options['url'] ?>"
                                                           maxlength="300" placeholder="URL"
                                                           class="form-control" style="margin: 0 5px; width: 400px;">&nbsp;&nbsp;
                                                             <input type="checkbox" name="iframe_options[nogeturl]" id="iframe_options_nogeturl"
                                   value="1" <?php echo $iframe_options['nogeturl'] ? 'checked' : ''; ?> />
                                <label for="iframe_options_nogeturl"><?php echo _IFRAMEOPTIONS3; ?></label> <?php echo tooltip(_NOLINK_EMPTY); ?>
                                                </div>
                                 <input type="hidden" name="id" value="<?php echo $id; ?>" />
                               <button type="submit" name="iframesave" value="1" class="btn btn-primary btn-landing-save"><?php echo _SEND; ?></button>
                              </form>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade show <?= $tab==4 ? 'active' : '' ?>" id="landings">
<div class="card-body card-block">
                        <?php
                        status (_LANDINGSINFO, 'info');
                        if (is_array($landings)) {


                           echo "<form name=\"form\" action=\"?m=".$module."&tab=4\" method=\"post\">";
                           echo "<div class=\"row form-group\">
                           
                           "._CATEGORY.": &nbsp;&nbsp;<select name=\"category\">
                           <option value=\"0\">"._EVERY."</option>\n";

                         foreach ($cat_list as $key => $value) {
                           if ($category && $category==$key) $sel ='selected'; else $sel='';
                           echo "<option value=\"$key\" ".$sel.">".$value."</option>";
                         }

                         echo "</select>&nbsp;&nbsp;
                     
                        <button class=\"btn btn-primary btn-sm\">
                                  <i class=\"fa fa-search\"></i>"._SEARCH."
                                 </button>
                                 </div>
                                 <input type=\"hidden\" name=\"id\" value=\"".$id."\" />
                                 </form>";
                            $i=0;
                            if ($sites[$id]['land_id']) {
                                $lands_arr = explode(',', $sites[$id]['land_id']);
                                
                                 echo ""._LANDINGCHOSEN."<div class=\"row\">";
                             foreach ($landings as $key => $value) {
                                if (!in_array($key, $lands_arr)) continue;

                              if ($value['views'] > 0 && $value['subs'] > 0 ) $cr = round(($value['subs'] / $value['views'])*100, 1); else $cr = '-';

                              $button = " <a href=?m=".$module."&id=".$id."&tab=4&dellanding=".$key." class=\"btn btn-primary btn-sm\" style='color: white !important'><i class=\"fa fa-times-circle-o\"></i>&nbsp; "._CANCEL2."</a>";
                              $style = "style='background-color: #e5ffe8;'";

                      echo "<div class=\"col-lg-3\">
                        <section class=\"card\" ".$style.">
                            <div class=\"card-body text-secondary\">
                            № ".$key." &nbsp;&nbsp; "._CATEGORY.": ".$value['category']."<br>
                            <a href=\"https://" . $settings['domain_link'] . "/land.php?id=" . $id . "&lid=".$key."&subid=&tag=&price=0\" target=\"_blank\"><img src=".$value['preview']." border=0 width=100% class=landimg></a><br>
                            CR: ".$cr."% ".tooltip(_CRTOOLTIP, 'right')." 
                           <div align=\"center\">".$button."</div>
                          
                            </div>
                        </section>
                    </div>";

                            }
                            echo '</div>';
                            }

                            foreach ($landings as $key => $value) {
                                if ($category && $category!=$value['category']) continue;
                                if ($lands_arr && in_array($key, $lands_arr)) continue;

                              if ($value['views'] > 0 && $value['subs'] > 0 ) $cr = round(($value['subs'] / $value['views'])*100, 1); else $cr = '-';

                              $button = " <a href=?m=".$module."&id=".$id."&tab=1&sellanding=".$key." class=\"btn btn-primary btn-sm\" style='color: white !important'><i class=\"fa fa-check\"></i>&nbsp; "._CHOSE."</a>";

                            if ($i==0) echo "<div class=\"row\">";

                      echo "<div class=\"col-lg-3\">
                        <section class=\"card\">
                            <div class=\"card-body text-secondary\">
                           № ".$key." &nbsp;&nbsp; "._CATEGORY.": ".$value['category']."<br>
                            <a href=\"https://" . $settings['domain_link'] . "/land.php?id=" . $id . "&lid=".$key."&subid=&tag=&price=0\" target=\"_blank\"><img src=".$value['preview']." border=0 width=100% class=landimg></a><br>
                            CR: ".$cr."% ".tooltip(_CRTOOLTIP, 'right')." 
                           <div align=\"center\">".$button."</div>
                          
                            </div>
                        </section>
                    </div>";

                            $i++;
                             if ($i==4) {echo "</div>"; $i=0; }
                            }

                        }
                        ?>
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
