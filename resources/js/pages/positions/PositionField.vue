<script setup lang="ts">
defineProps<{
    position: {
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    };
    otherPositions: {
        id: string;
        name: string;
        preview_x: number;
        preview_y: number;
        default_number: number;
    }[];
}>();

function fieldCoordinate(value: number): string {
    return `${Math.max(0, Math.min(100, Number(value) || 0))}%`;
}
</script>

<template>
    <div
        class="relative aspect-square w-full overflow-hidden rounded-lg border-4 border-emerald-800 bg-emerald-700"
        aria-label="Position preview field"
    >
        <div
            class="pointer-events-none absolute inset-4 border-2 border-white/70"
            aria-hidden="true"
        >
            <div
                class="absolute top-1/2 w-full border-t-2 border-white/70"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 h-1/4 w-1/4 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white/70"
            ></div>
        </div>
        <div class="absolute inset-5">
            <div
                v-for="other in otherPositions"
                :key="other.id"
                :style="{
                    left: fieldCoordinate(other.preview_x),
                    top: fieldCoordinate(other.preview_y),
                }"
                :title="other.name"
                :aria-label="`${other.name}, number ${other.default_number}`"
                class="absolute flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-slate-900 text-xs font-semibold text-white shadow"
            >
                {{ other.default_number }}
            </div>
            <div
                :style="{
                    left: fieldCoordinate(position.preview_x),
                    top: fieldCoordinate(position.preview_y),
                }"
                :aria-label="`Current position: ${position.name}, number ${position.default_number}`"
                class="absolute z-10 flex h-9 w-9 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-white bg-amber-500 text-xs font-bold text-slate-950 shadow ring-2 ring-amber-200"
            >
                {{ position.default_number }}
            </div>
        </div>
    </div>
</template>
