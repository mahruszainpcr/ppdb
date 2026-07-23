<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentUserSeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            ['name' => 'AISYAH NUR RAHMA', 'username' => 'AISYAHNURRAHMA', 'password' => '250512'],
            ['name' => 'ALYA QANITA', 'username' => 'ALYAQANITA', 'password' => '170212'],
            ['name' => 'AQILAH AZ ZAHRAH', 'username' => 'AQILAHAZZAHRAH', 'password' => '220512'],
            ['name' => 'AZZAHRATUL HANNA AFFANDI', 'username' => 'AZZAHRATULHANNAAFFANDI', 'password' => '020112'],
            ['name' => 'DELISHA HASANAH', 'username' => 'DELISHAHASANAH', 'password' => '191211'],
            ['name' => 'FAWNIA AZARIA', 'username' => 'FAWNIAAZARIA', 'password' => '230811'],
            ['name' => 'HUSNA HAFIDHAH ADELANI', 'username' => 'HUSNAHAFIDHAHADELANI', 'password' => '140812'],
            ['name' => 'INAYAH AL HARITS', 'username' => 'INAYAHALHARITS', 'password' => '130412'],
            ['name' => 'JAIDA RAHMAH', 'username' => 'JAIDARAHMAH', 'password' => '021111'],
            ['name' => 'NABILLA ALWI SUDITIO', 'username' => 'NABILLAALWISUDITIO', 'password' => '040811'],
            ['name' => 'NAEEMA KHAIRUNNISA', 'username' => 'NAEEMAKHAIRUNNISA', 'password' => '100912'],
            ['name' => 'NAIFA DZAKIRA', 'username' => 'NAIFADZAKIRA', 'password' => '010712'],
            ['name' => 'NAURA ZUHRAH', 'username' => 'NAURAZUHRAH', 'password' => '160712'],
            ['name' => 'NAYLA WULAN RAMADHANI', 'username' => 'NAYLAWULANRAMADHANI', 'password' => '210811'],
            ['name' => 'RADIFA FITRI', 'username' => 'RADIFAFITRI', 'password' => '250112'],
            ['name' => 'SALMA FAIZAH', 'username' => 'SALMAFAIZAH', 'password' => '261011'],
            ['name' => 'SIVANA RATU LATISYA', 'username' => 'SIVANARATULATISYA', 'password' => '230112'],
            ['name' => 'SYIFAA QOLBIYAH YENDRA', 'username' => 'SYIFAAQOLBIYAHYENDRA', 'password' => '091211'],
            ['name' => 'TASYA AQILA PINOV', 'username' => 'TASYAQILAPINOV', 'password' => '281211'],
            ['name' => 'WAN AISYAH', 'username' => 'WANAISYAH', 'password' => '201111'],
            ['name' => 'YULIA MIS ZAHWA', 'username' => 'YULIAMISZAHWA', 'password' => '090812'],
            ['name' => 'AIMAN CAILANI KASWA', 'username' => 'AIMANCAILANIKASWA', 'password' => '270811'],
            ['name' => 'ALIF ANDRI WIJAYA', 'username' => 'ALIFANDRIWIJAYA', 'password' => '280412'],
            ['name' => 'ARKAN ATAYA NARISKAL', 'username' => 'ARKANATAYANARISKAL', 'password' => '040312'],
            ['name' => 'DEEYA HILMY FATHURRAHMAN', 'username' => 'DEEYAHILMYFATHURRAHMAN', 'password' => '070512'],
            ['name' => 'DIMAS RAYHAN ABRAR', 'username' => 'DIMASRAYHANABRAR', 'password' => '230911'],
            ['name' => 'FAUZAN IBRAHIM HISYAM', 'username' => 'FAUZANIBRAHIMHISYAM', 'password' => '141012'],
            ['name' => 'FUDHAIL KOKOH ALFATIH', 'username' => 'FUDHAILKOKOHALFATIH', 'password' => '140512'],
            ['name' => 'HABIL RAMA ABDILLAH', 'username' => 'HABILRAMAABDILLAH', 'password' => '141111'],
            ['name' => 'HAFIZ YUSUF WARDHANA', 'username' => 'HAFIZYUSUFWARDHANA', 'password' => '060412'],
            ['name' => 'M. RAFIF SAVA ARIVYA', 'username' => 'MRAFIFSAVAARIVYA', 'password' => '231211'],
            ['name' => 'M.ALHAFIZ', 'username' => 'MALHAFIZ', 'password' => '290312'],
            ['name' => 'MUHAMMAD ABDUL RAHMAN', 'username' => 'MUHAMMADABDULRAHMAN', 'password' => '211111'],
            ['name' => 'MUHAMMAD MARFEL', 'username' => 'MUHAMMADMARFEL', 'password' => '170312'],
            ['name' => 'MUHAMMAD NAUFAL', 'username' => 'MUHAMMADNAUFAL', 'password' => '290711'],
            ['name' => 'MUHAMMAD NUR HASSAN SYAHME', 'username' => 'MUHAMMADNURHASSANSYAHME', 'password' => '000000'],
            ['name' => 'MUHAMMAD NUR HUSEIN SYAHME', 'username' => 'MUHAMMADNURHUSEINSYAHME', 'password' => '000000'],
            ['name' => 'PARNAUNGAN SITOMPUL', 'username' => 'PARNAUNGANSITOMPUL', 'password' => '080512'],
            ['name' => 'RAFA HABIB MAHASIN', 'username' => 'RAFAHABIBMAHASIN', 'password' => '290511'],
            ['name' => 'SALMAN AL-FARISY YUNUS', 'username' => 'SALMANALFARISYYUNUS', 'password' => '090512'],
            ['name' => 'SHABIAN AL BELDEN', 'username' => 'SHABIANALBELDEN', 'password' => '280212'],
            ['name' => 'YAZID HILAL ERDANI', 'username' => 'YAZIDHILALERDANI', 'password' => '310711'],
            ['name' => 'YUSUF FAHRI NUR HIDAYAT', 'username' => 'YUSUFFAHRINURHIDAYAT', 'password' => '060911'],
            ['name' => 'ZAIDAN FARHAN MU\'AFA', 'username' => 'ZAIDANFARHANMUAFA', 'password' => '010312'],
            ['name' => 'ZAMZAMI ABDULLAH', 'username' => 'ZAMZAMIABDULLAH', 'password' => '221111'],
        ];

        foreach ($parents as $parent) {
            $phone = strtolower($parent['username']);
            $email = $phone . '@parent.local';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $parent['name'],
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make($parent['password']),
                    'role' => 'parent',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
