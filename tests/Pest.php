<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

function actingAsUser(): \App\Models\User
{
    $user = \App\Models\User::factory()->create();
    test()->actingAs($user, 'sanctum');

    return $user;
}
