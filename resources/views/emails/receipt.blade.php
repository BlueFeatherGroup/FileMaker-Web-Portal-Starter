@component('mail::layout')
# Payment Receipt

{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        <a href="https://www.bluefeathergroup.com"><img src="{{asset('img/email/logo-517x100.png')}}" height="50" width=""></a>
    @endcomponent
@endslot

@component('mail::panel')
**Payment Amount: ${{$paymentAmount}}**<br>
Payment Method: {{$paymentMethod}}<br>
Transaction ID: {{$transactionId}}<br>
Date: {{$date}}
@endcomponent

Thank you,<br>
Blue Feather<br>
contact@bluefeathergroup.com<br>
(770) 765-6258


{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} Blue Feather Group, LLC. @lang('All rights reserved.')
    @endcomponent
@endslot
@endcomponent
