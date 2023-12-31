@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Ingrese detalles del pago</div>
                        </div>

                        {!! Form::model($payment_detail, ['method' => 'POST','action' => ['PaymentsController@update',$payment_detail->id],'id' => 'paymentsform']) !!}

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php  $invoiceList = App\Invoice::lists('invoice_number', 'id'); ?>
                                        {!! Form::label('invoice_id','Numero de recibo') !!}
                                        {!! Form::select('invoice_id',$invoiceList,(isset($invoice) ? $invoice->id : null),['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'invoice_id', 'data-live-search'=> 'true']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('payment_amount','Monto') !!}
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-money"></i></div>
                                            {!! Form::text('payment_amount',(isset($invoice) ? $invoice->pending_amount : null),['class'=>'form-control', 'id' => 'payment_amount']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('mode','Metodo de pago') !!}
                                        {!! Form::select('mode', array('1' => 'Efectivo', '0' => 'Transferencia'), (isset($payment_detail) ? $payment_detail->mode : null), ['class'=>'form-control selectpicker show-tick show-menu-arrow', 'id' => 'paymentMode']) !!}
                                    </div>
                                </div>
                            </div>
                                
                            
                            <div id="chequeDetails" style="display: {{ isset($payment_detail) && $payment_detail->mode == 0 ? 'block' : 'none' }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('number','Numero de transferencia') !!}
                                                {!! Form::text('number',(isset($cheque_detail) ? $cheque_detail->number : null),['class'=>'form-control', 'id' => 'number']) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                {!! Form::label('date','Fecha de transferencia') !!}
                                                {!! Form::text('date',(isset($cheque_detail) ? $cheque_detail->date : null),['class'=>'form-control datepicker-default', 'id' => 'date']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Hide the chequeDetails div initially
        $('#chequeDetails').hide();

        // Add change event listener to the paymentMode select dropdown
        $('#paymentMode').change(function() {
            // If Transferencia is selected, show the chequeDetails div, otherwise hide it
            if ($(this).val() === '0') {
                $('#chequeDetails').show();
            } else {
                $('#chequeDetails').hide();
            }
        });
    });
</script>
                            

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::submit('Actualizar', ['class' => 'btn btn-primary pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::Close() !!}

                    </div>
                </div>
            </div>
        </div>


        @stop
        @section('footer_scripts')
            <script src="{{ URL::asset('assets/js/payment.js') }}" type="text/javascript"></script>
@stop