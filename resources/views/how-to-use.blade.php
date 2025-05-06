<x-app-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 1.25rem; color: #1F2937; line-height: 1.5;">
            {{ __('How to Use') }}
        </h2>
    </x-slot>

    <!-- Container with flex layout -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; padding: 7rem; gap: 2rem;">
        <!-- Content Box -->
        <div style="flex: 1 1 500px; background-color: #1F2937; border-radius: 0.5rem; padding: 1.5rem; color: #F9FAFB;">
            <h1 style="text-align: center; font-size: 2.25rem; font-weight: bold;">
                {{ __("Using XenoDent is Simple") }}
            </h1>
            <p style="text-align: center; font-size: 1.125rem; color: #D1D5DB; margin-top: 0.5rem;">
                {{ __("Follow these quick steps to analyze dental X-rays using our AI-powered platform.") }}
            </p>

            <div style="margin-top: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: bold; text-align: center;">
                    {{ __("Step-by-Step Guide") }}
                </h2>

                <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 2rem; align-items: center;">
                    <!-- Step 1 -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ asset('images/img-placeholder.png') }}" alt="Login Step" style="width: 300px; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                        <p>Log in to your XenoDent account</p>
                    </div>

                    <!-- Step 2 -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ asset('images/img-placeholder.png') }}" alt="Dashboard Step" style="width: 300px; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                        <p>Click the “Analyze Dental X-Ray” button on your dashboard</p>
                    </div>

                    <!-- Step 3 -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ asset('images/img-placeholder.png') }}" alt="Upload X-Ray Step" style="width: 300px; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                        <p>Upload a valid dental X-ray image (JPG, PNG, etc.)</p>
                    </div>

                    <!-- Step 4 -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ asset('images/img-placeholder.png') }}" alt="Processing Step" style="width: 300px; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                        <p>Wait for the AI to process and display results</p>
                    </div>

                    <!-- Step 5 -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ asset('images/img-placeholder.png') }}" alt="Download Report Step" style="width: 300px; border-radius: 0.5rem; margin-bottom: 0.5rem;">
                        <p>Download the diagnostic report if needed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
