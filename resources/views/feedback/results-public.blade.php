<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Results for {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4 sm:p-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Feedback Results</h1>
            <h2 class="text-xl font-semibold text-gray-600 mb-4">{{ $event->name }}</h2>
            <p class="text-gray-500 border-b pb-4 mb-6">Total Submissions: <span class="font-bold">{{ $totalSubmissions }}</span></p>

            @if($totalSubmissions > 0)
            <div class="space-y-8">
                @foreach($aggregatedData as $fieldName => $data)
                @php
                // Membersihkan field name agar aman digunakan sebagai ID
                $sanitizedFieldName = Str::slug($fieldName);
                @endphp
                <div class="border rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $data['label'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">({{ $data['total_responses'] }} responses)</p>

                    @if(in_array($data['type'], ['radio', 'select', 'checkbox']))
                    <div class="w-full h-64"><canvas id="chart-{{ $sanitizedFieldName }}"></canvas></div>
                    <script>
                        new Chart(document.getElementById('chart-{{ $sanitizedFieldName }}'), {
                            type: 'pie',
                            data: {
                                labels: @json($data['data']['labels']),
                                datasets: [{
                                    label: 'Responses',
                                    data: @json($data['data']['values']),
                                    backgroundColor: ['#4ade80', '#fbbf24', '#f87171', '#60a5fa', '#c084fc', '#fb923c'],
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    </script>
                    @elseif($data['type'] === 'rating')
                    <div class="text-center">
                        <p class="text-5xl font-bold text-blue-600">{{ number_format($data['data']['average'], 1) }} / 5</p>
                        <p class="text-gray-600">Average Rating</p>
                    </div>
                    @else
                    <ul class="list-disc list-inside bg-gray-50 p-3 rounded-md max-h-48 overflow-y-auto">
                        @foreach($data['data'] as $response)
                        <li class="text-gray-700 text-sm">{{ $response }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 py-8">No feedback has been submitted for this event yet.</p>
            @endif
        </div>
    </div>
</body>

</html>