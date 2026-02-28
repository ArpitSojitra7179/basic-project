@component('mail::message')

# Hello {{ $student->name }}

@component('mail::panel')
You are successfully enrolled in the following courses.
@endcomponent

@component('mail::table')
| No  | Title |
|-----|-------|
@foreach ($courses as $index => $course)
| {{ $index + 1 }} | {{ $course->title }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url('/'), 'color' => 'success'])
Go To Home Page
@endcomponent

@component('mail::subcopy')
This email was sent automatically. Please do not reply.
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent
