@extends('layout.master')


@section('Content')
@include('includes.ProviderNavbar')
@include('includes.error')
@include('includes.ProvidersSideNav')
<div id='ContentBlank'>

        <div class="row">
            
            @foreach ($Services as $Service)
                <div class="col-sm-6">
                    <div class=" ServiceBox">
                      <h4 class="text-bold" >{{$Service['ServiceName']}}</h4>
                        <img src="https://drive.google.com/thumbnail?id={{$Service['ServiceThumb']}}&sz=w320&sz=h200" class="">
                        <div class="ServiceOptions btn-group">
                          @if ($Service['ServiceStatus'] == 1)
                           <button class='btn btn-primary StatusBtn' data-status={{$Service['ServiceStatus']}} data-serviceid='{{$Service['id']}}'> Suspend</button>
                           @elseif($Service['ServiceStatus'] == 0)
                           <button class='btn btn-success StatusBtn' data-status={{$Service['ServiceStatus']}} data-serviceid='{{$Service['id']}}'>Publish</button>
                          @endif
                          <button class='btn btn-warning UpdateSerBtn' data-toggle="modal" data-target="#UpdateService" data-ServiceId='{{$Service['id']}}' >Update</button>
                          <button class='btn btn-danger DelSerBtn' data-toggle="modal" data-target="#DelService" data-Serviceid="{{$Service['id']}}" >Delete</button>
                      </div>
                    </div>
                </div>
            @endforeach

         </div>

</div>


