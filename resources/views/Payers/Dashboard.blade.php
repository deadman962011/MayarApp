@extends('layout.master')

@section('Style')
 
<link rel="stylesheet" href="http://127.0.0.1:8000/webCode/css/style.css">

@endsection

@section('Content')
@include('includes.PayerNavbar')
@include('includes.error')
@include('includes.PayerSideNav')
<div id='Content'>
    <div class="btn-group center-block">
        <label class="btn btn-default"><input type="radio" name="GetIdI"  data-toggle="collapse" data-target="#GetById">get By Id</label>
        <label class="btn btn-default"><input type="radio" name="GetIdI"  data-toggle="collapse" data-target="#GetByQr">get By QrCode</label>
      </div>

      <br> 
      <br>
      <br>

      <div>
        <div id="GetById" class="collapse"> 
            <div  class="form-horizontal">

                <div class="form-group">
                    <input class="form-control" type="text" name="OrderIdI">
                </div>
                
                <div class="form-group">
                    <button type="submit" value="Get Order" class="btn btn-primary GetOrderSubmit  btn-block">
                        <div class="BtnVal">submit</div>
                    </button>
                </div>

            </div>
          </div>
    </div>
          <div id="GetByQr" class='collapse'>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="navbar-form navbar-left">
                        </div>
                        <div class="navbar-form navbar-right">
                            <select class="form-control" id="camera-select"></select>
                            <div class="form-group">
                                <input id="image-url" type="text" class="form-control" placeholder="Image url">
                                <button title="Decode Image" class="btn btn-default btn-sm" id="decode-img" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-upload"></span></button>
                                <button title="Image shoot" class="btn btn-info btn-sm" id="grab-img" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-picture"></span></button>
                                <button title="Play" class="btn btn-success btn-sm" id="play" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-play"></span></button>
                                <button title="Pause" class="btn btn-warning btn-sm" id="pause" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-pause"></span></button>
                                <button title="Stop streams" class="btn btn-danger btn-sm" id="stop" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-stop"></span></button>
                             </div>
                        </div>
                    </div>
                    <div class="panel-body text-center">
                        <div class="col-md-6">
                            <div class="well" style="position: relative;display: inline-block;">
                                <canvas width="320" height="184" id="webcodecam-canvas" style="transform: scale(1, 1);"></canvas>
                                <div class="scanner-laser laser-rightBottom" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-rightTop" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-leftBottom" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-leftTop" style="opacity: 0.5;"></div>
                            </div>
                            <div class="well" style="width: 100%;">
                                <label id="zoom-value" width="100">Zoom: 2</label>
                                <input id="zoom" onchange="Page.changeZoom();" type="range" min="10" max="30" value="20">
                                <label id="brightness-value" width="100">Brightness: 0</label>
                                <input id="brightness" onchange="Page.changeBrightness();" type="range" min="0" max="128" value="0">
                                <label id="contrast-value" width="100">Contrast: 0</label>
                                <input id="contrast" onchange="Page.changeContrast();" type="range" min="0" max="64" value="0">
                                <label id="threshold-value" width="100">Threshold: 0</label>
                                <input id="threshold" onchange="Page.changeThreshold();" type="range" min="0" max="512" value="0">
                                <label id="sharpness-value" width="100">Sharpness: off</label>
                                <input id="sharpness" onchange="Page.changeSharpness();" type="checkbox">
                                <label id="grayscale-value" width="100">grayscale: off</label>
                                <input id="grayscale" onchange="Page.changeGrayscale();" type="checkbox">
                                <br>
                                <label id="flipVertical-value" width="100">Flip Vertical: off</label>
                                <input id="flipVertical" onchange="Page.changeVertical();" type="checkbox">
                                <label id="flipHorizontal-value" width="100">Flip Horizontal: off</label>
                                <input id="flipHorizontal" onchange="Page.changeHorizontal();" type="checkbox">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="thumbnail" id="result">
                                <div class="well" style="overflow: hidden;">
                                    <img width="320" height="240" id="scanned-img" src="">
                                </div>
                                <div class="caption">
                                    <h3>Scanned result</h3>
                                    <p id="scanned-QR">Scanning ...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
          </div>
          <div class="Order"></div>
      </div>

      


@endsection

