<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback for {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS tambahan untuk rating bintang */
        .rating>input:checked~label,
        .rating:not(:checked)>label:hover,
        .rating:not(:checked)>label:hover~label {
            color: #FBBF24;
            /* amber-400 */
        }

        .rating>input:checked+label:hover,
        .rating>input:checked~label:hover,
        .rating>label:hover~input:checked~label,
        .rating>input:checked~label:hover~label {
            color: #F59E0B;
            /* amber-500 */
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4 sm:p-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Feedback Form</h1>
            <h2 class="text-lg font-semibold text-gray-600 mb-6">{{ $event->name }}</h2>

            <form action="{{ route('feedback.store', ['event' => $event->slug, 'registration' => $registration->uuid]) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    {{-- Loop untuk menampilkan setiap field dari form dinamis --}}
                    @foreach($formFields as $field)
                    <div>
                        <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                            {{ $field['label'] }}
                            @if(!empty($field['required'])) <span class="text-red-500">*</span> @endif
                        </label>
                        <div class="mt-1">
                            @if($field['type'] === 'textarea')
                            <textarea name="{{ $field['name'] }}" id="{{ $field['name'] }}" rows="4" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>

                            @elseif($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                {{-- TAMBAHKAN @if DI SINI --}}
                                @if(!empty($field['options']) && is_array($field['options']))
                                @foreach($field['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                                @endif
                            </select>

                            @elseif($field['type'] === 'rating')
                            <div class="rating flex flex-row-reverse justify-end items-center">
                                @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" id="{{ $field['name'] }}-{{ $i }}" name="{{ $field['name'] }}" value="{{ $i }}" class="hidden peer" {{ old($field['name']) == $i ? 'checked' : '' }} @if(!empty($field['required']) && $loop->last) required @endif />
                                <label for="{{ $field['name'] }}-{{ $i }}" class="cursor-pointer text-2xl text-gray-300 peer-hover:text-amber-400 hover:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.007z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                                @endfor
                            </div>

                            @else {{-- Default to text input --}}
                            <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            @endif
                        </div>
                        @error($field['name']) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 border-t pt-5">
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>