@extends('layout.master')
@extends('includes.datatables')

@section('Content')
@include('includes.BigBossNavbar')
@include('includes.error')
@include('includes.BigBossSideNav')

<div id="Content">

    <table id='dataTable' class="table table-responsive">
        <thead>
            <th>{{ trans('lang.Date') }}</th>
            <th>{{ trans('lang.TotalOrders') }}</th>
            <th>{{ trans('lang.PaidOrders') }}</th>
            <th>{{ trans('lang.CanceldOrders') }}</th>
            <th>{{ trans('lang.Income') }}</th>
            <th>{{ trans('lang.Options') }}</th>
        </thead>
        <tbody>
            @foreach ($Reports as $Report)
                <tr>
                    <td>{{$Report['ReportDate']}}</td>
                    <td>{{$Report['ReportTotalOrders']}}</td>
                    <td>{{$Report['ReportPaidOrders']}}</td>
                    <td>{{ $Report['ReportCanceldOrders'] }}</td>
                    <th>{{ $Report['ReportIncome'] }}</th>
                    <td><button class="btn btn-success">Export</button></td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
@endsection