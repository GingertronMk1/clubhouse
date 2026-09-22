# clubhouse

### Initial models

- [ ] Sports
- [ ] Teams
- [ ] Competitions

### Secondary models

- [ ] People
- [ ] Positions
- [ ] Team/Sport pivot table
- [ ] Games

### Tertiary models

- [ ] Person/Team pivot table
- [ ] Game/Person pivot table

### DBDiagram

<img width="1246" height="1102" alt="ClubHouse" src="https://github.com/user-attachments/assets/b1e3a845-d729-4fae-a58a-22334f4f8436" />

```dbml
Table users {
  id uuid [pk]
  name string
  email string
  password string
}

Table people {
  id uuid [pk]
  name string
  bio text
  dob date
  user_id uuid [ref: -? users.id, unique]
}

Table sports {
  id uuid [pk]
  name string
  description text
  scoring json
  field_diagram text // file path or maybe an svg description
}

Table positions {
  id uuid [pk]
  name string
  description text
  preview_x int
  preview_y int
  sort_order int
  default_number int
  sport_id uuid [ref: > sports.id, not null]
}

Table teams {
  id uuid [pk]
  name string
  description text
}

Table person_team {
  person_id uuid [ref: > people.id, not null]
  team_id uuid [ref: > teams.id, not null]
}

Table games {
  id uuid [pk]
  name string
  start datetime
  description text
  summary text
  sport_id uuid [ref: > sports.id, not null]
  competition_id uuid [ref: > competitions.id, not null]
  team1_id uuid [ref: > teams.id, not null]
  team2_id uuid [ref: > teams.id, not null]
  score json
}

Table competitions {
  id uuid [pk]
  parent_id uuid [ref: >? competitions.id]
  name string
  description text
}

Table game_person {
  game_id uuid [ref: > games.id, not null]
  person_id uuid [ref: > people.id, not null]
  team_id uuid [ref: > teams.id, not null]
  position_id uuid [ref: > positions.id, not null]
  notes text
}

Table team_sports {
  team_id uuid [ref: > teams.id, not null]
  sport_id uuid [ref: > sports.id, not null]
  order int
}
```
