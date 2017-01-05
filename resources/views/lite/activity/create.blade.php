@extends('lite.base.sidebar')

@section('title', 'Activities')

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>Activities</div>
            </div>
            <div class="panel-body">
                <div class="create-form create-project-form edit-form">
                    {!! form($form) !!}
                </div>
                <div class="funding_organisations-container hidden"
                     data-prototype="{{ form_row($form->funding_organisations->prototype()) }}">
                </div>
                <div class="implementing_organisations-container hidden"
                     data-prototype="{{ form_row($form->implementing_organisations->prototype()) }}">
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.add-to-collection').on('click', function (e) {
                e.preventDefault();
                var source = $(this).attr('data-collection');
                var collection = $('.' + source + '-container');
                var parentContainer = $('.' + source);
                var count = $('.' + source + '> div.form-group').length;
                var proto = collection.data('prototype').replace(/__NAME__/g, count);
                $(parentContainer).append(proto);
            });
        });
    </script>
@stop
