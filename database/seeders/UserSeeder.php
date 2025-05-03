<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //super_admin
        $user = User::factory()->create([
            'name' => 'aye',
            'email' => 'aye@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('qwerty12345'),
        ]);

        // $role = Role::create(['name' => 'admin']);
        // $user->assignRole($role);
        
       
        //admin for shop
        $admin_shop = User::factory()->create([
            'name' => 'shop',
            'email' => 'shop@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('qwerty12345'),
        ]);
        $role_shop = Role::create(['name' => 'admin_shop']);
        $admin_shop->assignRole($role_shop);


        //admin for vet
        $admin_vet = User::factory()->create([
            'name' => 'vetinary',
            'email' => 'vet@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('qwerty12345'),
        ]);
        $role_vet = Role::create(['name' => 'admin_vet']);
        $admin_vet->assignRole($role_vet);
       

        $totalUsers = 15;
        $progressBar = $this->command->getOutput()->createProgressBar($totalUsers);
        $progressBar->setFormat("CREATING USER\n %current%/%max% [%bar%] %percent:3s%%");

        $progressBar->start();

        for ($i = 0; $i < $totalUsers; $i++) {
            User::factory()->create();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->line('');
    }
}
