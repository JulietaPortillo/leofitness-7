<?php
    use Carbon\Carbon;
    Carbon::setLocale('es');
?>
@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Pagos
                @permission(['manage-gymie','manage-payments','add-payment'])
                <a href="{{ action('PaymentsController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Agregar nuevo pago</a>
                <small>Detalles de los pagos</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Cantidad recibida del mes</small>
            </h1>
            @endpermission
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border">

                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}

                                        <div class="col-sm-3">

                                            {!! Form::label('member-daterangepicker','Rango de fechas') !!}

                                            <div id="member-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>

                                            {!! Form::text('drp_start',null,['class'=>'hidden', 'id' => 'drp_start']) !!}
                                            {!! Form::text('drp_end',null,['class'=>'hidden', 'id' => 'drp_end']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_field','Organizar por') !!}
                                            {!! Form::select('sort_field',array('created_at' => 'Fecha','payment_amount' => 'Monto','mode' => 'Metodo','member_name' => 'Nombre del miembro','member_code' => 'Codigo del miembro','invoice_number' => 'Numero de recibo'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_direction','Ordenar por') !!}
                                            {!! Form::select('sort_direction',array('desc' => 'Descendente','asc' => 'Ascendente'),old('sort_direction'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_direction']) !!}</span>
                                        </div>

                                        <div class="col-xs-3">
                                            {!! Form::label('search','Buscador') !!}
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Buscar...">
                                        </div>

                                        <div class="col-xs-2">
                                            {!! Form::label('&nbsp;') !!} <br/>
                                            <button type="submit" class="btn btn-primary active no-border">Buscar</button>
                                        </div>

                                        {!! Form::Close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">
                            @if($payment_details->count() == 0)
                                <h4 class="text-center padding-top-15">Lo sentimos! No se encontraron pagos.</h4>
                            @else
                                <table id="payments" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Numero de recibo</th>
                                        <th>Nombre del miembro</th>
                                        <th>Monto</th>
                                        <th>Metodo de pago</th>
                                        <th>Fecha realizada </th>
                                        @permission(['manage-gymie','manage-payments','edit-payment','delete-payment'])
                                        <th class="text-center">Acciones</th>
                                        @endpermission
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        @foreach ($payment_details as $payment_detail)
                                            <?php $cheque_detail = App\ChequeDetail::where('payment_id', $payment_detail->id)->first(); ?>
                                            <td>
                                                <a href="{{ action('InvoicesController@show',['id' => $payment_detail->invoice_id]) }}">{{ $payment_detail->invoice_number }}</a>
                                            </td>
                                            <td>
                                                <a href="{{ action('MembersController@show',['id' => $payment_detail->member_id]) }}">{{ $payment_detail->member_name }}</a>
                                            </td>
                                            <td>
                                                <i class="fa fa-money"></i> {{ ($payment_detail->payment_amount >= 0 ? $payment_detail->payment_amount : str_replace("-","",$payment_detail->payment_amount)." (Paid)") }}
                                            </td>
                                            @if($payment_detail->mode == 1)
                                                <td>{{ Utilities::getPaymentMode($payment_detail->mode)}}</td>
                                            @elseif($payment_detail->mode == 0)
                                                <td>{{ Utilities::getPaymentMode($payment_detail->mode)}}
                                                    ({{ ($cheque_detail ? Utilities::getChequeStatus($cheque_detail->status) : "NA") }})
                                                </td>
                                            @endif
                                            <td>{{ Carbon::parse($payment_detail->created_at)->formatLocalized('%d %B %Y ') }}</td>

                                            <td class="text-center">
                                                @permission(['manage-gymie','manage-payments','edit-payment','delete-payment'])
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Acciones</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    @endpermission
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-payments','edit-payment'])
                                                            <a href="{{ action('PaymentsController@edit',['id' => $payment_detail->id]) }}">
                                                                Editar detalles
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        @if($payment_detail->mode == 0)
                                                            <?php $cheque = App\ChequeDetail::where('payment_id', $payment_detail->id)->whereIn('status', [
                                                                '0',
                                                                '1',
                                                                '3'
                                                            ])->first();
                                                            $result = ($cheque == null) ? false : true;
                                                            //$result = false;
                                                            ?>
                                                            @if($result == true && $payment_detail->mode == 0)
                                                                @if($cheque->status == 0)
                                                                    <li>
                                                                        <a href="{{ action('PaymentsController@depositCheque',['id' => $payment_detail->id]) }}">
                                                                            Marcar como depositado
                                                                        </a>
                                                                    </li>
                                                                @elseif($cheque->status == 1)
                                                                    <li>
                                                                        <a href="{{ action('PaymentsController@clearCheque',['id' => $payment_detail->id]) }}">
                                                                            Marcar como despejado
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="{{ action('PaymentsController@chequeBounce',['id' => $payment_detail->id]) }}">
                                                                            Marcar como no recibido
                                                                        </a>
                                                                    </li>
                                                                @elseif($cheque->status == 3)
                                                                    <li>
                                                                        <a href="{{ action('PaymentsController@chequeReissue',['id' => $payment_detail->id]) }}">
                                                                            Re-intento
                                                                        </a>
                                                                    </li>

                                                                @endif
                                                            @endif
                                                        @endif
                                                        <li>
                                                            @permission(['manage-gymie','manage-payments','delete-payment'])
                                                            <a href="#" class="delete-record"
                                                               data-delete-url="{{ url('payments/'.$payment_detail->id.'/delete') }}"
                                                               data-record-id="{{$payment_detail->id}}">
                                                                Eliminar transaccion
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                    </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            <!-- TO DO -->
                                            Mostrando pagina {{ $payment_details->currentPage() }} de {{ $payment_details->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">

                                            {!! str_replace('/?', '?', $payment_details->appends(Input::Only('search'))->render()) !!}
                                        </div>
                                    </div>
                                </div>

                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.deleterecord();
        });
    </script>
@stop