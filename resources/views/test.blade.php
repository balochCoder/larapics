<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Blade Components</title>
</head>
<body>
<div>
{{--    @php--}}
{{--        $icon = "logo.svg";--}}
{{--    @endphp--}}
{{--    <x-icon :src="$icon"/>--}}

    <x-alert type="success" id="my-alert" class="mt-4">
        <p class="mb-0">Data has been removed. {{$component->link('Undo')}}</p>
    </x-alert>

    <x-form action="/images" method="PUT">
        <label>
            <input type="text" name="name">
        </label>
        <button type="submit">Submit</button>
    </x-form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>
