@extends('layouts.xrayapp')

@section('content')

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">THANK YOU</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        PURCHASE SUCCESS!
      </div>
      <div class="modal-footer">
        <a href="{{url ('homepage') }}" class="btn btn-primary">Continue</a>
      </div>
    </div>
  </div>
</div>




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
                <li class="nav-item">
                    <a class="nav-link" href="{{url ('homepage') }}">Go back to homepage</a>
                </li>
            </ul>

            </div>
        </div>
        </nav>
    </div>

    <div class = "d-flex justify-content-center" style = "margin-top: 80px">

        <div style = "background-color: #AEACAC; height: 600px; width: 500px; padding: 25px 25px 25px 25px">
            
            <p class = "row" style = "margin-left: 35%">
                PREMIUM PACKAGE
            </p>

            <div class = "row" style = "margin-left: 15%; margin-top: 10%">
                <ul class="ms-3">
                    <li class="mb-1">1. EXTRA RAM FOR AI ANALYSIS</li>
                    <li class="mb-1">2. EXTRA STORAGE FOR AI ANALYSIS</li>
                    <li class="mb-1">3. BETTER AI MODEL</li>
                    <li class="mb-1">4. DIRECT SUPPORT</li>
                    <li class="mb-1">5. FEATURE REQUESTS</li>
                    <li class="mb-1">6. TEST</li>
                    <li class="mb-1">7. TEST</li>
                </ul>
            </div>

            <form id="updateUserForm" action="{{url('updateUSer')}}" method = "PATCH" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div>
                    <button type="submit" class="btn btn-outline-success" style = "margin-left: 75%; margin-top: 130px">PURCHASE</button>
                </div>

            </form>
            
        </div>


    
    </div>

    
</div>


@endsection

@section('script')

<script>
    window.addEventListener('load', () => {
        document.getElementById('fade').classList.add('loaded');
    });

    document.getElementById('updateUserForm').addEventListener('submit', function(e) {
        e.preventDefault(); 

        let form = e.target;
        let url = form.action;
        let formData = new FormData(form);

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            //return response.json(); 
            let modal = new bootstrap.Modal(document.getElementById('exampleModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong while processing your purchase.');
        });
    });



</script>

@endsection