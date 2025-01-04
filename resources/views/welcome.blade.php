<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LinkedMeet API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@400;500;700&display=swap"
        rel="stylesheet"
    />
    <style>
        * {
            font-family: "Source Code Pro", monospace;
        }
    </style>
</head>
<body class="flex flex-col h-screen justify-center p-5 lg:p-0 items-center">
<div class="flex flex-col gap-2">
    <div class="flex items-center gap-2">
        <img src="/meet.png" alt="sd" width="100">
        <span class="font-bold text-2xl">LinkedMeet API</span>
        <span
            class="rounded-full font-medium border-green-500 border p-1 text-green-500 text-xs px-3"
        >V1.2.5</span>

    </div>
    <span class="text-md"><span class="font-bold">Discover nearby connections with ease</span>, Your professional GPS-based networking solution.</span>



    <span class="text-md">
    <a href="/api/documentation">
        <span
            class="rounded-full font-medium border-slate-500 border p-1 text-slate-500 text-xs px-3"
        >API Doc</span>
    </a>
    <a href="/admin">
        <span
            class="rounded-full font-medium border-slate-500 border p-1 text-slate-500 text-xs px-3"
        >Panel</span>
    </a>
    <a href="{{ config('app.url_subdomain') }}">
        <span
            class="rounded-full font-medium border-slate-500 border p-1 text-slate-500 text-xs px-3"
        >APP</span>
    </a>
    </span>
</div>
</body>
</html>
