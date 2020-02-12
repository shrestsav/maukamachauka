@component('mail::message')
Dear {{$mailData['name']}},
<br>
<b>{{$mailData['userName']}}</b>has sent this enquiry to you, <br><br>

User Message, <br>
{{$mailData['message']}}<br><br>

User Details
<b>Name : </b> {{$mailData['userName']}} <br>
<b>Email : </b> {{$mailData['userEmail']}}


Thanks,<br>
{{ config('app.name') }}
@endcomponent