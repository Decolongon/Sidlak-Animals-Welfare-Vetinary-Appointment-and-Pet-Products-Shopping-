@component('mail::message')
# Hello, {{ $user->name }}

Your veterinary appointment status has been updated.

@switch($status)
    @case(\App\Enums\AppointmentStatusEnum::Approved)
**Your appointment has been confirmed!** 🎉

Here are the details of your booked services:

@foreach ($booked_services as $service)
- **Service:** {{ $service['appoint_cat_name'] }}
@if($service['appoint_cat_description'] ?? false)
  **Description:** {{ $service['appoint_cat_description'] }}
@endif
@endforeach

**Payment Method:** {{ $payment_method }}

**Your Appointment Date:** {{  \Carbon\Carbon::parse($date_to_visit_clinic)->format('l, F j, Y \a\t g:i A') }}

Please arrive 10 minutes before your scheduled time. We look forward to seeing you and your pet!

@break

    @case(\App\Enums\AppointmentStatusEnum::Pending)
Your appointment is currently **pending review**. We'll notify you once it's processed.

@break

    @case(\App\Enums\AppointmentStatusEnum::Rejected)
Your appointment has been **rejected**. 
Sorry for the inconvenience.

@break

    @default
Your appointment status has been updated. Please check your account for details.
@endswitch

@component('mail::button', ['url' => url('/appointments')])
View My Appointments
@endcomponent

Thanks, <br>
**{{ config('app.name') }} Veterinary Team**
@endcomponent