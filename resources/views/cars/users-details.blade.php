@component('mail::message')
# Hello, {{ $user->name }}

Here are the car details associated with your account:

@component('mail::table')
| No | Brand Name | Car Name | price |
|----|------------|----------|-------|
@foreach ($cars as $index => $car)
| {{ $index + 1 }} | {{ $car->brand_name }} | {{ $car->car_name }} | {{ $car->price }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url('/')])
Go to Home page
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
