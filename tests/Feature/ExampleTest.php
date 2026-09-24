<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_visiting_the_home_page_are_sent_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }
}
