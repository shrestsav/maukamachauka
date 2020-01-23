@component('mail::message')
Dear {{$mailData['name']}},

{{$mailData['message']}}<br><br>

{{$mailData['verifyLink']}}<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent