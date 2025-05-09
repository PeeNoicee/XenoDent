@extends('layouts.xrayapp')

@section('content')

<div id="fade" style="background-color: #111827; min-height: 100vh; color: #F9FAFB;">


    <!-- Content Section -->
    <div class="container" style="margin: 20px 0px 0px 5px">
        <div class="row">
            <div class="col">
                <h1 style="margin: 25% 0px 0px 15%; font-size: 2rem;">{{ $welcome }}</h1>
                <h1 style="margin: 10px 0px 0px 15%; font-size: 22px">How can we help you?</h1>

                <p style="margin: 10px 0px 0px 15%; font-size: 16px;">
                    Our AI-powered dental assistant helps streamline diagnostics,
                    <br> analyze X-rays, and improve treatment planning.
                    <br> Get insights, track patient progress, and make data-driven decisions
                    <br> with ease.
                </p>

                <h1 style="margin: 70px 0px 0px 15%; font-size: 16px">Click
                    <a href="{{ url('xrayPage') }}">
                        <button class="btn btn-outline-success">BEGIN</button>
                    </a>
                    to get started
                </h1>
            </div>

            <div class="col">
                <div class="d-flex justify-content-center align-items-center" style="height: 600px; width: 600px; margin: 10% 0 0 10%; border-radius: 0.5rem; overflow: hidden;">
                    <img src="{{ asset('images/dental.png') }}" alt="X-ray Image" style="width: 120%; height: auto; object-fit: cover; border-radius: 0.5rem; transform: scaleX(-1);">
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
