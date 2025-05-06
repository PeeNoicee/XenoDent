<x-app-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 1.25rem; color: #1F2937; line-height: 1.5;">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


            <!-- Container with flex layout -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; padding: 7rem; gap: 2rem;">
                <!-- Content Box -->
                <div style="flex: 1 1 500px; background-color: #1F2937; border-radius: 0.5rem; padding: 1.5rem; color: #F9FAFB;">
                    <h1 style="text-align: center; font-size: 2.25rem; font-weight: bold;">
                        {{ __("Welcome to XenoDent") }}
                    </h1>
                    <p style="text-align: center; font-size: 1.125rem; color: #D1D5DB; margin-top: 0.5rem;">
                        {{ __("Your trusted platform for AI-powered dental X-ray analysis. Upload an X-ray, and let our advanced model assist you in diagnosing dental conditions with ease.") }}
                    </p>
                    <div style="display: flex; justify-content: center; margin-top: 2rem;">
                        <a href="{{ url('xrayLanding/' . Auth::user()->id) }}">
                            <button style="background-color: #3B82F6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; border: none; cursor: pointer;">
                                Analyze Dental X-Ray
                            </button>
                        </a>
                    </div>

                    <!-- Feature Highlights -->
                    <h2 style="text-align: center; font-size: 1.5rem; font-weight: bold; margin-top: 2.5rem;">
                        {{ __("Feature Highlights") }}
                    </h2>
                    <div style="display: flex; justify-content: center;">
                        <ul style="margin-top: 1rem; font-size: 1rem; color: #E5E7EB; list-style: disc; padding-left: 1.5rem; text-align: left;">
                            <li>Instant X-ray analysis using AI</li>
                            <li>Privacy-first: Your data stays secure</li>
                            <li>Dental condition detection in seconds</li>
                            <li>Downloadable reports for your dentist</li>
                        </ul>
                    </div>
                </div>
            </div>




</x-app-layout>
