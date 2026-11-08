<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            'Rugby League' => [
                ['Fullback', 'Covers kicks and supports attacking play.', 50, 85, 1],
                ['Right Wing', 'Finishes attacks and defends the right edge.', 90, 65, 2],
                ['Right Centre', 'Attacks and defends on the right edge.', 75, 60, 3],
                ['Left Centre', 'Attacks and defends on the left edge.', 25, 60, 4],
                ['Left Wing', 'Finishes attacks and defends the left edge.', 10, 65, 5],
                ['Stand-off', 'Organises attacking play alongside the scrum-half.', 40, 55, 6],
                ['Scrum-half', 'Directs the team and distributes the ball.', 60, 55, 7],
                ['Left Prop', 'Carries the ball and defends in the middle.', 35, 35, 8],
                ['Hooker', 'Distributes the ball from the play-the-ball.', 50, 35, 9],
                ['Right Prop', 'Carries the ball and defends in the middle.', 65, 35, 10],
                ['Left Second Row', 'Runs and defends on the left edge.', 25, 40, 11],
                ['Right Second Row', 'Runs and defends on the right edge.', 75, 40, 12],
                ['Loose Forward', 'Links the forwards and backs in attack and defence.', 50, 45, 13],
            ],
            'Rugby Union' => [
                ['Loosehead Prop', 'Anchors the left side of the scrum.', 35, 30, 1],
                ['Hooker', 'Hooks the ball in scrums and usually throws into lineouts.', 50, 30, 2],
                ['Tighthead Prop', 'Anchors the right side of the scrum.', 65, 30, 3],
                ['Left Lock', 'Provides power in scrums and competes at lineouts.', 43, 38, 4],
                ['Right Lock', 'Provides power in scrums and competes at lineouts.', 57, 38, 5],
                ['Blindside Flanker', 'Defends and contests possession around the breakdown.', 25, 40, 6],
                ['Openside Flanker', 'Contests possession and supports play at the breakdown.', 75, 40, 7],
                ['Number Eight', 'Controls the ball at the back of the scrum and carries it forward.', 50, 46, 8],
                ['Scrum-half', 'Passes from scrums, rucks and mauls to start attacking play.', 58, 52, 9],
                ['Fly-half', 'Directs the attack and distributes or kicks the ball.', 65, 58, 10],
                ['Left Wing', 'Finishes attacks and defends the left edge.', 10, 70, 11],
                ['Inside Centre', 'Carries and distributes the ball in midfield.', 35, 62, 12],
                ['Outside Centre', 'Attacks and defends in the wider midfield.', 25, 66, 13],
                ['Right Wing', 'Finishes attacks and defends the right edge.', 90, 70, 14],
                ['Fullback', 'Covers the backfield and joins attacking moves.', 50, 85, 15],
            ],
            'Touch Rugby' => [
                ['Left Wing', 'Attacks and defends the left edge.', 10, 50, 1],
                ['Left Link', 'Connects the left wing and middle players.', 25, 50, 2],
                ['Left Middle', 'Organises play and defends in the middle.', 42, 50, 3],
                ['Right Middle', 'Organises play and defends in the middle.', 58, 50, 4],
                ['Right Link', 'Connects the right wing and middle players.', 75, 50, 5],
                ['Right Wing', 'Attacks and defends the right edge.', 90, 50, 6],
            ],
            'American Football' => [
                ['Quarterback', 'Directs the offense and passes or hands off the ball.', 50, 17, 1],
                ['Running Back', 'Carries the ball and may catch passes or block.', 50, 29, 22],
                ['Fullback', 'Blocks and can carry or catch the ball.', 45, 23, 44],
                ['Wide Receiver', 'Runs routes and catches passes.', 10, 8, 11],
                ['Tight End', 'Blocks and runs routes as a receiver.', 75, 8, 85],
                ['Center', 'Snaps the ball and blocks defenders.', 50, 8, 60],
                ['Offensive Guard', 'Blocks defenders on the interior offensive line.', 40, 8, 65],
                ['Offensive Tackle', 'Blocks defenders on the outside of the offensive line.', 30, 8, 75],
                ['Defensive Tackle', 'Defends the interior line against runs and passes.', 45, 40, 90],
                ['Defensive End', 'Defends the edge and pressures the quarterback.', 25, 40, 95],
                ['Nose Tackle', 'Defends the middle of the defensive line.', 50, 40, 99],
                ['Inside Linebacker', 'Defends runs and covers receivers in the middle.', 45, 48, 52],
                ['Outside Linebacker', 'Defends the edge, covers receivers and can rush the passer.', 20, 48, 55],
                ['Cornerback', 'Covers receivers and defends against passes.', 10, 56, 24],
                ['Free Safety', 'Provides deep pass coverage and supports the defence.', 40, 62, 20],
                ['Strong Safety', 'Provides pass coverage and supports run defence.', 60, 60, 31],
                ['Placekicker', 'Kicks field goals, extra points and kickoffs.', 50, 84, 4],
                ['Punter', 'Kicks punts to change field position.', 50, 90, 5],
                ['Long Snapper', 'Snaps the ball on punts and place kicks.', 50, 70, 48],
                ['Holder', 'Receives and holds the snap for place kicks.', 50, 77, 6],
                ['Kick Returner', 'Returns kickoffs to gain field position.', 50, 94, 23],
                ['Punt Returner', 'Fields and returns punts.', 50, 98, 10],
                ['Gunner', 'Covers kicks and punts downfield.', 10, 70, 27],
            ],
        ];

        foreach ($sports as $sportName => $positions) {
            $sport = Sport::query()->where('name', $sportName)->firstOrFail();

            foreach ($positions as $sortOrder => [$name, $description, $previewX, $previewY, $defaultNumber]) {
                $sport->positions()->updateOrCreate(['name' => $name], [
                    'description' => $description,
                    'preview_x' => $previewX,
                    'preview_y' => $previewY,
                    'sort_order' => $sortOrder + 1,
                    'default_number' => $defaultNumber,
                ]);
            }
        }
    }
}
