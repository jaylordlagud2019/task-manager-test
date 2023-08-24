<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

     public function testApiLogin() {
            $body = [
                'email' => 'test@test.com',
                'password' => 'password'
            ];
           $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
                ->assertStatus(200)
                ->assertJsonStructure([
                   "user" => [
                       'id',
                       'name',
                       'email',
                   ],
                    "token"
                ]);           
    }

    public function testMustEnterEmailAndPassword()
    {
        $body = [];
        $this->json('POST','/api/login',$body,['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJson([
            "success" => false,
            "message" => "Validation errors",
            "data" => [
                'email' => ["The email field is required."],
                'password' => ["The password field is required."],
            ]
        ]);
    }    
}


