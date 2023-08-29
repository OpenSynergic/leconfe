<?php

namespace Database\Factories;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Navigation>
 */
class NavigationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Primary Navigation Menu',
            'handle' => 'primary-navigation-menu',
            'items' => '{"51a23abf-cbb6-4bb2-b429-1ab643ef9c0f":{"label":"Home","type":"external-link","data":{"url":"\/","target":null},"children":[]},"ad9a8a3c-4201-46d0-b8fe-db875b6f87aa":{"label":"Conference Info","type":null,"children":{"8ae8caf8-9776-4d9f-8a41-0e6737d706c3":{"label":"Pembicara Utama","type":null,"children":{"7f2d761b-e57a-41dd-8e3a-a4ca8b589b3c":{"label":"Rahman Ramsi","type":null,"children":[]},"dbe1f7c8-63c8-4267-a78f-15a7fd23d112":{"label":"Almadani","type":null,"children":[]}}},"b3cdcc90-7730-429c-9c3a-9760aafc2585":{"label":"Susunan Kepanitiaan","type":null,"children":{"daf3d0fa-46e8-404a-a229-f1fdd5759782":{"label":"Test2","type":null,"children":{"cd28b80f-abcc-47ba-8b16-691f37894a90":{"label":"edasdas","type":null,"children":[]},"f0d70979-d261-49f0-9be6-a35d7bdcd525":{"label":"dasdasdsa","type":null,"children":[]}}},"396b8bb8-852a-4976-a14d-60486cc647b0":{"label":"Test1 ","type":null,"children":[]},"ce46126f-2520-44e1-b330-68075f1e4abe":{"label":"Test3","type":null,"children":[]},"548403fb-3980-4982-a8c8-a72898b92726":{"label":"Test4","type":null,"children":[]}}},"0a13db22-4320-40a1-9f83-3c18373fc993":{"label":"Topik Makalah","type":null,"children":[]},"8420522d-3088-40bc-8bca-136c73838578":{"label":"Jadwal Kegiatan","type":null,"children":[]}}},"4a17b848-760a-4d17-aae4-02e31cf61211":{"label":"Kegiatan","type":null,"children":{"c8b5f332-7a83-4635-944c-e55fb945e9b7":{"label":"Call for Papers","type":"external-link","children":[],"data":{"url":"https:\/\/google.com","target":null}},"546b3e4f-f568-4e8c-9c14-9d9fb9a43d8d":{"label":"Seminar Panel dan Paralel","type":"external-link","data":{"url":"https:\/\/google.com","target":null},"children":[]}}},"880da9d9-c6cc-46a1-af90-065c5b517990":{"label":"Submission","type":null,"children":[]},"98c46617-49e6-4e53-b47a-0549cdadb5eb":{"label":"Registrasi","type":null,"children":[]},"ee42b5d6-f08a-40c4-b6f2-6148dc9c637d":{"label":"Hubungi Kami","type":null,"children":[]}}',
            'conference_id' => Conference::inRandomOrder()->first()->id,
        ];
    }
}
