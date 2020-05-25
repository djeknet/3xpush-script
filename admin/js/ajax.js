var xmlHttp

function aj(file, param, id) {
    xmlHttp = GetXmlHttpObject()
    if (xmlHttp == null) {
        alert("Browser does not support HTTP Request")
        return
    }
    var url = "ajax/" + file
    url = url + "?param=" + param
    url = url + "&id=" + id

    xmlHttp.onreadystatechange = function () {
        return stateChangedcom4(id);
    };
    xmlHttp.open("GET", url, true)
    xmlHttp.send(null)
}

function stateChangedcom4(id) {
    if (xmlHttp.readyState == 0) {
        document.getElementById("block-" + id).innerHTML = "<img src=images/load.gif width=16 height=16>.";
    }
    if (xmlHttp.readyState == 1) {
        document.getElementById("block-" + id).innerHTML = "<img src=images/load.gif width=16 height=16> ..";
    }
    if (xmlHttp.readyState == 2) {
        document.getElementById("block-" + id).innerHTML = "<img src=images/load.gif width=16 height=16> ...";
    }
    if (xmlHttp.readyState == 3) {
        document.getElementById("block-" + id).innerHTML = "<img src=images/load.gif width=16 height=16> ....";
    }
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
        document.getElementById("block-" + id).innerHTML = xmlHttp.responseText
    }

    $('#block-' + id + ' #ajax').click();
    validationForm();

    if(document.location.href.search('prices') !== -1) {
        parseScript($('#block-' + id).html());
    }

}

function GetXmlHttpObject() {
    var xmlHttp = null;
    try {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        //Internet Explorer
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}

function parseScript(_source) {
    var source = _source;
    var scripts = new Array();

    // Strip out tags
    while(source.toLowerCase().indexOf("<script") > -1 || source.toLowerCase().indexOf("</script") > -1) {
        var s = source.toLowerCase().indexOf("<script");
        var s_e = source.indexOf(">", s);
        var e = source.toLowerCase().indexOf("</script", s);
        var e_e = source.indexOf(">", e);

        // Add to scripts array
        scripts.push(source.substring(s_e+1, e));
        // Strip from source
        source = source.substring(0, s) + source.substring(e_e+1);
    }

    // Loop through every script collected and eval it
    for(var i=0; i<scripts.length; i++) {
        try {
            if (scripts[i] != '')
            {
                try  {          //IE
                    execScript(scripts[i]);
                }
                catch(ex)           //Firefox
                {
                    window.eval(scripts[i]);
                }

            }
        }
        catch(e) {
            // do what you want here when a script fails
            // window.alert('Script failed to run - '+scripts[i]);
            if (e instanceof SyntaxError) console.log (e.message+' - '+scripts[i]);
        }
    }
// Return the cleaned source
    return source;
}
