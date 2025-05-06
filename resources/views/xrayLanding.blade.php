@extends('layouts.xrayapp')

@section('content')
<style>
@keyframes loading-stripes {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>

<div class="container d-flex justify-content-center" style="padding: 7rem 1rem;">
    <div style="
        background-color: #111827; /* darker background than #1F2937 */
        color: #F9FAFB;
        border-radius: 0.5rem;
        padding: 3rem;
        width: 100%;
        max-width: 700px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.4); /* optional shadow for depth */
    ">

        <!-- Title -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: bold;">Welcome to XenoDent</h1>
        </div>

        <!-- Checking message -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem;">Checking user key ...</h2>
        </div>

        <!-- Progress bar -->
        <div style="
            background-color: #1F2937;
            height: 1.25rem;
            border-radius: 0.375rem;
            overflow: hidden;
            border: 1px solid #4B5563;
        ">
            <div id="progress-bar"
                style="
                    width: 0%;
                    height: 100%;
                    background: linear-gradient(90deg, #3B82F6 25%, #60A5FA 50%, #3B82F6 75%);
                    background-size: 200% 100%;
                    animation: loading-stripes 1s linear infinite;
                    transition: width 0.2s ease-in-out;
                ">
            </div>
        </div>

    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let progress = 0;
        const progressBar = document.getElementById('progress-bar');
        const interval = setInterval(() => {
            progress += 1;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);

            if (progress >= 100) {
                clearInterval(interval);
                window.location.href = "{{ route('homepage') }}";
            }
        }, 40);
    });
</script>
@endsection
