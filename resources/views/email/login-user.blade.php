<x-mail::message>
# Hello

Welcome to Laravel!

<x-mail::panel>
Your account has been logged in successfully.
</x-mail::panel>

<x-mail::button :url="url('/')" color="success">
Go to Dashboard
</x-mail::button>

Thanks for visiting our site.
</x-mail::message>