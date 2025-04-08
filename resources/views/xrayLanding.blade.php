@extends('layouts.xrayapp')

@section('content')


<div class =  "container d-flex justify-content-center">

    <div class = "container" style = "margin-top: 7%; background-color: #9A9A9A ;height: 500px; width: 750px;">

        
        <div class = "col">

            <div class = "row" style = "margin: 10% 1% 1% 35%">
                    <h1>DENTAL LOGO AND TITLE <br>

                    
                
                    </h1>
            </div>

            <div class = "row" style = "margin: 10% 1% 1% 38%">
                    <h1>Checking user key ...</h1>
            </div>



        </div>

        
        <div class="progress" style = "margin: 10% 1% 1% 12%; width: 75%">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" ></div>
        </div>
       
    </div>



</div>


@endsection

@section('script')

<script>

        let progress = 0;
        const progressBar = document.getElementById('progress-bar');
        const interval = setInterval(() => {
            progress += 1;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);

            if (progress >= 100) {
                clearInterval(interval);
                // Redirect to homepage after loading
                window.location.href = "{{ route('homepage') }}";
            }
        }, 40); // adjust speed here (30ms * 100 = 3 seconds)


</script>


@endsection






