<ol class="flex items-center w-full">
    <!-- Enrollment Step -->
    <li class="flex w-full items-center {{ $enrollmentDate ? 'text-blue-600 dark:text-blue-500 after:border-blue-100 !important' : 'text-gray-500 after:border-gray-100' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block">
        <span class="flex items-center justify-center w-10 h-10 {{ $enrollmentDate ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
            @if($enrollmentDate)
                <!-- Completed Icon -->
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                </svg>
            @else
                <!-- Pending Icon -->
                1
            @endif
        </span>
    </li>

    {{-- <!-- Counseling Step -->
    <li class="flex w-full items-center {{ $counselingDate ? 'text-blue-600 dark:text-blue-500 after:border-blue-100' : 'text-gray-500 after:border-gray-100' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block">
        <span class="flex items-center justify-center w-10 h-10 {{ $counselingDate ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
            @if($counselingDate)
                <!-- Completed Icon -->
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                </svg>
            @else
                <!-- Pending Icon -->
                2
            @endif
        </span>
    </li> --}}

    <!-- Application Step -->
    <li class="flex w-full items-center {{ $applicationDate ? 'text-blue-600 dark:text-blue-500 after:border-blue-100' : 'text-gray-500 after:border-gray-100' }} after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block">
        <span class="flex items-center justify-center w-10 h-10 {{ $applicationDate ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
            @if($applicationDate)
                <!-- Completed Icon -->
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                </svg>
            @else
                <!-- Pending Icon -->
                3
            @endif
        </span>
    </li>

    <!-- Visa Step -->
    <li class="flex items-center w-full {{ $visaDate ? 'text-blue-600 dark:text-blue-500' : 'text-gray-500' }}">
        <span class="flex items-center justify-center w-10 h-10 {{ $visaDate ? 'bg-blue-100 dark:bg-blue-800' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
            @if($visaDate)
                <!-- Completed Icon -->
                <svg class="w-3.5 h-3.5 text-blue-600 lg:w-4 lg:h-4 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
                </svg>
            @else
                <!-- Pending Icon -->
                4
            @endif
        </span>
    </li>
</ol>
