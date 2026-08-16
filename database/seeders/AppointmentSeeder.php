<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Legacy weekday seeder — incompatible with current booking appointments schema.
 * Kept for reference only. Use AppointmentBookingSeeder instead.
 */
class AppointmentSeeder extends Seeder
{
    public function run()
    {
        // Intentionally empty — appointments table now stores patient bookings.
    }
}
