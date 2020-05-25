<?php

/**
 * This is a control panel for the thumbnail creating
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * The PHP License, version 3.0
 *
 * Copyright (c) 1997-2005 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following url:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @author      Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license     http://www.php.net/license/3_0.txt
 *              The PHP License, version 3.0
 */

// {{{

class Thumbnail_Control2
{

    // {{{

    var $_css;
    var $_js;

    // }}}
    // {{{

    function Thumbnail_Control2()
    {
        $this->__construct();
    }

    // }}}
    // {{{

    function __construct()
    {
        $this->_loadCss();
        $this->_loadJs();
    }

    // }}}
    // {{{

    function showCss($inline=false)
    {
        if ( $inline ) {
            echo "\n\n<style type=\"text/css\">\n\n";
        }
        echo $this->_css;
        if ( $inline ) {
            echo "\n\n</style>\n\n";
        }
    }

    // }}}
    // {{{

    function showHtml()
    {
    }

    // }}}
    // {{{

    function showJavascript($inline=false)
    {
        if ( $inline ) {
            echo "\n\n<script language=\"javascript\" type=\"text/javascript\"><!--//--><![CDATA[//><!--\n\n";
        }
        echo $this->_css;
        if ( $inline ) {
            echo "\n\n//--><!]]></script>\n\n";
        }
    }

    // }}}
    // {{{

    function _loadCss()
    {
        $this->_css = '';
    }

    // }}}
    // {{{

    function _loadJs()
    {
        $this->_js  = '';
    }

    // }}}

}

// }}}
// {{{

class Thumbnail_Control
{

    // {{{ _showSelect()

    /**
     * Displays one row of the thumbnail control panel with the specific control
     *
     * @param  string  $name
     * @param  string  $options
     * @param  string  $values
     *
     * @result void
     * @access private
     * @see    Thumbnail::showHtml()
     */
    function _showSelect($name, $options, $value_list)
    {
        $name = strtolower($name);
        $control = $options['control'];
        $request = strtoupper($options['request']);
        $value = isset($GLOBALS['_' . $request][$name]) ? $GLOBALS['_' . $request][$name] : $options[$name];

?>
<tr>
<td><span><?=ucfirst($name)?>:</span></td>
<td>
<select name="<?=$name?>" id="<?=$name?>" onchange="<?=$control?>.set<?=ucfirst($name)?>()">
<?

        foreach ($value_list as $k => $v) {
            $s = $value == $k ? ' selected="selected" ' : '';

?>
<option <?=$s?> value="<?=$k?>"><?=$v?></option>
<?

        }

?>
</select>
</td>
</tr>
<?

    }

    // }}}
    // {{{ showHtml()

