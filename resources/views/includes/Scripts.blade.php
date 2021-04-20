
<Script>



$('.SideNavBtn').on('click',function(){
  
  $('#sidebar').toggleClass('SideNavActive')

})

//Error Sign Hide In ^ Sec

setTimeout(() => {
  $( ".ErrorSign" ).delay( 5000 ).fadeOut( 400 );
}, 2000);

//End Error Sign Hide In ^ Sec  



//Toast Start
var lang = "{{str_replace('_','-',app()->getLocale()) }}";

 if(lang ==="en"){
    toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "8000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
 }
 else{
  toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": true,
  "progressBar": false,
  "positionClass": "toast-top-left",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "8000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}
 }



var Timer=2000;

setTimeout(() => {
if(AuthType != null){
  $.ajax({
  method:"post",
  data:{ AuthType:AuthType, _token:"{{csrf_token()}}"  },
  url:"{{ route('CheckNM') }}",
  success:function(data){

   if(data.msg != 0){
    toastr["info"]('You Got '+data.msg+' Messages')
   }

   if(data.notif !=0){
       toastr["info"]('You Got '+data.notif+'Notification')
   }
    console.log(data)
  }
})
}
}, Timer);



//Toast End


//Change All Notificateion Status  When Click On Dropdown 

$(document).on('click','.NotifDrop',function(){

  //get Target Type
  var Type=$(this).data('type');

  $.ajax({
  method:'post',
  url:"{{ route('ChangeNotifPost') }}",
  data:{Type:Type,_token:"{{ csrf_token() }}"}})

})

//End Change All Notificateion Status  When Click On Dropdown 



 //get Categories And Providers for AddService Form

$(document).on('click',".AddService",function(){

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
            $(".CategoryInput").html(Categories)
  }
})
})

//End get Categories And Providers for AddService Form


//Provider Uplaod File

   //Set Value For OrderIdI On Upload file Form

   $('.UploadFileBtn').click(function(){
    var OrderId=$(this).data('orderid');

    $('input[name="OrderIdUplI"]').val(OrderId)
   })

    //End Set Value For OrderIdI On Upload file Form

$("#dropzoneOrderUp").dropzone({
    url: "{{route('OrderUploadFile')}}",
    addRemoveLinks : true,
    maxFilesize: 12,
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!',
    headers: {
        'X-CSRF-TOKEN':"{{ csrf_token() }}"
    }
});

//End Provider Uplaod File




//Send Message Set CustomerId input value

$('.SendMsgBtn').click(function(){
var CustomerId=$(this).data('customerid');
var OrderId=$(this).data('orderid');

$('input[name="MessageCustomerIdI"]').val(CustomerId);
$('input[name="MessageOrderIdI"]').val(OrderId);


})




//End Send Message Set CustomerId input value




</Script>

