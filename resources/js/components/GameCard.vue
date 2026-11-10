<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { show } from '@/routes/games';

type Named = { name: string };

defineProps<{
    game: {
        id: string;
        name: string;
        start: string | null;
        team1: Named | null;
        team2: Named | null;
        competition: Named | null;
        location: Named | null;
    };
}>();

function formatDate(value: string | null): string {
    return value ? new Date(value).toLocaleString() : 'Kickoff to be announced';
}
</script>

<template>
    <li
        class="club-panel rounded-sm border-l-4 border-l-club-orange p-5 transition hover:shadow-md"
    >
        <p
            class="mb-3 text-xs font-bold tracking-wide text-club-orange uppercase"
        >
            {{ game.competition?.name ?? 'Game' }}
            <span class="text-slate-400"> / {{ formatDate(game.start) }}</span>
        </p>
        <h2 class="text-xl font-black tracking-tight">
            <Link
                :href="show(game.id)"
                class="hover:text-club-orange"
            >
                {{ game.name }}
                <span
                    aria-hidden="true"
                    class="text-club-orange"
                    >↗</span
                >
            </Link>
        </h2>
        <p
            v-if="game.team1 && game.team2"
            class="mt-2 font-semibold text-slate-700"
        >
            {{ game.team1.name }} vs {{ game.team2.name }}
        </p>
        <p
            v-if="game.location"
            class="mt-2 text-sm text-slate-500"
        >
            {{ game.location.name }}
        </p>
    </li>
</template>
