<x-app-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 1.25rem; color: #1F2937; line-height: 1.5;">
            {{ __('About Us') }}
        </h2>
    </x-slot>

    <!-- Container with flex layout -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; padding: 7rem; gap: 2rem;">
        <!-- Content Box -->
        <div style="flex: 1 1 500px; background-color: #1F2937; border-radius: 0.5rem; padding: 1.5rem; color: #F9FAFB;">
            <h1 style="text-align: center; font-size: 2.25rem; font-weight: bold;">
                {{ __("About XenoDent") }}
            </h1>
            <p style="text-align: center; font-size: 1.125rem; color: #D1D5DB; margin-top: 0.5rem;">
                {{ __("XenoDent is a cutting-edge dental diagnostic tool that leverages AI to analyze dental X-rays in seconds. Our mission is to make dental diagnostics more accessible, accurate, and affordable for both patients and professionals.") }}
            </p>

            <div style="margin-top: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: bold; text-align: center;">
                    {{ __("Our Goals") }}
                </h2>
                <div style="display: flex; justify-content: center;">
                    <ul style="margin-top: 1rem; font-size: 1rem; color: #E5E7EB; list-style: disc; padding-left: 1.5rem; text-align: left;">
                        <li>Empower dentists with fast and reliable X-ray analysis</li>
                        <li>Improve patient access to early diagnosis</li>
                        <li>Ensure privacy and data security in medical imaging</li>
                        <li>Bridge the gap between AI technology and healthcare</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
