@extends('layouts.xrayapp')

@section('content')

<!-- Modal -->
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
        e.preventDefault();

        const form = e.target;
        const url = form.action;
        const formData = new FormData(form);

        fetch(url, {
            method: 'POST', // Send as POST since PATCH isn't supported by all browsers
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            // Show success modal
            const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong while processing your purchase.');
        });
    });
</script>
@endsection