@section('Script')

   <script src="{{ url('webCode/js/qrcodelib.js') }}"></script>
   <script src="{{ url('webCode/js/webcodecamjs.js') }}"></script>
   

    <script>
    (function(undefined) {
        "use strict";

    function Q(el) {
        if (typeof el === "string") {
            var els = document.querySelectorAll(el);
            return typeof els === "undefined" ? undefined : els.length > 1 ? els : els[0];
        }
        return el;
    }
    var txt = "innerText" in HTMLElement.prototype ? "innerText" : "textContent";
    var scannerLaser = Q(".scanner-laser"),
        imageUrl = new Q("#image-url"),
        play = Q("#play"),
        scannedImg = Q("#scanned-img"),
        scannedQR = Q("#scanned-QR"),
        grabImg = Q("#grab-img"),
        decodeLocal = Q("#decode-img"),
        pause = Q("#pause"),
        stop = Q("#stop"),
        contrast = Q("#contrast"),
        contrastValue = Q("#contrast-value"),
        zoom = Q("#zoom"),
        zoomValue = Q("#zoom-value"),
        brightness = Q("#brightness"),
        brightnessValue = Q("#brightness-value"),
        threshold = Q("#threshold"),
        thresholdValue = Q("#threshold-value"),
        sharpness = Q("#sharpness"),
        sharpnessValue = Q("#sharpness-value"),
        grayscale = Q("#grayscale"),
        grayscaleValue = Q("#grayscale-value"),
        flipVertical = Q("#flipVertical"),
        flipVerticalValue = Q("#flipVertical-value"),
        flipHorizontal = Q("#flipHorizontal"),
       
        flipHorizontalValue = Q("#flipHorizontal-value");
        
    var args = {
        autoBrightnessValue: 100,
        resultFunction: function(res) {
            
            
            [].forEach.call(scannerLaser, function(el) {
                fadeOut(el, 0.5);
                setTimeout(function() {
                    fadeIn(el, 0.5);
                }, 300);
            });
            scannedImg.src = res.imgData;
            scannedQR[txt] = res.format + ": " + res.code;
      

            //Ajax Request Find Order 
            $.ajax({
                method:"post",
                url:"{{route('OrderOneAj')}}",
                data:{
                    WayI:'Qr',
                    QrCodeI:res.code,
                    _token:"{{ csrf_token() }}"
                },success:function(data){
                    if(data == 403 ){

                    $('.Order').html('');
                }
                else{
            

                    $('.Order').html('<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse42" class="" aria-expanded="true">Order :'+data.id+' </a></h4> </div><div id="collapse42" class="panel-collapse collapse in" aria-expanded="true" style=""><div class="panel-body"><div class="row"><div class="col-sm-12"><img src="data:image/svg+xml;base64,'+data.OrderQr+'" class="center-block"><br><h3>Orderd Service: <span><a href="#">'+data.service.ServiceName+'</a></span></h3><h3>Customer: <span><a href="#"> '+data.customer.CustUserName+' </a></span></h3><h3>Desception:</h3><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae eius non, nam nemo nulla magnam repellat sequi quod odio ipsa tenetur necessitatibus dolorem voluptates aliquid, natus blanditiis expedita ipsum iste.</p><h3>Order Files:</h3><ul class="OrderFileList"></ul><br><br><br><h3>Biling:</h3><table class="table table-light"><thead><tr><th>item</th><th>type</th><th>Price</th></tr></thead><tbody><tr><td>Blaxk Service</td><td>Service</td><td>540</td></tr></tbody><tfoot><tr><th></th><th></th><th>540</th></tr></tfoot></table></div></div></div><div class="panel-footer"><form action="" method="post" style="display: inline-block"><input type="hidden" name="OrderIdI" value="42"><input type="submit" value="Pay" class="btn btn-success"></form></div></div></div>')
                 

                    //Display Files
                    function list(list) {
                        let list1="";

                        list.map((item)=>{
                        list1 += '<li><a href="https://drive.google.com/file/d/1PvK_pGtrfZ0ZN-DHB4gvlQ8o_nKfvbf-"><img src="http://127.0.0.1/images/avatar.png"><h4>export-2020-02-21_23-46-31<span>bin</span></h4></a></li>';
                        });
                        return list1;
                        
                    }
                    var Files=list(data.files)

                    $('.OrderFileList').html(Files)
                }
                }
            })

            
            
        },
        getDevicesError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += p + ": " + error[p] + "\n";
            }
            alert(message);
        },
        getUserMediaError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += p + ": " + error[p] + "\n";
            }
            alert(message);
        },
        cameraError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            if (error.name == "NotSupportedError") {
                var ans = confirm("Your browser does not support getUserMedia via HTTP!\n(see: https:goo.gl/Y0ZkNV).\n You want to see github demo page in a new window?");
                if (ans) {
                    window.open("https://andrastoth.github.io/webcodecamjs/");
                }
            } else {
                for (p in error) {
                    message += p + ": " + error[p] + "\n";
                }
                alert(message);
            }
        },
        cameraSuccess: function() {
            grabImg.classList.remove("disabled");
        }
    };

    var decoder = new WebCodeCamJS("#webcodecam-canvas").buildSelectMenu("#camera-select", "environment|back").init(args);
    decodeLocal.addEventListener("click", function() {
        Page.decodeLocalImage();
    }, false);
    play.addEventListener("click", function() {
        if (!decoder.isInitialized()) {
            scannedQR[txt] = "Scanning ...";
        } else {
            scannedQR[txt] = "Scanning ...";
            decoder.play();
        }
    }, false);
    grabImg.addEventListener("click", function() {
        if (!decoder.isInitialized()) {
            return;
        }
        var src = decoder.getLastImageSrc();
        scannedImg.setAttribute("src", src);
    }, false);
    pause.addEventListener("click", function(event) {
        scannedQR[txt] = "Paused";
        decoder.pause();
    }, false);
    stop.addEventListener("click", function(event) {
        grabImg.classList.add("disabled");
        scannedQR[txt] = "Stopped";
        decoder.stop();
    }, false);
    Page.changeZoom = function(a) {
        if (decoder.isInitialized()) {
            var value = typeof a !== "undefined" ? parseFloat(a.toPrecision(2)) : zoom.value / 10;
            zoomValue[txt] = zoomValue[txt].split(":")[0] + ": " + value.toString();
            decoder.options.zoom = value;
            if (typeof a != "undefined") {
                zoom.value = a * 10;
            }
        }
    };
    Page.changeContrast = function() {
        if (decoder.isInitialized()) {
            var value = contrast.value;
            contrastValue[txt] = contrastValue[txt].split(":")[0] + ": " + value.toString();
            decoder.options.contrast = parseFloat(value);
        }
    };
    Page.changeBrightness = function() {
        if (decoder.isInitialized()) {
            var value = brightness.value;
            brightnessValue[txt] = brightnessValue[txt].split(":")[0] + ": " + value.toString();
            decoder.options.brightness = parseFloat(value);
        }
    };
    Page.changeThreshold = function() {
        if (decoder.isInitialized()) {
            var value = threshold.value;
            thresholdValue[txt] = thresholdValue[txt].split(":")[0] + ": " + value.toString();
            decoder.options.threshold = parseFloat(value);
        }
    };
    Page.changeSharpness = function() {
        if (decoder.isInitialized()) {
            var value = sharpness.checked;
            if (value) {
                sharpnessValue[txt] = sharpnessValue[txt].split(":")[0] + ": on";
                decoder.options.sharpness = [0, -1, 0, -1, 5, -1, 0, -1, 0];
            } else {
                sharpnessValue[txt] = sharpnessValue[txt].split(":")[0] + ": off";
                decoder.options.sharpness = [];
            }
        }
    };
    Page.changeVertical = function() {
        if (decoder.isInitialized()) {
            var value = flipVertical.checked;
            if (value) {
                flipVerticalValue[txt] = flipVerticalValue[txt].split(":")[0] + ": on";
                decoder.options.flipVertical = value;
            } else {
                flipVerticalValue[txt] = flipVerticalValue[txt].split(":")[0] + ": off";
                decoder.options.flipVertical = value;
            }
        }
    };
    Page.changeHorizontal = function() {
        if (decoder.isInitialized()) {
            var value = flipHorizontal.checked;
            if (value) {
                flipHorizontalValue[txt] = flipHorizontalValue[txt].split(":")[0] + ": on";
                decoder.options.flipHorizontal = value;
            } else {
                flipHorizontalValue[txt] = flipHorizontalValue[txt].split(":")[0] + ": off";
                decoder.options.flipHorizontal = value;
            }
        }
    };
    Page.changeGrayscale = function() {
        if (decoder.isInitialized()) {
            var value = grayscale.checked;
            if (value) {
                grayscaleValue[txt] = grayscaleValue[txt].split(":")[0] + ": on";
                decoder.options.grayScale = true;
            } else {
                grayscaleValue[txt] = grayscaleValue[txt].split(":")[0] + ": off";
                decoder.options.grayScale = false;
            }
        }
    };
    Page.decodeLocalImage = function() {
        if (decoder.isInitialized()) {
            decoder.decodeLocalImage(imageUrl.value);
        }
        imageUrl.value = null;
    };
    var getZomm = setInterval(function() {
        var a;
        try {
            a = decoder.getOptimalZoom();
        } catch (e) {
            a = 0;
        }
        if (!!a && a !== 0) {
            Page.changeZoom(a);
            clearInterval(getZomm);
        }
    }, 500);

    function fadeOut(el, v) {
        el.style.opacity = 1;
        (function fade() {
            if ((el.style.opacity -= 0.1) < v) {
                el.style.display = "none";
                el.classList.add("is-hidden");
            } else {
                requestAnimationFrame(fade);
            }
        })();
    }

    function fadeIn(el, v, display) {
        if (el.classList.contains("is-hidden")) {
            el.classList.remove("is-hidden");
        }
        el.style.opacity = 0;
        el.style.display = display || "block";
        (function fade() {
            var val = parseFloat(el.style.opacity);
            if (!((val += 0.1) > v)) {
                el.style.opacity = val;
                requestAnimationFrame(fade);
            }
        })();
    }
    document.querySelector("#camera-select").addEventListener("change", function() {
        if (decoder.isInitialized()) {
            decoder.stop().play();
        }
    });

     



}).call(window.Page = window.Page || {});   



        $(".collapse").on("show.bs.collapse",function(){
            $(".collapse.in").each(function(){
            $(this).collapse("hide");
            })
        })



        //Get Order 
        $(document).on('click','.GetOrderSubmit',function(){

            //get Order Id input Value
            var OrderId =$('input[name=OrderIdI]').val();
            
            //Activate Spinner On Submit Button
            $('.BtnVal').html('<div class="sk-chase "><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div></div>')

            //get Order By Ajax
            $.ajax({
                url:"{{route('OrderOneAj')}}",
                method:"Post",
                data:{WayI:"Id",IdI:OrderId,_token:"{{ csrf_token() }}" },
                success:function(data){
                
                if(data == 403 ){
                    console.log('Baddddd')
                    $('.BtnVal').html('bad')
                    $('.Order').html('');
                }
                if(data == 405){
                    console.log('Baddddd')
                    $('.BtnVal').html('bad')
                    $('.Order').html('Order paid');
                }
         
                    $('.BtnVal').html('goood')
                    
                    $('.Order').html('<div class="panel panel-success"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse42" class="" aria-expanded="true">Order :'+data.id+' </a></h4> </div><div id="collapse42" class="panel-collapse collapse in" aria-expanded="true" style=""><div class="panel-body"><div class="row"><div class="col-sm-12"><img src="data:image/svg+xml;base64,'+data.OrderQr+'" class="center-block"><br><h3>Orderd Service: <span><a href="#">'+data.service.ServiceName+'</a></span></h3><h3>Customer: <span><a href="#"> '+data.customer.CustUserName+' </a></span></h3><h3>Desception:</h3><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae eius non, nam nemo nulla magnam repellat sequi quod odio ipsa tenetur necessitatibus dolorem voluptates aliquid, natus blanditiis expedita ipsum iste.</p><h3>Order Files:</h3><ul class="OrderFileList"></ul><br><br><br><h3>Biling:</h3><table class="table table-light"><thead><tr><th>item</th><th>type</th><th>Price</th></tr></thead><tbody><tr><td>Blaxk Service</td><td>Service</td><td>540</td></tr></tbody><tfoot><tr><th></th><th></th><th>540</th></tr></tfoot></table></div></div></div><div class="panel-footer"><form action="{{ route("PayOrder") }}" method="post" style="display: inline-block"><input type="hidden" name="OrderIdI" value="'+data.id+'">{{ csrf_field() }}<input type="submit" value="Pay" class="btn btn-success"></form></div></div></div>')
                    console.log(data)

                    //Display Files
                    function list(list) {
                        let list1="";

                        list.map((item)=>{
                        list1 += '<li><a href="https://drive.google.com/file/d/1PvK_pGtrfZ0ZN-DHB4gvlQ8o_nKfvbf-"><img src="http://127.0.0.1/images/avatar.png"><h4>export-2020-02-21_23-46-31<span>bin</span></h4></a></li>';
                        });
                        return list1;
                        
                    }
                    var Files=list(data.files)

                    $('.OrderFileList').html(Files)
          

                }
            })
        })
        


    </script>
@endsection