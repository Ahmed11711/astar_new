<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'username'   => 'admin_user',
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'email'      => 'admin@example.com',
                'role'       => 'admin',
                'phone'      => '01000000001',
            ],
            [
                'username'   => 'teacher_pro',
                'first_name' => 'Sarah',
                'last_name'  => 'Teacher',
                'email'      => 'teacher@example.com',
                'role'       => 'teacher',
                'phone'      => '01000000002',
            ],
            [
                'username'   => 'high_school_alpha',
                'first_name' => 'Green',
                'last_name'  => 'School',
                'email'      => 'school@example.com',
                'role'       => 'student',
                'phone'      => '01000000003',
            ],
            [
                'username'   => 'data_wizard',
                'first_name' => 'Mark',
                'last_name'  => 'Entry',
                'email'      => 'dataentry@example.com',
                'role'       => 'data_entry',
                'phone'      => '01000000004',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'password'          => Hash::make('password123'),
                'is_email_verified' => true,
                'student_type'      => $user['role'] === 'student' ? 'school' : 'individual',
                'is_active'         => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]));

            // 3. إنشاء سجل OTP لكل مستخدم تلقائياً
            DB::table('user_otps')->insert([
                'email'      => $user['email'],
                'otp_code'   => rand(1111, 9999), // كود عشوائي
                'expires_at' => Carbon::now()->addHours(1),
                'active'     => 1, // نشط كما طلبت
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
