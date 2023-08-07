<!DOCTYPE html>
<html>

<head>
    <title>Datatables with Livewire in Laravel 8</title>
    @livewireStyles
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.9.2/tailwind.min.css" integrity="sha512-l7qZAq1JcXdHei6h2z8h8sMe3NbMrmowhOl+QkP3UhifPpCW2MC4M0i26Y8wYpbz1xD9t61MLT9L1N773dzlOA==" crossorigin="anonymous" />
</head>

<body>
    <style>
        .rounded-lg,
        .rounded-b-none {
            width: 100%;
        }
    </style>
    <div class="container m-10">
        <br />
        <div class="flex items-center markdown">
            <h1 style="font-size: 2em;"><b>Daily User Registration</b></h1>
        </div>
        <br />
        <div class="flex">
            <livewire:livewire-datatables searchable="username, email," exportable per-page="20" />
        </div>

    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>

</body>
@livewireScripts

</html>