<?php require_once('config.inc.php'); ?>
<?php require_once('common.inc.php'); ?>
<!DOCTYPE html>
<!--
    WebPrint - Printing via Web Browser
    Copyright (C) 2015-2016 Benedikt Budig, Fabian Lipp
    2015 Lehrstuhl für Informatik I, Uni Würzburg

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
<head>
  <meta charset=="utf-8">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>  

  <title>Web Print</title>

  <style>
    #holder { border: 10px dashed #ccc; width: 100%; height: 400px; display: table; }
    #holder.hover { border: 10px dashed #0c0; }
    #holder img { display: block; margin: 10px auto; }
    #holder p { margin: 10px; font-size: 14px; text-align: center; }
    #holder p.placeholder { font-size: 30px; font-weight: bold; color: #ccc; vertical-align: middle; display: table-cell }
    #holder div.fileholder { display: table-cell; vertical-align: middle;  }
    progress { width: 100%; }
    progress:after { content: '%'; }
    .fail { background: #c00; padding: 2px; color: #fff; }
    .hidden { display: none !important;}
  </style>

</head>
<body>

  <div class="container" id="container">
    <h1>Web Print <small> easy pdf printing</small></h1>

    <hr>

    <div class="row">

      <div class="col-lg-8">
        <div id="holder"><p class="placeholder">Drop PDF here</p></div>
        <p id="upload" class="hidden"><label>Drag & drop not supported, but you can still upload via this input field:<br><input type="file"></label></p>
        <p id="filereader">File API &amp; FileReader API not supported</p>
        <p id="formdata">XHR2's FormData is not supported</p>
        <p id="progress">XHR2's upload progress isn't supported</p>
        <p style="display: none">Upload progress: <progress id="uploadprogress" min="0" max="100" value="0">0</progress></p>
      </div>

      <div class="col-lg-4">
        <div class="well">
          <div class="form-group">
            <label for="printer-select">Select Printer</label>
            <select class="form-control" id="printer-select">
              <?php
                foreach ($printers as $printerName => $printerDescription) {
                  echo '<option value="' . $printerName . '">' . $printerDescription . '</option>';
                }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="duplex-select">Print Mode (Duplex)</label>
            <select class="form-control" id="duplex-select">
              <option value="1">one-sided</option>
              <option value="2" selected>two-sided</option>
            </select>
          </div>
          <?php if (!checkIpRange()) { ?>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" class="form-control" />
          </div>
          <?php } ?>
          <button class="btn btn-primary btn-block" onclick="postfiles()"><span class="glyphicon glyphicon-print"></span> Print</button>
        </div>
        <div id="alert" class="hidden" role="alert">
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <span id="alertText"></span>
        </div>
      </div>
    </div>

    <script>
    var holder = document.getElementById('holder'),
        tests = {
          filereader: typeof FileReader != 'undefined',
          dnd: 'draggable' in document.createElement('span'),
          formdata: !!window.FormData,
          progress: "upload" in new XMLHttpRequest
        }, 
        support = {
          filereader: document.getElementById('filereader'),
          formdata: document.getElementById('formdata'),
          progress: document.getElementById('progress')
        },
        acceptedTypes = {
          'application/pdf': true
          //'image/png': true,
          //'image/jpeg': true,
          //'image/gif': true
        },
        progress = document.getElementById('uploadprogress'),
        fileupload = document.getElementById('upload');

    "filereader formdata progress".split(' ').forEach(function (api) {
      if (tests[api] === false) {
        support[api].className = 'fail';
      } else {
        // FFS. I could have done el.hidden = true, but IE doesn't support
        // hidden, so I tried to create a polyfill that would extend the
        // Element.prototype, but then IE10 doesn't even give me access
        // to the Element object. Brilliant.
        support[api].className = 'hidden';
      }
    });

    $('#alert .close').on('click', function(e) {
      $(this).parent().hide();
    });

    function previewfile(file) {
      holder.innerHTML = '<div class="fileholder"><img src="application-pdf.png"><p>' + file.name + ' ' + (file.size ? '(' + (file.size/1024|0) + ' KB)' : '') + '</div>';
      console.log(file);
    }

    function readfiles(files) {
      formData = tests.formdata ? new FormData() : null;
      for (var i = 0; i < files.length; i++) {
        if (tests.formdata) formData.append('file', files[i]);
        previewfile(files[i]);
      }
    }

    function postfiles() {
      var e = document.getElementById("printer-select");
      var printer = e.options[e.selectedIndex].value;
      e = document.getElementById("duplex-select");
      var duplex = e.options[e.selectedIndex].value;
      e = document.getElementById("password");
      var password = null;
      if (e !== null) {
        password = e.value;
      }

      // now post a new XHR request
      if (tests.formdata && typeof formData !== "undefined" && formData !== null) {
        if (password !== null) {
          formData.append('password', password);
        }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'print_file.php?p=' + printer + "&d=" + duplex);
        xhr.onload = function() {
          progress.value = progress.innerHTML = 100;
        };

        if (tests.progress) {
          xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
              var complete = (event.loaded / event.total * 100 | 0);
              progress.value = progress.innerHTML = complete;
            }
          }
        }

        xhr.onreadystatechange = function(){
          if ( xhr.readyState == 4 ) {
            var alertElement = document.getElementById("alert");
            var alertText = document.getElementById("alertText");
            if ( xhr.status == 200 ) {
              alertText.innerHTML = "<strong>Success:</strong> " + xhr.responseText;
              alertElement.className = " alert alert-success alert-dismissible";
              alertElement.style.display = "inherit";
              resetform();
            } else {
              alertText.innerHTML = "<strong>Error: </strong> "+ xhr.responseText;
              alertElement.className = "alert alert-danger alert-dismissible";
              alertElement.style.display = "inherit";
            }
          }
        };

        xhr.send(formData);
      }
    }

    function resetform() {
      formData = null;
      holder.innerHTML = '<p class="placeholder">Drop PDF here</p>';
    }

    if (tests.dnd) {
      holder.ondragover = function () { this.className = 'hover'; return false; };
      holder.ondragend = function () { this.className = ''; return false; };
      holder.ondragleave = function () { this.className = ''; return false; };
      holder.ondrop = function (e) {
        this.className = '';
        e.preventDefault();
        readfiles(e.dataTransfer.files);
      }
    } else {
      fileupload.className = 'hidden';
      fileupload.querySelector('input').onchange = function () {
        readfiles(this.files);
      };
    }

    </script>

  </div>

</body>
</html>
