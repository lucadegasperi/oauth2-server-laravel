@extends('oauth2server::layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Authorize App</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/oauth/authorize?' . $queryString) }}">
                            {{ csrf_field() }}

                            {{ $authRequest->getClient()->name }}

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" name="authorize" value="0" class="btn btn-danger">
                                        <i class="fa fa-btn fa-sign-in"></i> Deny
                                    </button>

                                    <button type="submit" name="authorize" value="1" class="btn btn-success">
                                        <i class="fa fa-btn fa-sign-in"></i> Authorize
                                    </button>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
