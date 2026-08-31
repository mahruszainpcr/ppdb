<?php

namespace Tests\Feature\Auth;

use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationClosureTest extends TestCase
{
    use RefreshDatabase;

    public function test_closed_registration_shows_notice_and_hides_group_link(): void
    {
        $period = new Period([
            'name' => 'PPDB 2027/2028',
            'wave' => 1,
            'is_active' => true,
            'registration_open_date' => now()->subDays(30)->toDateString(),
            'registration_close_date' => now()->subDays(1)->toDateString(),
            'wa_group_ikhwan' => 'https://chat.whatsapp.com/ikhwan-example',
            'wa_group_akhwat' => 'https://chat.whatsapp.com/akhwat-example',
        ]);
        $period->save();

        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Pendaftaran PPDB ditutup');
        $response->assertDontSee('Gabung Group WA Info');
    }
}
