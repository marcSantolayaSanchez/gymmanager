<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Trainer;
use App\Models\Workout;
use App\Models\Membership;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Memberships ──────────────────────────────────────────────────
        $basic   = Membership::create(['name' => 'Básica',   'price' => 29.99, 'duration_days' => 30, 'description' => 'Acceso a sala y vestuarios.']);
        $premium = Membership::create(['name' => 'Premium',  'price' => 49.99, 'duration_days' => 30, 'description' => 'Sala + clases grupales + plan nutricional.']);
        $vip     = Membership::create(['name' => 'VIP',      'price' => 89.99, 'duration_days' => 30, 'description' => 'Acceso ilimitado + entrenador personal + sauna.']);

        // ── Admin ────────────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@gymmanager.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Trainers ─────────────────────────────────────────────────────
        $trainersData = [
            ['name' => 'Carlos Vega',   'email' => 'carlos@gymmanager.com', 'speciality' => 'Musculación',          'bio' => 'Especialista en hipertrofia y fuerza. 10 años de experiencia.'],
            ['name' => 'Ana Pérez',     'email' => 'ana@gymmanager.com',    'speciality' => 'CrossFit',              'bio' => 'Entrenadora certificada CrossFit L2.'],
            ['name' => 'Roberto Mora',  'email' => 'roberto@gymmanager.com','speciality' => 'Cardio & Resistencia', 'bio' => 'Maratonista y coach de resistencia aeróbica.'],
        ];

        $trainers = [];
        foreach ($trainersData as $td) {
            $user = User::create([
                'name'     => $td['name'],
                'email'    => $td['email'],
                'password' => Hash::make('password'),
                'role'     => 'trainer',
            ]);
            $trainers[] = Trainer::create([
                'user_id'    => $user->id,
                'speciality' => $td['speciality'],
                'bio'        => $td['bio'],
            ]);
        }

        // ── Clients ──────────────────────────────────────────────────────
        $clientsData = [
            ['name' => 'Sara Morales',    'email' => 'sara@example.com',   'membership' => $premium, 'days_left' => 20,  'weight' => 62, 'height' => 165],
            ['name' => 'Diego Ruiz',      'email' => 'diego@example.com',  'membership' => $vip,     'days_left' => 15,  'weight' => 85, 'height' => 180],
            ['name' => 'Lucía Fernández', 'email' => 'lucia@example.com',  'membership' => $basic,   'days_left' => 4,   'weight' => 58, 'height' => 162],
            ['name' => 'Marco Jiménez',   'email' => 'marco@example.com',  'membership' => $premium, 'days_left' => -5,  'weight' => 90, 'height' => 178],
            ['name' => 'Elena Torres',    'email' => 'elena@example.com',  'membership' => $vip,     'days_left' => 25,  'weight' => 55, 'height' => 160],
            ['name' => 'Pablo Sánchez',   'email' => 'pablo@example.com',  'membership' => $basic,   'days_left' => 18,  'weight' => 75, 'height' => 175],
        ];

        $clients = [];
        foreach ($clientsData as $cd) {
            $user = User::create([
                'name'     => $cd['name'],
                'email'    => $cd['email'],
                'password' => Hash::make('password'),
                'role'     => 'client',
            ]);
            $expires = now()->addDays($cd['days_left']);
            $clients[] = Client::create([
                'user_id'                => $user->id,
                'membership_id'          => $cd['membership']->id,
                'weight'                 => $cd['weight'],
                'height'                 => $cd['height'],
                'birth_date'             => now()->subYears(rand(20, 45)),
                'phone'                  => '6' . rand(10000000, 99999999),
                'membership_starts_at'   => $expires->copy()->subDays($cd['membership']->duration_days),
                'membership_expires_at'  => $expires,
            ]);
        }

        // ── Workouts ─────────────────────────────────────────────────────
        $workoutsData = [
            ['title' => 'Full Body Strength A', 'desc' => 'Sentadillas 4x8, peso muerto 4x6, press banca 4x8.', 'trainer' => 0, 'client' => 0],
            ['title' => 'HIIT Cardio 30min',    'desc' => 'Intervalos 30s ON / 15s OFF, 6 ejercicios funcionales.', 'trainer' => 1, 'client' => 1],
            ['title' => 'Upper Body Pull',       'desc' => 'Dominadas 5x6, remo barra 4x8, curl bíceps 3x12.', 'trainer' => 0, 'client' => 2],
            ['title' => 'Cardio Steady State',   'desc' => '45min en zona 2 de FC. Cinta inclinada 4%.', 'trainer' => 2, 'client' => 3],
            ['title' => 'Leg Day VIP',           'desc' => 'Sentadilla búlgara, prensa 45º, extensiones.', 'trainer' => 0, 'client' => 4],
            ['title' => 'Core & Movilidad',      'desc' => 'Plancha, crunch cable, hip flexor stretch.', 'trainer' => 1, 'client' => 5],
        ];

        foreach ($workoutsData as $i => $wd) {
            Workout::create([
                'trainer_id'     => $trainers[$wd['trainer']]->id,
                'client_id'      => $clients[$wd['client']]->id,
                'title'          => $wd['title'],
                'description'    => $wd['desc'],
                'scheduled_date' => now()->addDays($i),
                'status'         => $i < 2 ? 'completed' : 'pending',
            ]);
        }

        $this->command->info('✅  GymManager seed completado.');
        $this->command->line('   Admin:     admin@gymmanager.com / password');
        $this->command->line('   Entrenador: carlos@gymmanager.com / password');
        $this->command->line('   Cliente:   sara@example.com / password');
    }
}
