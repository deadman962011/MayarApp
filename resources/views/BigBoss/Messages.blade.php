@extends('layout.master')


@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')
<div id='Content' style="padding: 0">
    <div class="Message container-fluid" style="padding:0">
        <div  class="ContactsBlock col-sm-4  col-xs-12" >
            <ul >
             @foreach ($Contacts as $Contact)
             <li class="MsgContact" >
                <a data-toggle="pill" href="#Contact{{$Contact['id']}}" data-contact='{{$Contact['id']}}' class='contactBtn' >
                    <img src="http://127.0.0.1/images/avatar.png" class='pull-left' alt="Avatar" style='padding:3px'>
                    <h5>{{ $Contact['Customer']['CustFirstName'] ." ". $Contact['Customer']['CustLastName'] }}</h5>
                </a>
             </li>
             <li class="divider"></li>

             @endforeach
            </ul>



        </div>
        <div class="ChatBlock col-sm-8 col-xs-12 " >
            @foreach ($Contacts as $ContactMsg)
            <div id="Contact{{$ContactMsg['id']}}" class="tab-pane fade ChatBox ">

                @foreach ($ContactMsg['Messages'] as $msg)
                @if ($msg['MessageSourceType'] == 0)
               
                <div class='MsgMe'>
                    <h5>
                       {{$msg['MessageValue']}}
                    </h5>
               </div>
             
                @elseif ($msg['MessageSourceType'] == 1)
               
                 <div class='MsgMe'>
                     <h5>
                        {{$msg['MessageValue']}}
                     </h5>
                </div>
              
                @elseif($msg['MessageSourceType'] == 2)
                
                 <div class='MsgHim'>
                    <h5>
                       {{$msg['MessageValue']}}
                    </h5>
               </div>
                 
                @endif
                
                @endforeach
                



            </div>
            @endforeach
            <div class="SendMsgForm">
                <form method='post' class="form-vertical">
                    <div class="form-group">
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name='MsgValueI'>
                            <input type="hidden" name="ContactIdI">
                        </div>
                    </div>
                    <div class="form-group">
                        {{ csrf_field() }}
                        <input type="submit" value="Send" class="btn btn-success">
                    </div>
                </form>
            </div>

        </div>
    </div>
    


</div>

@endsection

@section('Script')

  <script>
      $(document).on('click','.contactBtn',function(){
          
          var ContactId=$(this).data('contact');
          $('input[name=ContactIdI]').val(ContactId)

          //Update Message Status On Ch 
          $.ajax({
              method:"post",
              url:"{{ route('UpdateCh') }}",
              data:{
                ChIdI:ContactId,
                SourceTypeI:1,
              },sucsess:function(data){
                  console.log(data)
              }
              })


      })
  </script>

@endsection