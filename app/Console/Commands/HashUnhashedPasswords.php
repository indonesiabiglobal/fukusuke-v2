<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class HashUnhashedPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:hash-unhashed {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash passwords for users that have unhashed passwords';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Scanning for users with unhashed passwords...');

        $users = User::all();
        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if password is already hashed (bcrypt hashes start with $2y$ and are 60 chars long)
            if ($user->password && !$this->isPasswordHashed($user->password)) {
                if ($dryRun) {
                    $this->warn("Would hash password for user: {$user->username} (ID: {$user->id})");
                } else {
                    $originalPassword = $user->password;
                    $user->password = Hash::make($originalPassword);
                    $user->save();

                    $this->info("✓ Hashed password for user: {$user->username} (ID: {$user->id})");
                }
                $updated++;
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->info("Total users: " . $users->count());

        if ($dryRun) {
            $this->warn("Would update: {$updated} users");
        } else {
            $this->info("Updated: {$updated} users");
        }

        $this->info("Skipped (already hashed): {$skipped} users");

        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a DRY RUN. Run without --dry-run to apply changes.');
        }

        return 0;
    }

    /**
     * Check if a password is already hashed
     *
     * @param string $password
     * @return bool
     */
    private function isPasswordHashed($password)
    {
        // Bcrypt hashes are always 60 characters and start with $2y$
        // Argon2 hashes start with $argon2i$ or $argon2id$
        return (strlen($password) === 60 && strpos($password, '$2y$') === 0) ||
               strpos($password, '$argon2i$') === 0 ||
               strpos($password, '$argon2id$') === 0;
    }
}
