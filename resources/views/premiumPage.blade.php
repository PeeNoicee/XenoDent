@extends('layouts.xrayapp')

@section('content')
<style>
    .modal-dialog {
        max-width: 600px;  /* Adjust the width of the modal */
        margin: 30px auto; /* Center the modal */
    }

    .modal-body {
        font-size: 16px;
        color: #333;
        text-align: center;
    }

    .modal-footer {
        justify-content: center;
    }
</style>
<!-- Modal Structure -->
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
        <a href="{{ url('homepage') }}" class="btn btn-primary">Continue</a>
      </div>
    </div>
  </div>
</div>


<div id="fade">
    <!-- Navbar -->
    <nav nav class="navbar navbar-expand-lg" style="background-color: #1F2937;">
        <div class="container-fluid">
            <a href="{{ route('premium') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-100" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('homepage') }}">Go back to homepage</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Premium Card -->
    <div class="d-flex justify-content-center mt-5">
        <div class="card shadow-lg" style="background-color: #374151; width: 500px; border-radius: 1rem;">
            <div class="card-body">
                <h4 class="text-center mb-4 text-light">PREMIUM PACKAGE</h4>

                <ol class="ms-4 text-light">
                    <li>EXTRA RAM FOR AI ANALYSIS</li>
                    <li>EXTRA STORAGE FOR AI ANALYSIS</li>
                    <li>BETTER AI MODEL</li>
                    <li>DIRECT SUPPORT</li>
                    <li>FEATURE REQUESTS</li>
                    <li>TEST</li>
                    <li>TEST</li>
                </ol>

                <!-- Purchase Form -->
                <form id="updateUserForm" action="{{ url('updateUser') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-outline-success" style="border-radius: 0.5rem;">PURCHASE</button>
                    </div>
                </form>
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

    document.getElementById('updateUserForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        let form = e.target;
        let url = form.action;
        let formData = new FormData(form);

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(response => response.json()) // Parse the JSON response from the server
        .then(data => {
            if (data.message) {
                // Show the success modal
                let modal = new bootstrap.Modal(document.getElementById('exampleModal'));
                modal.show();

                // Redirect to the homepage after the modal is closed
                document.querySelector('.btn-primary').addEventListener('click', function() {
                    window.location.href = '{{ url('homepage') }}';  // Redirect to homepage or any route you want
                });
            } else if (data.error) {
                // Handle any errors (e.g., show an error message)
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong while processing your purchase.');
        });
    });


</script>
@endsection
