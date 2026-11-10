import { clsx } from 'clsx';
import type { ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function fieldCoordinate(value: number): string {
    return `${Math.max(0, Math.min(100, value))}%`;
}
