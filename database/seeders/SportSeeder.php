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
        $this->command->withProgressBar($this->getSports(), function (array $sport) {
            Sport::query()->updateOrCreate($sport);
        });
    }

    public function getSports(): array
    {
        return [
            [
                'name' => 'Rugby League',
                'description' => 'Rugby league football, commonly known as rugby league, is a full-contact sport played by two teams of thirteen players on a rectangular field measuring 68 m (74 yd) wide and 112–122 m (122–133 yd) long with H-shaped posts at both ends. It is one of the two major codes of rugby football, the other being rugby union.',
                'scoring' => [
                    'try' => 4,
                    'conversion' => 2,
                    'penalty_goal' => 2,
                    'drop_goal' => 1,
                ],
            ],
            [
                'name' => 'Rugby Union',
                'description' => 'Rugby union football, commonly known simply as rugby union or often just rugby, is a close-contact team sport that originated at Rugby School in England in the first half of the 19th century. Rugby involves running with the ball in hand. In its most common form, the game is played between two teams of 15 players each, using an oval-shaped ball on a rectangular field called a pitch. The pitch has H-shaped goalposts at both ends. The objective of the game is to score more points than the opposing team by scoring tries, conversion kicks, penalties, and drop goals. It is one of the two major codes of rugby football, the other being rugby league.',
                'scoring' => [
                    'try' => 5,
                    'conversion' => 2,
                    'penalty_goal' => 3,
                    'drop_goal' => 3,
                ],
            ],
            [
                'name' => 'Touch Rugby',
                'description' => 'Touch (also known as touch football or touch rugby league) is a variant of rugby league that is conducted under the direction of the Federation of International Touch (FIT). Though it shares similarities and history with rugby league, it is recognised as a sport in its own right due to its differences which have been developed over the sport\'s lifetime.',
                'scoring' => [
                    'try' => 1,
                ],
            ],
        ];
    }
}
