<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * There is no create/edit screen anywhere in SchoolM for the `term` table —
 * it was clearly meant to be populated once, directly in the database, and
 * then just selected from a dropdown when building fee structures. This
 * seeder recreates that starting data so the Accounts "Create Fee
 * Structure" and Admin "Admit Student" forms have a Term to select on a
 * fresh install, exactly like the original local database did.
 *
 * Safe to re-run: it only inserts a term if one with that name doesn't
 * already exist, and it never touches existing rows.
 */
class TermSeeder extends Seeder
{
    public function run(): void
    {
        $year = (int) date('Y');

        $terms = [
            ['TermName' => 'Term 1', 'StartDate' => "{$year}-01-01", 'EndDate' => "{$year}-04-30"],
            ['TermName' => 'Term 2', 'StartDate' => "{$year}-05-01", 'EndDate' => "{$year}-08-31"],
            ['TermName' => 'Final Term', 'StartDate' => "{$year}-09-01", 'EndDate' => "{$year}-12-31"],
        ];

        foreach ($terms as $term) {
            $exists = DB::table('term')->where('TermName', $term['TermName'])->exists();

            if (! $exists) {
                DB::table('term')->insert($term);
            }
        }
    }
}
