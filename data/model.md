```
clubs {
	club_id integer pk increments
	name varchar unique
}

seasons {
	season_id integer pk increments
	first_round datetime
	last_round datetime
}

club_season {
	id integer pk increments
	season_id integer *>* seasons.season_id
	club_id integer *>* clubs.club_id
}

stadiums {
	stadium_id integer pk increments
	name varchar unique
}

rounds {
	round_id integer pk increments
	season_id integer *> seasons.season_id
	stadium_id integer *> stadiums.stadium_id
	home_club integer *> clubs.club_id
	away_club integer *> clubs.club_id
	date datetime
	home_score integer
	away_score integer
	result integer
}
```