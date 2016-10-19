@extends('app')
@section('title', 'Great Going')

@section('content')
    @include('includes.side_bar_menu')
    <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
        <div class="modal-dialog welcome-screen">
            <div class="modal-content text-center">
                <div class="modal-body">
                    <img src={{ url('/images/logo-large.svg') }} alt="Aidstream" width="130" height="124">
                    <h1>Great going {{ucfirst($firstname)}}</h1>
                    @if(Auth::user()->isAdmin())
                        <p>
                            <em>Set up your account to Start Publishing to the IATI Registry.</em>
                            @if(session('completed') == 'yes')
                                'Checked'
                            @else
                                'Unchecked'
                            @endif
                        </p>
                    @endif
                    <p>You can always go back to Settings page to change your organisation's settings</p>
                    <div><a href="{{ url('dashboardTour') }}" class="btn">
                            Get to know your Dashboard
                        </a></div>
                    <span class="explore-later"><a href="{{ url('activity')  }}">Explore Later</a></span>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot')
    <script>
        $(document).ready(function () {
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    </script>
@endsection
