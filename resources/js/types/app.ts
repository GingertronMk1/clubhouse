export type Sport = {
    id: string;
    name: string;
    description: string;
    scoring: Record<string, number>;
    field_diagram: string;
};

export type Location = {
    id: string;
    name: string;
    description: string;
    coordinates: [string, string] | null;
}
