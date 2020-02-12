@component('mail::message')
Dear {{$mailData['name']}},
<br>
<b>{{$mailData['userName']}}</b> has sent this enquiry to you, <br>

User Message, <br>
{{$mailData['message']}}<br><br>

<b>Name : </b> {{$mailData['userName']}} <br>
<b>Email : </b> {{$mailData['userEmail']}}


Thanks,<br>
{{ config('app.name') }}
@endcomponent