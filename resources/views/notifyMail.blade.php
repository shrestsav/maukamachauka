@if($mailData['emailType']==='new_registration')

	@include('mail.__newRegistration')

@elseif($mailData['emailType']==='brand_enquiry')

	@include('mail.__brandEnquiry')

@endif