<!--Update Service  Modal -->
<div id="UpdateService" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Update Service Modal</h4>
        </div>
        <div class="modal-body">
          <div class="sk-chase  UpdateServiceSpinner">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
          </div>
          <form method="post" action="{{ route('UpdateService') }}" class="form-horizontal UpdateServiceForm" enctype="multipart/form-data">
  
            <!-- Service Name Input -->
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ServiceName') }}" name="ServiceNameUI" class="form-control" required></div>
            </div>
  
            <!-- Service Thumbnail Input -->
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1"><input type="file" placeholder="Service Thumbnail" name="ServiceThumbnailUI" class="form-control" ></div>
            </div>
  
            <!-- Service Price Input -->
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1"><input type="text" placeholder="{{ trans('lang.ServicePrice') }}" name="ServicePriceUI" class="form-control" required></div>
            </div>
  
            <!-- Service Category Input -->
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                    <select name="ServiceCatUI"  class="CategoryInputSerUp form-control" required>
                    </select>
                </div>
            </div>
  
            <!-- Service Descreption Input -->
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                    <textarea name="ServiceDescUI" placeholder="{{ trans('lang.ServiceDesc') }}" rows="15" class="form-control" required></textarea>
                </div>
            </div>
  
            <!--- Service Upgrades List -->
            <div class=' UpdatesList'>
                <div class='UpdateSerAddBtn UpdateOne'>
                 <span class='glyphicon glyphicon-plus' ></span>
                 <p>{{ trans('lang.NewUpdate') }} </p>
                </div>
                  <div class="UpgradeFormCollapse collapse">
                    <div class="form-group">
                      <div class="col-sm-10 col-sm-offset-1">
                        <input class='form-control' type='text' placeholder="{{ trans('lang.SerUpTitle') }}" name='ServiceUpTitleI' />
                      </div>
                      
                    </div>
  
                    <div class="form-group">
                      <div class="col-sm-10 col-sm-offset-1">
                       <input class='form-control' type='text' placeholder="{{ trans('lang.SerUpPrice') }}" name='ServiceUpPriceI' />
                      </div>
                    </div>
                   
                   <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-1">
                     <textarea class='form-control'  placeholder='{{ trans('lang.SerUpDesc') }}' name='ServiceUpDescI' rows='20'></textarea>
                    </div>
                   </div>
  
                   <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-1">
                     <input type="button" value="{{ trans('lang.Save') }}" class='btn btn-success btn-block SaveUpgradeBtn' >
                    </div>
                   </div>
                  </div>
  
                  <div class="UpgradesListAj"></div>
            </div>
  
            <br>
  
         {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="hidden" name="ServiceIdUI" >
          <input type="submit" value="{{ trans('lang.Update') }}" class="btn btn-primary">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('lang.Cancel') }}</button>
        </div>
      </div>
  
    </div>
  </div>
  <!-- End Update Service Modal -->
  
  
  
  
  
  <!-- Start Delete Service Modal -->
  
  <div id="DelService" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Service Modal</h4>
        </div>
        <div class="modal-body">
          
          
         <form action="{{ route('DeleteService') }}" method="post" class="form-horizontal" >
         
          <div class="form-group">
            <div class="col-sm-10 col-sm-offset-1">
              <input type="text" name="ProviderPassI" class="form-control" placeholder="Provider Password">
            </div>
          </div>
  
          <input type="hidden" name="DelSerIdI">
          {{ csrf_field() }}
        </div>
        <div class="modal-footer">
          <input type="submit" value="Delete" class="btn btn-danger">
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('lang.Cancel') }}</button>
        </div>
      </div>
  
    </div>
  </div>
  
  
  
  <!-- End Delete Service Modal -->
  
  
  <!-- File Upload Modal -->
  
  <!-- Modal -->
  <div id="FileUplaodModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <main>
            <section>
                <div id="dropzone dropzoneOrderUp">
                    <form  class="dropzone dz-clickable" id="dropzoneOrderUp" enctype="multipart/form-data" style="border: 2px #004fff dashed;background: gainsboro;">
                        {{-- <span class='center-block	glyphicon glyphicon-download-alt'></span> --}}
                        <div class="dz-message">Drop files here or click to upload.
                        </div>
                        <input type="hidden" name="OrderIdUplI" >
                        {{ csrf_field() }}
                    </form>
                </div>
            </section>
        </main>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>
  
  <!-- End File Upload Modal -->
  
  
  
  <!-- Start Send Message Modal -->
  
  <div id="SendMessage" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Send Message Modal</h4>
        </div>
        <div class="modal-body">
          <form action="{{route('OrderSendMessage')}}" method="post" class="form-horizontal">
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <input type="text" name="MessageTitleI" class="form-control" placeholder="Message Title Input" >
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <select name="MessageTargetI" class='form-control' >
                  <option value="Customer">Customer :</option>
                  <option value="Admins">Administrators</option>
                </select>
              </div>
            </div>
  
            <div class="form-group"> 
             <div class="col-sm-10 col-sm-offset-1">
              <select name="MessageSubjectI" class="form-control" >
                <option value="Customer Order">Customer Order</option>
                <option value="Technical problem">Technical problem</option>
                <option value="Additional information to order">Additional information to order</option>
              </select>
             </div>
            </div>
  
            <div class="form-group">
              <div class="col-sm-10 col-sm-offset-1">
                <textarea name="MessageBodyI" placeholder="Message Body Input" rows="10" class="form-control"></textarea>
                <input type="hidden" name="MessageCustomerIdI">
                <input type="hidden" name="MessageOrderIdI">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          {{ csrf_field() }}
          <input type="submit" value="Send" class="btn btn-success"> 
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- End Send Message Modal -->

@endsection

@section('Script')
    

<script>
  


//get Service For Update Service Form Start 


