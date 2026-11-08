<?php

namespace App\Models;

use Illuminate\Support\Collection;

class ScoringBreakdown
{
    /**
     * @param  Collection<int, Scoring>  $methods
     * @return list<array{name: string, value: int, count: int}>|null
     */
    public static function infer(Collection $methods, int $total): ?array
    {
        if ($total < 0) {
            return null;
        }

        $methods = $methods->values();
        $queue = [[0, 0, array_fill(0, $methods->count(), 0)]];
        $position = 0;
        $visited = ['0:0' => true];

        while (isset($queue[$position])) {
            [$points, $opportunities, $counts] = $queue[$position++];
            if ($points === $total) {
                return array_values($methods->map(fn (Scoring $method, int $index): array => [
                    'name' => $method->name,
                    'value' => $method->value,
                    'count' => $counts[$index],
                ])->all());
            }

            foreach ($methods as $index => $method) {
                if ($method->value <= 0 || $points + $method->value > $total) {
                    continue;
                }
                $isConversion = in_array($method->name, ['Conversion', 'Extra point', 'Two-point conversion'], true);
                if ($isConversion && $opportunities === 0) {
                    continue;
                }
                $newOpportunities = $opportunities + (int) in_array($method->name, ['Try', 'Touchdown'], true) - (int) $isConversion;
                $newPoints = $points + $method->value;
                $key = $newPoints.':'.$newOpportunities;
                if (isset($visited[$key])) {
                    continue;
                }
                $visited[$key] = true;
                $newCounts = $counts;
                $newCounts[$index]++;
                $queue[] = [$newPoints, $newOpportunities, $newCounts];
            }
        }

        return null;
    }
}