    /**
     * Displays html-formatted thumbnail control panel
     *
     * @param  string  $image   Path to the image
     * @param  array   $options The assoc.array of parameters. There are available parameters:
     *         <pre>
     *         control The name of the Javascript 'ThumbnailControl' class variable
     *         request The request method (default is post)
     *         action  The handler (default is the path of the current script)
     *         method  The method of thumbnail creating (default is 0 - Maximal Scale)
     *         width   The thumbnail width in pixels (default is 150)
     *         height  The thumbnail height in pixels (default is 150)
     *         percent The percent scaling (default is 0)
     *         halign  The horizontal align (default is 0 - Center)
     *         valign  The vertical align (default is 0 - Center)
     *         </pre>
     *
     * @result boolean If true the text had been printed
     * @access piblic
     */
    function showHtml($image, $options=array())
    {
        $def = array(
            'control' => 'ctrl_' . md5(time()),
            'request' => 'post',
            'action'  => $_SERVER['SCRIPT_NAME'],

            'method'  => 0,
            'width'   => 150,
            'height'  => 150,
            'percent' => 0,
            'halign'  => 0,
            'valign'  => 0,
        );

        list($maxWidth, $maxHeight) = @getimagesize($image);
        if ( empty($maxWidth) || empty($maxHeight) ) {
            return false;
        }

        foreach ($def as $k => $v) {
            if ( empty($options[$k]) ) {
                $options[$k] = $v;
            }
        }

        Thumbnail_Control::showJavascript();

?>
<table id="thumbnailControlFrame">
<tr valign="top">
<td>

<form id="controlForm" method="<?=strtolower($options['request'])?>" action="<?=$options['action']?>">

<table>
<tr>
<td><span>Border:</span></td>
<td><input type="button" name="borderColor" value="#ffffff" onclick="<?=$options['control']?>.setBorder()" /></td>
</tr>

<?

        $value_list = array('Max Scale', 'Min Scale', 'Crop');
        Thumbnail_Control::_showSelect('method', $options, $value_list);

        $value_list = array();
        for ($i = $options['width']; $i <= $maxWidth; $i += 50) {
            $value_list[$i] = $i;
        }
        Thumbnail_Control::_showSelect('width', $options, $value_list);

        $value_list = array();
        for ($i = $options['height']; $i <= $maxHeight; $i += 50) {
            $value_list[$i] = $i;
        }
        Thumbnail_Control::_showSelect('height', $options, $value_list);

        $value_list = array('0.0' => 0, '0.1' => 10, '0.2' => 20, '0.3' => 30, '0.4' => 40, '0.5' => 50, '0.6' => 60, '0.7' => 70, '0.8' => 80, '0.9' => 90, '1.0' => 100);
        Thumbnail_Control::_showSelect('percent', $options, $value_list);

        $value_list = array(-1 => 'Left', 0 => 'Center', +1 => 'Right');
        Thumbnail_Control::_showSelect('halign', $options, $value_list);

        $value_list = array(-1 => 'Top', 0 => 'Center', +1 => 'Bottom');
        Thumbnail_Control::_showSelect('valign', $options, $value_list);

?>

<tr>
<td><span>&nbsp;</span></td>
<td><input type="submit" value="Save" /></td>
</tr>
</table>

</form>

</td>
<td>

<div style="position: relative;">
<img id="imageFrame" src="<?=$image?>" style="border: none; height: <?=$maxHeight?>px; width: <?=$maxWidth?>px;" border="0" height="<?=$maxHeight?>" width="<?=$maxWidth?>" alt="" />
<div id="thumbnailFrame" style="border-style: solid; border-width: 1px; position: absolute;">&nbsp;</div>
</div>

</td>
</tr>
</table>

<script language="javascript" type="text/javascript"><!--//--><![CDATA[//><!--

var <?=$options['control']?> = new TumbnailControl(
    document.getElementById('controlForm'),
    document.getElementById('thumbnailFrame'), 
    document.getElementById('imageFrame'))

//--><!]]></script>
<?

        return true;
    }

    // }}}
    // {{{ showCSS()

    /**
     * Displays the default CSS tables for the tumbnail control panel
     * This method protected from the double call
     *
     * @param  string  $standalone If true it prints the CSS within opening and closing tags <style></style>
     *
     * @result void
     * @access piblic
     */
    function showCSS($standalone=true)
    {
        static $secondCall = false;
        if ( $secondCall ) {
            return;
        }
        $secondCall = true;

        if ( $standalone ) {

?>

<style type="text/css"><!--

<?

        }

?>
/**
 * This is CCS of control panel for the thumbnail creating
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * The PHP License, version 3.0
 *
 * Copyright (c) 1997-2005 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following url:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @author      Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license     http://www.php.net/license/3_0.txt
 *              The PHP License, version 3.0
 */

table#thumbnailControlFrame
{
}

form#controlForm
{
}

form#controlForm table
{
}

form#controlForm span
{
    font-family: Verdana, Tahoma, 'Courier New', sans-serif;
    font-size: 10px;
    font-weight: bold;
}

form#controlForm input
{
    border: 1px solid #000;
    width: 90px;
}

form#controlForm select
{
    width: 90px;
}

<?
        if ( $standalone ) {

?>

--></style>

<?

        }
        return true;
    }

    // }}}
    // {{{ showJavascript()