$('.UpdateSerBtn').on('click',function(){


$('.UpdateServiceSpinner').show();
$('.UpdateServiceForm').hide();

var ServiceId=$(this).data('serviceid');

//  console.log(ServiceId)

var route='{{ route("ServiceOne","ServiceId") }}';
var url=route.replace('ServiceId',ServiceId);
$.ajax({
 method:'get',
 url:url,
 success:function(dataServ){

//get Categories
 function list(list,type) {
     let list1="";

     if(type == "Category"){
         list.map((item)=>{
       list1 += '<option value="'+item.id+'" >'+item.CategoryName+'</option>';
     });
     return list1;
     }
 }
 $.ajax({
   method:'post',
   url:"{{ route('getCatProAjax') }}",
   data:{_token:"{{ csrf_token() }}"},
   success:function(data){     
     var Categories=list(data['Categories'],'Category');
     $(".CategoryInputSerUp").html(Categories)
   }
 })
 //end Get Categories


  //fill input Fields
  $('input[name="ServiceNameUI"]').val(dataServ.item.ServiceName);
  $('input[name="ServicePriceUI"]').val(dataServ.item.ServicePrice);
  $('textarea[name="ServiceDescUI"]').val(dataServ.item.ServiceDesc);
  $('input[name="ServiceIdUI"]').val(dataServ.item.id);

  //Set add UpgradeBtn Serviceid data
  $('.SaveUpgradeBtn').data('serviceid',ServiceId);
  $('.UpgradesListAj').html('<div class="sk-chase "> <div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div></div>')
  //get Service Upgrades
   $.ajax({
       method:'post',
       url:"{{ route('GetUpgrades') }}",
       data:{
         ServiceIdI:ServiceId,
         _token:"{{ csrf_token() }}"
         },
       success:function(data){ 

         // get Upgrades List

         function Uplist(list) {
             let list1="";
                 list.map((item)=>{
              // list1 += '<div class="UpgradeOne" ><h4>'+item.UpgradeTitle+'</h4> <button  type="button" class="DelUpgradeBtn" data-UpgId="'+item.id+'" data-serviceid="'+item.ServiceId+'" >X</button> </div>';
              list1 +='<div class="panel panel-default"><div class="panel-body"><button style="float:right"  type="button" class="DelUpgradeBtn" data-UpgId="'+item.id+'" data-serviceid="'+item.ServiceId+'" >X</button><h4>'+item.UpgradeTitle+'</h4><p>'+item.UpgradeDesc+' </p></div></div>';
             });
             return list1;
         }
         var UpgradesList=Uplist(data[1].Upgrades)
         $('.UpgradesListAj').html(UpgradesList)
       }
     }) 

  $('.UpdateServiceSpinner').hide();
  $('.UpdateServiceForm').show();  
 
 }
})

})

//get Service For Update Service Form End 



//Add Service Upgrades Form Collpase Start

$(".UpdateSerAddBtn").click(function(){
    $(".UpgradeFormCollapse").collapse('toggle');
  });

//Add Service Upgrades Form Collpase End





//Add Service Upgrades Send Request Start

$('.SaveUpgradeBtn').click(function(){

 
//get Add Upgrade Form Values
 var SerUpTitle=$('input[name="ServiceUpTitleI"]');
 var SerUpPrice=$('input[name="ServiceUpPriceI"]');
 var SerUpDesc=$('textarea[name="ServiceUpDescI"]');
 var ServiceIdUp=$(this).data('serviceid');


 //Save Upgrade And Refresh Upgrades List
 $.ajax({
    method:'post',
    url:"{{ route('SaveUpgrade') }}",
    data:{
      SerUpTitleI:SerUpTitle.val(),
      SerUpPriceI:SerUpPrice.val(),
      SerUpDescI:SerUpDesc.val(),
      ServiceIdI:ServiceIdUp,
      _token:"{{ csrf_token() }}"
      },
      statusCode: {
      400: function() {
        toastr["danger"]("Validation Error")
      }
    },
    success:function(data){ 

      //Fetch Toaster
      toastr["success"]("Service upgrade Created")

      //Collpas Form 
      $(".UpgradeFormCollapse").collapse('toggle');

      //Empty inputs
      SerUpTitle.val(null),
      SerUpPrice.val(null),
      SerUpDesc.val(null),

      //Loading Spinner
      $('.UpgradesListAj').html('<div class="sk-chase "> <div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div></div>')


      //Refresh Upgrades List
      $.ajax({
        method:'post',
        url:"{{ route('GetUpgrades') }}",
        data:{
          ServiceIdI:ServiceIdUp,
          _token:"{{ csrf_token() }}"
          },
        success:function(data){ 

          // get Upgrades List

          function Uplist(list) {
              let list1="";
                  list.map((item)=>{
                list1 += '<div class="panel panel-default"><div class="panel-body"><button style="float:right"  type="button" class="DelUpgradeBtn" data-UpgId="'+item.id+'" data-serviceid="'+item.ServiceId+'" >X</button><h4>'+item.UpgradeTitle+'</h4><p>'+item.UpgradeDesc+' </p></div></div>';
              });
              return list1;
          }
          var UpgradesList=Uplist(data[1].Upgrades)
          $('.UpgradesListAj').html(UpgradesList)
        }
      }) 
    }
  })  
})

