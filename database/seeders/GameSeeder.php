<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * The fixture snapshot comes from fixturedownload.com (NFL and NRL),
 * rugbyleagueproject.org (Super League), and internationaltouch.org
 * (Touch World Cup men's and women's open divisions). Times are stored in UTC.
 * NFL kickoffs that were unannounced when the snapshot was taken are null.
 * Touch results displayed as 0–0 are omitted because they may be forfeits.
 */
class GameSeeder extends Seeder
{
    public function run(): void
    {
        $competitions = Competition::query()->without(['parent', 'location'])->whereIn('name', [
            '2026 NFL season', '2026 NRL season', '2026 Super League season', '2024 Touch World Cup',
        ])->get()->keyBy('name');
        $sports = Sport::query()->whereIn('name', ['American Football', 'Rugby League', 'Touch Rugby'])->get()->keyBy('name');
        $clubs = Club::query()->get()->keyBy('name');
        $teams = Team::query()->without('club')->get()->keyBy(fn (Team $team) => $team->club_id.'|'.$team->name);
        $locations = Location::query()->get()->keyBy('name');
        $competitionNames = ['nfl' => '2026 NFL season', 'nrl' => '2026 NRL season', 'super' => '2026 Super League season', 'touch' => '2024 Touch World Cup'];
        $sportNames = ['nfl' => 'American Football', 'nrl' => 'Rugby League', 'super' => 'Rugby League', 'touch' => 'Touch Rugby'];

        /** @var list<array{competition: string, key: string, division: string, home: string, away: string, start: ?string, venue: string, round: string, score: array{home: int, away: int}|array{}}> $fixtures */
        $fixtures = json_decode(File::get(database_path('seeders/data/competition-fixtures.json')), true, flags: JSON_THROW_ON_ERROR);

        foreach ($fixtures as $fixture) {
            $competition = $competitions->get($competitionNames[$fixture['competition']]);
            $sport = $sports->get($sportNames[$fixture['competition']]);
            if (! $competition || ! $sport) {
                throw new \RuntimeException('Run the sport and competition seeders before the game seeder.');
            }

            $fixtureTeams = [];
            foreach (['home', 'away'] as $side) {
                $club = $clubs->get($fixture[$side]);
                if (! $club) {
                    throw new \RuntimeException("Missing club: {$fixture[$side]}. Run the club seeder before the game seeder.");
                }
                $teamKey = $club->id.'|'.$fixture['division'];
                $team = $teams->get($teamKey);
                if (! $team) {
                    $team = $club->teams()->firstOrCreate(
                        ['name' => $fixture['division']],
                        ['description' => "{$fixture['division']} for {$club->name}."],
                    );
                    $teams->put($teamKey, $team);
                }
                $fixtureTeams[$side] = $team;
            }

            $location = $locations->get($fixture['venue']);
            if (! $location) {
                $location = Location::query()->firstOrCreate(
                    ['name' => $fixture['venue']],
                    ['description' => $fixture['venue'], 'additional_information' => []],
                );
                $locations->put($fixture['venue'], $location);
            }

            $game = Game::query()->without(['sport', 'competition', 'club1', 'club2', 'location'])->firstOrCreate(
                ['competition_id' => $competition->id, 'description' => "{$fixture['competition']} fixture {$fixture['key']}"],
                [
                    'name' => "{$fixture['home']} vs {$fixture['away']}",
                    'start' => $fixture['start'],
                    'summary' => trim($fixture['division'].' — '.$fixture['round']),
                    'sport_id' => $sport->id,
                    'club1_id' => $fixtureTeams['home']->id,
                    'club2_id' => $fixtureTeams['away']->id,
                    'result' => $fixture['score'],
                    'club1_score' => [],
                    'club2_score' => [],
                    'location_id' => $location->id,
                ],
            );

            if ($game->result === [] && $fixture['score'] !== []) {
                $game->update(['result' => $fixture['score']]);
            }
        }
    }
}
