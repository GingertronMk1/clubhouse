import { computed, toValue, type MaybeRefOrGetter } from 'vue';

type GameEvent = {
    score: number;
    pivot: {
        competitor_type: string;
        competitor_id: string;
    };
};

type GameWithEvents<Event extends GameEvent> = {
    team1_id: string;
    team2_id: string;
    sport_events: Event[];
    users: { pivot: { id: string; team: number } }[];
};

export function useGameTeamEvents<Event extends GameEvent>(
    game: MaybeRefOrGetter<GameWithEvents<Event>>,
) {
    const teamEvents = computed(() => {
        const currentGame = toValue(game);
        const playerSides = new Map(
            currentGame.users.map((user) => [user.pivot.id, user.pivot.team]),
        );
        const team1: Event[] = [];
        const team2: Event[] = [];

        for (const event of currentGame.sport_events) {
            const { competitor_type, competitor_id } = event.pivot;
            let side: number | undefined;

            if (competitor_type === 'App\\Models\\Team') {
                side =
                    competitor_id === currentGame.team1_id
                        ? 1
                        : competitor_id === currentGame.team2_id
                          ? 2
                          : undefined;
            } else if (competitor_type === 'App\\Models\\GameUser') {
                side = playerSides.get(competitor_id);
            }

            if (side === 1) {
                team1.push(event);
            } else if (side === 2) {
                team2.push(event);
            }
        }

        return { team1, team2 };
    });

    return {
        team1Events: computed(() => teamEvents.value.team1),
        team2Events: computed(() => teamEvents.value.team2),
        team1Score: computed(() =>
            teamEvents.value.team1.reduce(
                (total, event) => total + event.score,
                0,
            ),
        ),
        team2Score: computed(() =>
            teamEvents.value.team2.reduce(
                (total, event) => total + event.score,
                0,
            ),
        ),
    };
}
