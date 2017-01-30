@extends('layouts.app')
@section('title','Facebook Report')
@section('content')
    <div class="wrapper">
        @include('components.navigation')
        @include('components.sidebar')

        <div class="content-wrapper">
            <section class="content">


                @foreach($data['data'] as $d)

                    {{-- page widget start--}}
                    <div class="box box-widget widget-user">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-aqua-active">
                            <h3 class="widget-user-username">{{$d['name']}}</h3>
                            <h5 class="widget-user-desc">{{$d['id']}}</h5>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle" src="{{$d['picture']['data']['url']}}" alt="User Avatar">
                        </div>
                        <div class="box-footer">
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                </div>
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{$d['fan_count']}}</h5>
                                        <span class="description-text">FANS</span>
                                    </div>
                                    <!-- /.description-block -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-4 border-right">

                                </div>

                            </div>
                            <!-- /.row -->
                            {{-- start row for page--}}
                            <div class="row">


                                {{--find country start--}}
                                @foreach($d['insights']['data'][209]['values'] as $valNo => $val)
                                    @foreach($val as $value => $list)

                                        @if(is_array($list))
                                            <div class="row">
                                                <div align="center" class="col-md-12">
                                                    <!-- USERS LIST -->

                                                    @foreach($list as $country => $count)
                                                        <label class="badge">
                                                            <img class="direct-chat-img"
                                                                 src="{{url('/opt/flags/flags/1x1/')}}/{{strtolower($country)}}.svg">
                                                            {{$count}}
                                                        </label>

                                                @endforeach

                                                <!--/.box -->
                                                </div>
                                            </div>
                                        @else
                                            <div style="padding: 10px">
                                                <div class="info-box bg-yellow">
                                                    <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Date</span>
                                                        <span class="info-box-number"><?php $s = $list;
                                                            $date = strtotime($s);
                                                            echo date('d/M/Y', $date);?>
                                                            <br></span>
                                                    </div>
                                                    <!-- /.info-box-content -->
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach
                                    {{-- find country end--}}

                                    {{--find location start--}}
                                    <div class="row">
                                        @foreach($d['insights']['data'] as $data)
                                            @if($data['name']=='page_fans_city')

                                                @foreach($data['values'] as $valueNo => $valueData)
                                                    <div class="col-md-4">

                                                        @foreach($valueData as $v=>$da)
                                                            {{--                                        <pre><code>{{print_r($da)}}</code></pre>--}}
                                                            @if(is_array($da))

                                                                <div class="box-body">
                                                                    <ul class="todo-list">
                                                                        <li>End time {{\App\Http\Controllers\Prappo::date($valueData['end_time'])}}</li>
                                                                        @foreach($da as $city=>$count)
                                                                            <li>
                                                                                <span class="text">{{$city}}</span>
                                                                                <small class="label label-danger"><i class="fa fa-user"></i>{{$count}}</small>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>

                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </div>
                                    {{--find location end--}}

                                @endforeach
                            </div>
                            {{-- end row for page--}}
                        </div>
                    </div>
                    {{-- page widget end--}}
                @endforeach
            </section>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{url('opt/flags/css/flag-icon.css')}}">
    <script src="http://maps.google.com/maps/api/js?sensor=false"
            type="text/javascript"></script>
@endsection
