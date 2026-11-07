<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds for top 10 Pakistani active banks.
     */
    public function run(): void
    {
        $banks = [
            [
                'name'           => 'Meezan Bank Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '01020102345678',
                'branch'         => 'Gulberg Branch, Lahore (0102)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Habib Bank Limited (HBL)',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '00427901234567',
                'branch'         => 'Mall Road Branch, Lahore (0042)',
                'status'         => 'active',
            ],
            [
                'name'           => 'United Bank Limited (UBL)',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '01092345678901',
                'branch'         => 'Main Boulevard Branch, Lahore (0109)',
                'status'         => 'active',
            ],
            [
                'name'           => 'MCB Bank Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '05841234567890',
                'branch'         => 'DHA Phase 3 Branch, Lahore (0584)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Allied Bank Limited (ABL)',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '00100234567890',
                'branch'         => 'Ferozepur Road Branch, Lahore (0010)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Bank Alfalah Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '55011234567890',
                'branch'         => 'Islamic Banking Branch, Gulberg, Lahore (5501)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Faysal Bank Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '30151234567890',
                'branch'         => 'Barkat Market Branch, Lahore (3015)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Askari Bank Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '00281234567890',
                'branch'         => 'Cavalry Ground Branch, Lahore (0028)',
                'status'         => 'active',
            ],
            [
                'name'           => 'Bank Al Habib Limited',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '10051234567890',
                'branch'         => 'Shadman Branch, Lahore (1005)',
                'status'         => 'active',
            ],
            [
                'name'           => 'National Bank of Pakistan (NBP)',
                'account_name'   => 'Baba Hajj Group Pvt Ltd',
                'account_number' => '01231234567890',
                'branch'         => 'Civil Secretariat Branch, Lahore (0123)',
                'status'         => 'active',
            ],
        ];

        foreach ($banks as $data) {
            Bank::updateOrCreate(
                [
                    'name'           => $data['name'],
                    'account_number' => $data['account_number'],
                ],
                $data
            );
        }
    }
}
