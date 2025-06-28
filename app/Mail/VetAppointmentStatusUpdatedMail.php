<?php

namespace App\Mail;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment\Appointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VetAppointmentStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public User $user;
    public AppointmentStatusEnum $status;
    public array $booked_services;
    public $payment_method;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $status, Appointment $appointment)
    {
        $this->user = $user;
        //check status if already enum kng enum use as it is else convert string to enum 
        $this->status = $status instanceof AppointmentStatusEnum 
            ? $status 
            : AppointmentStatusEnum::tryFrom($status) ?? AppointmentStatusEnum::Pending; //default ang pending

        //categories many is to may relationship ara sa model
       $this->booked_services = $appointment->categories()->get()->toArray();
       $this->payment_method = $appointment->payment_method;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vetinary Appointment Details',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment.vet_appointment_status',
            with : [
                'user' => $this->user,
                'status' => $this->status,
                'booked_services' => $this->booked_services,
                'payment_method' => $this->payment_method
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
