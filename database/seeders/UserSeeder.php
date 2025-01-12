<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $course_ids = Course::pluck('id')->toArray();
        for ($i = 0; $i < 3; $i++) {
            $name = $faker->name;
            $user = User::create([
                'name' => $name,
                'email' => $name . '@example.com',
                'password'  => bcrypt('123456'),
            ]);

            $user->Courses()->attach($course_ids);
        }
    }
}
