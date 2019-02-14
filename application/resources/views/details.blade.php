<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Popular PHP Projects</title>
        <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css"/>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <style>          
        </style>
	
	
    </head>
    <body>
        <div class="position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="container">
              <img src="/images/vanderbilt.jpg" alt="Vanderbilt University" class="img-fluid" />

                <div>
		  <!-- Project Details start -->	
		<table class="table table-striped table-bordered">
		  <tbody>	
		@foreach($project as $feildName=>$fieldVaule)           
		    <tr>
		      <th scope="row">{{ $feildName }}</th>
		      <td>{{ $fieldVaule }}</td>
		    </tr>
		@endforeach  
		  </tbody>
		</table>
		  <!-- Project Details end -->
                </div>

            </div>
        </div>
	<script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
