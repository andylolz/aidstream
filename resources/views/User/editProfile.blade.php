@extends('app')

@section('title', 'Edit Profile - ' . $user->first_name)

@section('content')
    @inject('getCodeList', 'App\Core\Form\BaseForm')
    <div class="container main-container">
        <div class="row">
            @include('includes.side_bar_menu')
            <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    @include('includes.response')
                @endif
                @include('includes.breadcrumb')
                <div class="panel-content-heading panel-title-heading">Edit Profile</div>
                <form class="form-horizontal form-edit-profile" role="form" method="POST" action="{{ route('user.update-profile', $user->id)}}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    {{--*/
                        if(old()) {
                            $user->first_name               = old('first_name');
                            $user->last_name                = old('last_name');
                            $user->email                    = old('email');
                            $user->time_zone_id             = old('time_zone');
                            $organization->name             = old('organization_name');
                            $organization->address          = old('organization_address');
                            $organization->country          = old('country');
                            $organization->organization_url = old('organization_url');
                            $organization->telephone        = old('organization_telephone');
                            $organization->twitter          = old('organization_twitter');
                            $organization->disqus_comments  = old('disqus_comments');
                        }
                    /*--}}
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{$user->first_name}}">
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{$user->last_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Email</label>
                                <input type="text" class="form-control" name="email" value="{{$user->email}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Time Zone</label>
                                {{ Form::select('time_zone', ['' => 'Select Time Zone'] + $timeZone, $user->time_zone_id . ' : '. $user->time_zone) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-12 top-border">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Name</label>
                                <input type="text" class="form-control" name="organization_name" value="{{$organization->name}}">
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Address</label>
                                <input type="text" class="form-control" name="organization_address" value="{{$organization->address}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Country</label>
                                <?php $countries = $getCodeList->getCodeList('Country', 'Organization'); ?>
                                <select name="country">
                                    <option value="">Select any option:</option>
                                    @foreach($countries as $countryIndex=>$country)
                                        <option value={{$countryIndex}} {{$organization->country != $countryIndex ?: 'selected="selected"'}}> {{$country}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Url</label>
                                <input type="text" class="form-control" name="organization_url" value="{{ $organization->organization_url }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Telephone</label>
                                <input type="text" class="form-control" name="organization_telephone" value="{{ $organization->telephone }}">
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Organization Twitter</label>
                                <input type="text" class="form-control" name="organization_twitter" value="{{$organization->twitter }}">
                                <div class="description"><span>Please insert a valid twitter username. Example: '@oxfam' or 'oxfam'</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Upload Organization Logo</label>
                                <input type="file" class="form-control" name="organization_logo">
                                <div class="description"><span>Please use jpg/jpeg/png/gif format and 150x150 dimensions image.</span></div>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-6">
                            <div class="col-xs-12 col-md-12">
                                <label class="control-label">Disqus Comments</label>
                                <div>
                                  <input type="checkbox" name="disqus_comments" {{!$organization->disqus_comments ?: 'checked="checked"'}} value="1">
                                  <span>Enable/disable comments on your organization page.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-form btn-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection