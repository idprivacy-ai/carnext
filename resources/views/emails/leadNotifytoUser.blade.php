@component('mail::message')
# Lead Notification

Dear {{ $user->first_name }},

@if($vehicle)
We have found a vehicle matching your interest:
- Vehicle ID: {{ $vehicle['id'] }}
- VIN: {{ $vehicle['vin'] }}
- Dealer: {{ $dealer['name'] ?? 'N/A' }}
- Dealer: {{ $user['first_name'] ?? 'N/A' }}

@endif

Thank you for your interest.

Regards,  
{{ config('app.name') }}
@endcomponent
