<?php

namespace App\Services;

use App\Models\BookingEditRequest;

/**
 * Class BookingEditRequestService.
 */
class BookingEditRequestService
{
    public function show($id)
    {
        return BookingEditRequest::with('booking.customer.user')->find($id);
    }

    public function update($data, $id)
    {
        $booking_edit = BookingEditRequest::find($id);
        $booking_edit->update($data);
        return $booking_edit;
    }
}
