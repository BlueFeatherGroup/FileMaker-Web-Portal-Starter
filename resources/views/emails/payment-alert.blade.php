@component('mail::layout')

@if($error)
# Payment Error
{!! $error !!}
@else
# Payment Received
A client has submitted a new payment through the payment portal
@endif

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        <a href="https://www.bluefeathergroup.com"><img src="{{asset('img/email/logo-517x100.png')}}" height="50" width=""></a>
    @endcomponent
@endslot

@component('mail::panel')
**Payment Amount: ${{$paymentAmount}}**<br>
Transaction ID: {{$transactionId}}<br>
Client: {{$clientName}}<br>
Email: {{$email}}<br>
Date: {{$date}}
@endcomponent


{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} Blue Feather Group, LLC. @lang('All rights reserved.')
    @endcomponent
@endslot
@endcomponent
