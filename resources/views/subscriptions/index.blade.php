@extends('app')

@section('content')


    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Suscripciones
                @permission(['manage-gymie','manage-subscriptions','add-subscription'])
                <a href="{{ action('SubscriptionsController@create') }}" class="page-head-btn btn-sm btn-primary active" role="button">Agregar nueva subscripciones</a>
                <small>Detalles de todas las suscripciones del gimnacio</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total de Suscripciones</small>
            </h1>
            @endpermission
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel no-border ">

                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        {!! Form::Open(['method' => 'GET']) !!}

                                        <div class="col-sm-3">

                                            {!! Form::label('subscription-daterangepicker','Rango de fechas') !!}

                                            <div id="subscription-daterangepicker"
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
                                            {!! Form::select('sort_field',array('created_at' => 'Fecha','plan_name' => 'Nombre de plan'),old('sort_field'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_field']) !!}
                                        </div>

                                        <div class="col-sm-2">
                                            {!! Form::label('sort_direction','Orden') !!}
                                            {!! Form::select('sort_direction',array('desc' => 'Decendente','asc' => 'Ascendiendo'),old('sort_direction'),['class' => 'form-control selectpicker show-tick show-menu-arrow', 'id' => 'sort_direction']) !!}</span>
                                        </div>

                                        <div class="col-xs-2">
                                            {!! Form::label('plan_name','Nombre del plan') !!}

                                            <?php $plans = App\Plan::all(); ?>

                                            <select id="plan_name" name="plan_name" class="form-control selectpicker show-tick">

                                                <option value="0" {{ (old('plan_name') == "" ? "selected" : "") }}>All</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" {{ (old('plan_name') == $plan->id ? "selected" : "") }}>{{ $plan->plan_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-xs-2">
                                            {!! Form::label('search','Buscador') !!}
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Buscar...">
                                        </div>

                                        <div class="col-xs-1">
                                            {!! Form::label('&nbsp;') !!} <br/>
                                            <button type="submit" class="btn btn-primary active no-border">Buscar</button>
                                        </div>

                                        {!! Form::Close() !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="panel-body bg-white">
                            @if($subscriptions->count() == 0)
                                <h4 class="text-center padding-top-15">Lo sentimos! No se encontro suscripciones</h4>
                            @else
                                <table id="subscriptions" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Codigo del miembro</th>
                                        <th>Nombre del miembros </th>
                                        <th>Nombre del plan</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Caducacion</th>
                                        <th>Status</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>

                                        @foreach ($subscriptions as $subscription)

                                            <td>
                                                <a href="{{ action('MembersController@show',['id' => $subscription->member->id]) }}">{{ $subscription->member->member_code}}</a>
                                            </td>
                                            <td>
                                                <a href="{{ action('MembersController@show',['id' => $subscription->member->id]) }}">{{ $subscription->member->name}}</a>
                                            </td>
                                            <td>{{ $subscription->plan_name}}</td>
                                            <td>{{ $subscription->start_date->format('Y-m-d')}}</td>
                                            <td>{{ $subscription->end_date->format('Y-m-d')}}</td>
                                            <td>
                                                <span class="{{ Utilities::getSubscriptionLabel ($subscription->status) }}">{{ Utilities::getSubscriptionStatus($subscription->status) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Acciones</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-subscriptions','edit-subscription'])
                                                            <a href="{{ action('SubscriptionsController@edit',['id' => $subscription->id]) }}">
                                                                Editar detalles
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        @permission(['manage-gymie','manage-subscriptions','change-subscription'])
                                                        <li>
                                                            <a href="{{ action('SubscriptionsController@change',['id' => $subscription->id]) }}">
                                                                Aumentar/Decrementar
                                                            </a>
                                                        <li>
                                                            @endpermission
                                                            @permission(['manage-gymie','manage-subscriptions','delete-subscription'])
                                                            <a href="#" class="delete-record"
                                                               data-delete-url="{{ url('subscriptions/'.$subscription->id.'/delete') }}"
                                                               data-record-id="{{$subscription->id}}">
                                                                Eliminar Suscripciones
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>

                                            </td>
                                    </tr>

                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            <!-- TO DO -->
                                            Mostrando pagina {{ $subscriptions->currentPage() }} de {{ $subscriptions->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">

                                            {!! str_replace('/?', '?', $subscriptions->appends(Input::all())->render()) !!}
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