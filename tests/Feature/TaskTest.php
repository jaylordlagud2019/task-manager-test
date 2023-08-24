<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\User;
use App\Task;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testTask__ListbyOwner() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        //GET PARAMS
        //status [pending,in_progress,completed]
        //per_page
        //sort_by 
        //sort - [asc,desc]
    
        $this->json('GET','/api/tasks?per_page=1',[],['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonStructure(["success",
                "data"=>[
                    "current_page",
                    "data",
                    "first_page_url",
                    "from",
                    "last_page",
                    "last_page_url",
                    "next_page_url",
                    "path",
                    "per_page",
                    "prev_page_url",
                    "to",
                    "total"
                ]
            ]);        
     }

     public function testTask__CreateSuccessful() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        $body = [
            "title"=>"test generate",
            "description"=>"test description",
            "due_date"=>"10-10-2023",
            "status"=>"pending"
        ];

        $this->json('POST','/api/tasks',$body,['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data"=>[
                    "id",
                    "title",
                    "description",
                    "due_date",
                    "status"
                    ]
            ]);        
     }    
     
     public function testTask__CreateValidations() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
    
        $body = [
        ];

        $this->json('POST','/api/tasks',$body,['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson([
                    "success"=>false,
                    "data"=>[
                        "title"=>[
                            "The title field is required."
                        ],
                        "description"=>[
                            "The description field is required."
                        ],
                        "due_date"=>[
                            "The due date field is required."
                        ],
                        "status"=>[
                            "The status field is required."
                        ]
                    ]
            ]);        
     }     

     public function testTask__UpdateSuccessful() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
        $Task = Task::create([
            'user_id'=>$user->id,
            "title"=>"original",
            "description"=>"description",
            "due_date"=> Carbon::parse("10-10-2025"),
            "status"=>"pending"            
        ]);

        $new_data = [
            "title"=>"Updated title",
            "description"=>"Updated description",
            "due_date"=>"10-10-2023",
            "status"=>"completed"
        ];

        $this->json('POST','/api/tasks/'.$Task->id,$new_data,['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson([
                    "success"=>true,
                    "data"=>[
                        "id"=>$Task->id,
                        "title"=>"Updated title",
                        "description"=>"Updated description",
                        "due_date"=>"2023-10-10T00:00:00.000000Z",
                        "status"=>"completed"
                    ]
            ]);        
     } 
     
     
     public function testTask__DeleteSuccessful() {
        $user = factory(User::class)->create(['name' => 'test']);
        $token = $user->createToken('auth_token')->plainTextToken;
        $Task = Task::create([
            'user_id'=>$user->id,
            "title"=>"original",
            "description"=>"description",
            "due_date"=> Carbon::parse("10-10-2025"),
            "status"=>"pending"            
        ]);

        $this->json('DELETE','/api/tasks/'.$Task->id,[],['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200)
            ->assertJson([
                    "success"=>true,
                    "data"=>[
                        "message"=>"Delete successful"
                        ]
            ]);        
     } 

}     