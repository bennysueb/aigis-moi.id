<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Notice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
        <h1 class="text-2xl font-bold text-yellow-600 mb-4">Notice</h1>
        <p class="text-gray-700">{{ $message }}</p>
        <a href="{{ route('home') }}" class="mt-6 inline-block bg-blue-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-700">
            Back to Home
        </a>
    </div>
</body>

</html>