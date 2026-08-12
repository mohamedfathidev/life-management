<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    protected $signature = 'app:create-user {name?} {email?}';

    protected $description = 'Create a login user (registration is disabled in this app).';

    public function handle(): int
    {
        $name = $this->argument('name') ?: $this->ask('الاسم');
        $email = $this->argument('email') ?: $this->ask('البريد الإلكتروني');
        $password = $this->secret('كلمة المرور');
        $confirm = $this->secret('تأكيد كلمة المرور');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if ($password !== $confirm) {
            $this->error('كلمتا المرور غير متطابقتين.');

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,          // hashed via the model cast
            'email_verified_at' => now(),     // so the "verified" routes work
        ]);

        $this->info("✓ تم إنشاء المستخدم: {$user->email}");
        $this->line('تقدر دلوقتي تسجّل الدخول من /login');

        return self::SUCCESS;
    }
}
