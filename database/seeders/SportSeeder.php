<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getSports() as $sport) {
            Sport::query()->firstOrCreate($sport);
        }

        Sport::factory()->count(10)->create();
    }

    private function getSports(): array
    {
        return [
            [
                'name' => 'Rugby League',
                'description' => 'Rugby league football, commonly known as rugby league in English-speaking countries and rugby 13/XIII in non-Anglophone Europe, is a full-contact sport played by two teams of thirteen players on a rectangular field measuring 68 m (74 yd) wide and 112–122 m (122–133 yd) long with H-shaped posts at both ends. It is one of the two major codes of rugby football, the other being rugby union.',
            ],
            [
                'name' => 'Rugby Union',
                'description' => 'Rugby union football, commonly known simply as rugby union in English-speaking countries and rugby 15/XV in non-Anglophone Europe, or often just rugby, is a close-contact team sport that originated at Rugby School in England in the first half of the 19th century. Rugby is based on running with the ball in hand. In its most common form, a game is played between two teams of 15 players each, using an oval-shaped ball on a rectangular field called a pitch. The field has H-shaped goalposts at both ends.',
            ],
            [
                'name' => 'Touch Rugby',
                'description' => 'Touch (also known as touch football or touch rugby) is a variant of rugby league that is conducted under the direction of the Federation of International Touch (FIT). Though it shares similarities and history with rugby league, it is recognised as a sport in its own right due to its differences which have been developed over the sport\'s lifetime. Touch is a variation of rugby league with the tackling of opposing players replaced by a touch. As touches must be made with minimal force, touch is therefore considered a limited-contact sport. The original basic rules of touch were established in the 1960s by members of the South Sydney Junior Rugby League Club in Sydney, Australia.',
            ],
            [
                'name' => 'American Football',
                'description' => 'American football, referred to simply as football in the United States and Canada and also known as gridiron football,[nb 1] is a team sport played by two teams of eleven players on a rectangular field with goalposts at each end. The offense, the team with possession of the oval-shaped football, attempts to advance down the field by running with the ball or throwing it, while the defense, the team without possession of the ball, aims to stop the offense\'s advance and to take control of the ball for themselves. The offense must advance the ball at least ten yards in four downs or plays; if they fail, they turn over the football to the defense, but if they succeed, they are given a new set of four downs to continue the drive. Points are scored primarily by advancing the ball into the opposing team\'s end zone for a touchdown or kicking the ball through the opponent\'s goalposts for a field goal. The team with the most points at the end of the game wins.',
            ],
            [
                'name' => 'Canadian Football',
                'description' => 'Canadian football, or simply football, is a sport in Canada in which two teams of 12 players each compete on a field 110 yards (101 m) long and 65 yards (59 m) wide, attempting to advance a pointed oval-shaped ball into the opposing team\'s end zone.',
            ],
        ];
    }
}