//Add Service Upgrades Send Request End




//Start Delete Service Upgrades Request 

$(document).on('click','.DelUpgradeBtn',function(){

  //Get Upgrade Id
  var UpgradeId=$(this).data('upgid');
  var ServiceIdUp=$(this).data('serviceid');
  console.log(UpgradeId)

  $.ajax({
    url:"{{route('DelUpgrade')}}",
    method:'post',
    data:{
      UpgradeIdI:UpgradeId,
      _token:"{{ csrf_token() }}"
    },
    statusCode: {
          400:function() {
           
            //Fetch Danger  Toastr 
            toastr["danger"]("Somthing Wrong")
          },
          200:function(){

            //Fetch Succsess Toastr 
            toastr["success"]("Service upgrade Deleted")

            //Loading Spinner
            $('.UpgradesListAj').html('<div class="sk-chase "> <div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div><div class="sk-chase-dot"></div></div>')


            //Refresh Upgrades List
            $.ajax({
                method:'post',
                url:"{{ route('GetUpgrades') }}",
                data:{
                  ServiceIdI:ServiceIdUp,
                  _token:"{{ csrf_token() }}"
                  },
                success:function(data){ 

                  // get Upgrades List

                  function Uplist(list) {
                      let list1="";
                          list.map((item)=>{
                        list1 += '<div class="panel panel-default"><div class="panel-body"><button style="float:right"  type="button" class="DelUpgradeBtn" data-UpgId="'+item.id+'" data-serviceid="'+item.ServiceId+'" >X</button><h4>'+item.UpgradeTitle+'</h4><p>'+item.UpgradeDesc+' </p></div></div>';
                      });
                      return list1;
                  }
                  var UpgradesList=Uplist(data[1].Upgrades)
                  $('.UpgradesListAj').html(UpgradesList)
                }
              }) 
            }
    },
  })


})
//End Delete Service Upgrades Request 


//Start Delete Service 


$(document).on('click','.DelSerBtn',function(){



var ServiceId=$(this).data('serviceid');

$('input[name="DelSerIdI"]').val(ServiceId);



})


//end Delete Service 


//Change Service Status Start

$('.StatusBtn').on('click',function(){

var SerStatus=$(this).data('status');
var SerId=$(this).data('serviceid');

function ChangeSerStatusAj(SerIdAttr,SerStatusAttr) {
   $.ajax({
    url:"{{route('ChangeStatusSer')}}",
    method:'post',
    data:{
      ServiecIdI:SerIdAttr,
      SerStausI:SerStatusAttr,
      _token:"{{csrf_token()}}"
    },
    success:function(data){
       return console.log('done')
    }
  })
}

if(SerStatus == 0){
  console.log('Suspended')

  ChangeSerStatusAj(SerId,SerStatus)
  //Fetch Success Toaster 
  toastr["success"]("Service Successfully Published")

  //remove success class And Put Primary Class
  $(this).removeClass('btn-success')
  $(this).addClass('btn-primary')

  $(this).data('status',1)
  //Change Text inside Button
  $(this).html("suspend");
}

if(SerStatus == 1){
  console.log('Published')

  ChangeSerStatusAj(SerId,SerStatus)
  //Fetch Success Toaster 
  toastr["success"]("Service Successfully Suspended")

  //remove Primary class And Put Success Class
  $(this).removeClass('btn-primary')
  $(this).addClass('btn-success')

  $(this).data('status',0)
  //Change Text inside Button
  $(this).html("Publish");
}
})

//Change Service Statis End


</script>

@endsection