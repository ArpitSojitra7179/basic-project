<x-mail::message>
# Hello {{ $userName }}

Welcome to **{{ config('app.name') }}**!

<x-mail::panel>
Your account has been successfully created.
</x-mail::panel>

<x-mail::button :url="url('/')" color="success">
Go to Dashboard
</x-mail::button>

<x-mail::table>
| ID            | Name            | credit           |
| ------------- |:---------------:| ----------------:|
| {{ $userId }} | {{ $userName }} | {{ $user }}      |

</x-mail::table>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
