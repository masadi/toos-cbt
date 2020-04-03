<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\User;
use App\Models\RoleHierarchy;

class UsersAndNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 10;
        $numberOfNotes = 100;
        $usersIds = array();
        $statusIds = array();
        $faker = Faker::create();
        /* Create roles */
        $adminRole = Role::create(['name' => 'admin']); 
        RoleHierarchy::create([
            'role_id' => $adminRole->id,
            'hierarchy' => 1,
        ]);
        $sekolahRole = Role::create(['name' => 'sekolah']); 
        RoleHierarchy::create([
            'role_id' => $sekolahRole->id,
            'hierarchy' => 2,
        ]);
        $ptkRole = Role::create(['name' => 'ptk']); 
        RoleHierarchy::create([
            'role_id' => $ptkRole->id,
            'hierarchy' => 3,
        ]);
        $proktorRole = Role::create(['name' => 'proktor']); 
        RoleHierarchy::create([
            'role_id' => $proktorRole->id,
            'hierarchy' => 4,
        ]);
        $peserta_didikRole = Role::create(['name' => 'peserta_didik']); 
        RoleHierarchy::create([
            'role_id' => $peserta_didikRole->id,
            'hierarchy' => 5,
        ]);
        $userRole = Role::create(['name' => 'user']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 6,
        ]);
        $guestRole = Role::create(['name' => 'guest']); 
        RoleHierarchy::create([
            'role_id' => $guestRole->id,
            'hierarchy' => 7,
        ]);
        /*  insert users   */
        $user = User::create([ 
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'masadi.com@gmail.com',
            'email_verified_at' => now(),
            'password' => app('hash')->make('12345678'),//'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'timezone' => 'Asia/Jakarta',
            'remember_token' => Str::random(10),
            'menuroles' => 'admin' 
        ]);
        $user->assignRole('admin');
    }
}