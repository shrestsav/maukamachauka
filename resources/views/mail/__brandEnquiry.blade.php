@component('mail::message')
Dear {{$mailData['name']}},
You've received this enquiry, <br>
{{$mailData['message']}}<br><br>


Thanks,<br>
{{ config('app.name') }}
@endcomponent