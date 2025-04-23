@extends('layouts.xrayapp')

@section('content')

<div id = "fade">

    <div class = "col" style = "margin-top: 10px">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{url ('dashboard') }}">DENTAL LOGO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Analytics</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">X-ray AI</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @if($prem === 0)
                        <li><a class="dropdown-item disabled" href="">For Premium user</a></li>
                    @else
                        <li><a class="dropdown-item" href="">Feature Request</a></li>
                    @endif
                </ul>
                </li>
            </ul>

            @if($prem === 0)
                <div class="d-flex">
                    <a href = "{{url ('premiumPage') }}">
                    <button class="btn btn-outline-success">Go PREMIUM</button>
                    </a>
                </div>
            @else

                <div class="d-flex">
                    <a >
                    <button class="btn btn-outline-success">Need Support?</button>
                    </a>
                </div>

            @endif
            </div>
        </div>
        </nav>
    </div>


    <div class = 'container' style = "margin: 20px 0px 0px 5px">

        <div class = 'row '>

            <div class = 'col' >
                
                <div>
                    <h1 style = "margin: 25% 0px 0px 15%; font-size: 2rem;">{{$welcome}}</h1>
                    <h1 style = "margin: 10px 0px 0px 15%; font-size: 22px">How can we help you?</h1>

                    <br>

                    <p style = "margin: 10px 0px 0px 15%; font-size: 16px" >Our AI-powered dental assistant helps streamline diagnostics, 
                        <br> analyze X-rays, and improve treatment planning. 
                        <br> Get insights, track patient progress, and make data-driven decisions 
                        <br> with ease.
                    </p>

                
                        <h1 style = "margin: 70px 0px 0px 15%; font-size: 16px">Click
                            <a href = "{{url ('xrayPage') }}">
                                <button class="btn btn-outline-success">BEGIN</button>
                            </a>

                            to get started
                        </h1>

                    
                </div>



            </div>

            <div class = 'col'>
            
                <div style = 'background-color: #AEACAC; height: 500px; width: 500px; margin: 15% 0px 0px 10%;'>
                    <h1>IMG HERE</h1>
                </div>
            </div>

        </div>
        
    </div>

</div>

@endsection

@section('script')

<script>
    window.addEventListener('load', () => {
        document.getElementById('fade').classList.add('loaded');
    });
</script>

@endsection