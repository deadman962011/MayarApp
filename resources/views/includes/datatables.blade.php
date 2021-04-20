
@section('Style')
@section('Style')
<link rel="stylesheet" href="http://127.0.0.1/cdn/datatables/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="http://127.0.0.1/cdn/datatables-responsive/dataTables.responsive.css">

@endsection
@endsection




@section('Script')
    <script src="http://127.0.0.1/cdn/datatables/js/jquery.dataTables.js"></script>
    <script src="http://127.0.0.1/cdn/datatables/js/dataTables.bootstrap.min.js"></script>
    <script src="http://127.0.0.1/cdn/datatables-responsive/dataTables.responsive.js"></script>
    @if (app()->getLocale() =="ar")

    <script>
    $(document).ready(function() {
    $('#dataTable').dataTable({
        "language":{
                "url":"http://127.0.0.1/cdn/store-manage/Arabic.json"
        },
        resposive:true,
    });
    } );
    </script>
    @else
        
    <script>
        $(document).ready(function() {
        $('#dataTable').dataTable({
            responsive:true,
         
        });
        });
        </script>
    @endif
@endsection


