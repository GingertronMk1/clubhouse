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
    latitude: number;
    longitude: number;
    coordinates?: [string, string] | null;
    links: {title: string; url: string}[];
};
