import assert from 'node:assert/strict';
import { test } from 'node:test';
import { ref } from 'vue';
import { useGameTeamEvents } from '../../resources/js/composables/useGameTeamEvents.ts';

await test('separates team and player events and reacts to game changes', () => {
    const event = (id, type, competitorId, score = 0) => ({
        id,
        score,
        pivot: { competitor_type: type, competitor_id: competitorId },
    });
    const team = 'App\\Models\\Team';
    const player = 'App\\Models\\GameUser';
    const game = ref({
        team1_id: 'team-1',
        team2_id: 'team-2',
        users: [
            { pivot: { id: 'player-1', team: 1 } },
            { pivot: { id: 'player-2', team: 2 } },
        ],
        sport_events: [
            event('a', team, 'team-1', 2),
            event('b', player, 'player-2', 5),
            event('c', player, 'player-1', 3),
            event('d', team, 'team-2', 2),
            event('unknown-team', team, 'other'),
            event('unknown-player', player, 'other'),
            event('unknown-type', 'other', 'team-1'),
        ],
    });

    const { team1Events, team2Events, team1Score, team2Score } =
        useGameTeamEvents(game);

    assert.deepEqual(
        team1Events.value.map((item) => item.id),
        ['a', 'c'],
    );
    assert.deepEqual(
        team2Events.value.map((item) => item.id),
        ['b', 'd'],
    );
    assert.equal(team1Score.value, 5);
    assert.equal(team2Score.value, 7);

    game.value.sport_events.push(event('e', team, 'team-2', 3));
    assert.deepEqual(
        team2Events.value.map((item) => item.id),
        ['b', 'd', 'e'],
    );
    assert.equal(team2Score.value, 10);

    game.value.sport_events = [];
    assert.equal(team1Score.value, 0);
    assert.equal(team2Score.value, 0);
});
