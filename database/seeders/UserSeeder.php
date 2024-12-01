<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserSeeder extends Seeder
{
    /**
     * For high numbers, it's recommended to truncate "users" table to avoid email conflicts.
     * Note, it has a geometric progression for a number of QuizAnswers will be created by {@see QuizAnswerSeeder}.
     */
    private const int DEFAULT_NUMBER_OF_USERS = 200_000;

    /**
     * Run the database seeds.
     *
     * The current way to create multiple DB records is chosen based on these benchmarks:
     * Chunk 5_000 + UserFactory
     * - Time to insert 1,000,000 users: 176.62 seconds, 201 DB queries
     * - Time to insert 1,000,000 users: 146.1 seconds, 201 DB queries
     * - Time to insert 1,000,000 users: 214.3 seconds, 201 DB queries
     * Chunk 5_000 + UserFactory + Transaction:
     * - Time to insert 1,000,000 users: 222.3 seconds, 201 DB queries
     * Chunk 5_000 + array
     * - Time to insert 1,000,000 users: 77.3 seconds, 201 DB queries
     * - Time to insert 1,000,000 users: 76.2 seconds, 201 DB queries
     * - Time to insert 1,000,000 users: 76.3 seconds, 201 DB queries
     * Chunk 5_000 + array + Transaction
     * -Time to insert 1,000,000 users: 75.7 seconds, 201 DB queries
     * Chunk 8_000 + UserFactory:
     * - Time to insert 1,000,000 users: 241.0 seconds, 126 DB queries
    */
    public function run(int $numberOfUsers = self::DEFAULT_NUMBER_OF_USERS): void
    {
        $now = now();
        $hashedPassword = Hash::make('password');
        /** @var \Illuminate\Support\Collection<int, string> $countryCodes */
        $countryCodes = Country::all(['code'])->pluck('code');

        // $startAt = microtime(true);

        $attributes = [];
        for ($i = 0; $i < $numberOfUsers; $i++) {
            $attributes[] = $this->createUserAttributes($now, $hashedPassword, $countryCodes);
        }

        foreach (array_chunk($attributes, 5_000) as $chunkOfAttributes) {
            // mass insert() is a performance improvement, @see https://x.com/rodolfovmartins/status/1552995618217656320
            User::query()->insert($chunkOfAttributes);
        }

        /**
         * Handy for debugging:
         * echo sprintf("Time to insert %s users: %s seconds%s", number_format($numberOfUsers), number_format(microtime(true) - $startAt, 3), \PHP_EOL);
         */
    }

    /**
     * @param \Illuminate\Support\Collection<int, string> $countryCodes
     * @return array<string, mixed>
     */
    private function createUserAttributes(\DateTimeInterface $now, string $hashedPassword, Collection $countryCodes): array
    {
        /**
         * Note, this code 2-3 times slower: UserFactory::new(['country_code' => $countryCodes->random()])->raw()
         * That's why it partially duplicates UserFactory
         */
        return [
            'country_code' => $countryCodes->random(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => $now,
            'password' => $hashedPassword,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