    /**
     * Displays the Javascript 'ThumbnailControl' class for the tumbnail control panel
     * This method protected from the double call and can be called from the Thumbnail_Control::showHtml() or separate
     *
     * @param  string  $standalone If true it prints the Javascript within opening and closing tags <script></script>
     *
     * @result void
     * @access piblic
     */
    function showJavascript($standalone=true)
    {
        static $secondCall = false;
        if ( $secondCall ) {
            return;
        }
        $secondCall = true;

        if ( $standalone ) {

?>

<script language="javascript" type="text/javascript"><!--//--><![CDATA[//><!--

<?

        }

?>
/**
 * This is Javascript of control panel for the thumbnail creating
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * The PHP License, version 3.0
 *
 * Copyright (c) 1997-2005 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following url:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @author      Ildar N. Shaimordanov <ildar-sh@mail.ru>
 * @license     http://www.php.net/license/3_0.txt
 *              The PHP License, version 3.0
 */

/**
 * Constructor
 *
 * @param  object  aControlForm    The form reference for control the thumbnail frame
 * @param  object  aThumbnailFrame The thumbnail  frame
 * @param  object  aImageFrame     The original image frame
 *
 * @result TumbnailControl
 * @access public
 */
function TumbnailControl(aControlForm, aThumbnailFrame, aImageFrame)
{
    /**
     * Control form (select method, width, height, aligns)
     */
    var controlForm = null;

    /**
     * The thumbnail frame
     */
    var thumbnailFrame = null;

    /**
     * The original image frame
     */
    var imageFrame = null;

    /**
     * Upper-left corner of the thumbnail frame within the original image
     * Width and height of the original image
     */
    var X = 0;
    var Y = 0;
    var W = 0;
    var H = 0;

    /**
     * Thumbnail frame border color
     */
    var borderColor;

    /**
     * Width and height of the thumbnail frame
     */
    var width   = 0;
    var height  = 0;
    var percent = 0;

    /**
     * Aligns of the thumbnail frame
     */
    var hAlign = 0;
    var vAlign = 0;

    /**
     * Secondary variable for temporary bypass of draw
     */
    var drawBypass = false;

    /**
     * Secondary variable for temporary bypass of draw
     */
    var htmlColorSelect = '';
    htmlColorSelect += '<body style="margin: 0; padding: 0;" onblur="window.close();">';
    htmlColorSelect += '<' + 'script>var ctrl = null;<' + '/script>';
    htmlColorSelect += '<table border="0" cellpadding="5" cellspacing="2" style="font-family: \'Courier New\', Verdana, Tahoma, sans-serif; font-size: 11px; width: 100%;">';
    for (var r = 0x00; r < 0x100; r += 0x33) {
        for (var g = 0x00; g < 0x100; g += 0x33) {
            htmlColorSelect += '<tr>';
            for (var b = 0x00; b < 0x100; b += 0x33) {
                var rgb;
                rgb = '000000' + ((0x100 * r + g) * 0x100 + b).toString(0x10);
                rgb = rgb.substr(rgb.length - 6);
                htmlColorSelect += '<td '
                    + 'style="background-color: #' + rgb + '; color: ' + (g > 0x80 ? '#000' : '#fff') + ';" '
                    + 'onclick="if ( opener && ctrl ) { ctrl.drawBorderColorButton(' + "'#" + rgb + "'" + '); }" '
                    + 'ondblclick="if ( opener && ctrl ) { ctrl.drawBorderColorButton(' + "'#" + rgb + "'" + '); window.close(); }" '
                    + '>' + rgb + '</td>';
            }
            htmlColorSelect += '</tr>';
        }
    }
    htmlColorSelect += '</table>';
    htmlColorSelect += '</body>';

    /**
     * Toggle of the thumbnail frame
     *
     * @param  string  display The value for the thumbnail frame display
     * @result void
     * @access private
     */
    this.toggleFrame = function(display)
    {
        thumbnailFrame.style.display = display;
    }

    /**
     * Displays the thumbnail frame with calculated border, width, height and aligns
     *
     * @param  void
     * @result void
     * @access private
     * @see    ThumbnailControl.setXXX() methods
     */
    this.draw = function()
    {
        if ( drawBypass ) {
            return;
        }

        thumbnailFrame.style.borderColor = borderColor;
        thumbnailFrame.style.left = X + 'px';
        thumbnailFrame.style.top = Y + 'px';
        thumbnailFrame.style.width = width + 'px';
        thumbnailFrame.style.height = height + 'px';
    }

    /**
     * Draws the border color of the thumbnail frame and the appropriate control button
     *
     * @param  string  value The value of the border color in the format #RRGGBB
     * @result void
     * @access private
     * @see    ThumbnailControl.draw()
     */
    this.drawBorderColorButton = function(value)
    {
        controlForm.borderColor.style.color = (value.replace(/^#/, '0x') & 0x00ff00) >> 8 > 0x80 ? '#000' : '#fff';
        //controlForm.borderColor.style.color = 
        borderColor = 
        controlForm.borderColor.style.backgroundColor = 
        controlForm.borderColor.value = value;
        this.draw();
    }

    /**
     * Sets the border color selector for the thumbnail frame
     *
     * @param  void
     * @result void
     * @access public
     * @see
     */
    this.setBorder = function()
    {
        var sw = 350;
        var sh = 350;
        var w = window.open('', 'ColorSelector', 'directories=no,location=no,menubar=no,resizable=no,scrollbars=yes,status=no,toolbar=no,height=' + sh + ',width=' + sw);
        w.document.writeln(htmlColorSelect);
        w.ctrl = this;
    }

    /**
     * Set the thumbnail creating method
     * Toggle the thumbnail frame, enable/disable controls in compliance with the method
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.toggleFrame(), ThumbnailControl.setPercent()
     */
    this.setMethod = function()
    {
        var saveDrawBypass = drawBypass;
        drawBypass = true;

        var method = controlForm.method.options[controlForm.method.selectedIndex].value;

        if ( method == 2 ) {
            //
            // Crop method
            //

            // All controls are enabled
            controlForm.borderColor.disabled = 
            controlForm.width.disabled = 
            controlForm.height.disabled = 
            controlForm.percent.disabled = 
            controlForm.halign.disabled = 
            controlForm.valign.disabled = false;

            // The thumbnail frame is visible
            this.toggleFrame('');

            // If it is necessary calculate the thumbnail frame in percent
            this.setPercent()
        } else if ( method == 1 ) {
            //
            // Minimal scale method
            //

            // The border color, aligns controls are enabled
            controlForm.borderColor.disabled = 
            controlForm.halign.disabled = 
            controlForm.valign.disabled = false;
            // The width, height and percent controls are disabled
            controlForm.width.disabled = 
            controlForm.height.disabled = 
            controlForm.percent.disabled = true;

            width = height = ( W > H ) ? H : W;

            // The thumbnail frame is visible
            this.toggleFrame('');

            // Calculate the thumbnail frame aligns
            this.setHalign();
            this.setValign();
        } else {
            //
            // Maximal scale method
            //

            // All controls are disabled
            controlForm.borderColor.disabled = 
            controlForm.width.disabled = 
            controlForm.height.disabled = 
            controlForm.percent.disabled = 
            controlForm.halign.disabled = 
            controlForm.valign.disabled = true;

            // The thumbnail frame is invisible
            this.toggleFrame('none');
        }

        drawBypass = saveDrawBypass;

        this.draw();
    }

    /**
     * Set the width of the thumbnail
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.setHalign()
     */
    this.setWidth = function()
    {
        width = ( percent > 0 )
            ? Math.floor(W * percent)
            : controlForm.width.options[controlForm.width.selectedIndex].value;
        this.setHalign();
    }

    /**
     * Set the height of the thumbnail
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.setValign()
     */
    this.setHeight = function()
    {
        height = ( percent > 0 )
            ? Math.floor(H * percent)
            : controlForm.height.options[controlForm.height.selectedIndex].value;
        this.setValign();
    }

    /**
     * Set the width and height of the thumbnail
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.setWidth(), ThumbnailControl.setHeight()
     */
    this.setPercent = function()
    {
        percent = controlForm.percent.options[controlForm.percent.selectedIndex].value;

        var saveDrawBypass = drawBypass;
        drawBypass = true;

        this.setWidth();
        this.setHeight();

        drawBypass = saveDrawBypass;

        this.draw();
    }

    /**
     * Set the horizontal align of the thumbnail
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.draw()
     */
    this.setHalign = function()
    {
        hAlign = controlForm.halign.options[controlForm.halign.selectedIndex].value;
        if ( hAlign < 0 ) {
            X = 0;
        } else if ( hAlign > 0 ) {
            X = W - width;
        } else {
            X = Math.floor((W - width) / 2);
        }
        this.draw();
    }

    /**
     * Set the vertical align of the thumbnail
     *
     * @param  void
     * @result void
     * @access public
     * @see    ThumbnailControl.draw()
     */
    this.setValign = function()
    {
        vAlign = controlForm.valign.options[controlForm.valign.selectedIndex].value;
        if ( vAlign < 0 ) {
            Y = 0;
        } else if ( vAlign > 0 ) {
            Y = H - height;
        } else {
            Y = Math.floor((H - height) / 2);
        }
        this.draw();
    }

    controlForm = aControlForm;
    controlForm.borderColor.style.fontFamily = '\'Courier New\', Verdana, Tahoma, sans-serif';
    controlForm.borderColor.style.fontSize = '10px';

    thumbnailFrame = aThumbnailFrame;

    imageFrame = aImageFrame;
    W = imageFrame.style.width.substr(0, imageFrame.style.width.length - 2);
    H = imageFrame.style.height.substr(0, imageFrame.style.height.length - 2);

    drawBypass = true;

    this.drawBorderColorButton('#ffffff');
    this.setMethod();

    drawBypass = false;

    this.draw();
}

<?

        if ( $standalone ) {

?>

//--><!]]></script>

<?

        }
    }

    // }}}

}

// }}}

?>