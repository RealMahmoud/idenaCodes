function ajax_post(url, form_Data, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            try {
                var data = JSON.parse(xmlhttp.responseText);
            } catch(err) {
                console.log(err.message + " in " + xmlhttp.responseText);
                return;
            }
            callback(data);
        }
    };

    xmlhttp.open("POST", url, true);
    xmlhttp.send(form_Data);
}

function ajax_get(url, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            try {
                var data = JSON.parse(xmlhttp.responseText);
            } catch(err) {
                console.log(err.message + " in " + xmlhttp.responseText);
                return;
            }
            callback(data);
        }
    };

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function precise2(x) {
  return x.toLocaleString(undefined, {maximumFractionDigits:2});
}

function color(x) {
  if(x<0) {
     return '<span class="red">'+precise2(x)+'% &#x2193;</span>';
  } else {
    return '<span class="green">+'+precise2(x)+'% &#x2191;</span>';
  }
}
