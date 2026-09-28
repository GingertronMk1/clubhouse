export * from './auth';

export const ucfirst = (name: string, separator?: string) =>
    name
        .split(separator ?? '-')
        .map((word: string) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
