<?php
	// Local SQL Procedures
	$SQLProc = array(
		'ListCountries' => "
			SELECT id, name FROM countries ORDER BY name ASC
		",
		'ListIndustries' => "
			SELECT id, industry AS name FROM directories ORDER BY industry ASC
		",
		// DIRECTORIES
		'ShowDirectories' => "
			SELECT d.id, d.logo, d.industry, c.name AS country, d.organisation AS name
			FROM directories d
			LEFT OUTER JOIN countries c ON c.id = d.country
			%condition%
			ORDER BY d.organisation ASC
		",
		'ShowDirectoriesAdmin' => "
			SELECT d.id, d.logo, d.description, d.organisation AS name, d.clicks
			FROM directories d
			LEFT OUTER JOIN countries c ON c.id = d.country
			%condition%
			ORDER BY d.organisation ASC
		",
		'ShowDirectory' => "
			SELECT d.logo, d.industry, c.name AS country, d.organisation AS name,
				d.description, d.phone, d.URL, d.email
			FROM directories d
			LEFT OUTER JOIN countries c ON c.id = d.country
			WHERE d.id = %id%
		",
		'DeleteListing' => "
			DELETE FROM directories WHERE id = %id%
		",
		'RecordListingClick' => "
			UPDATE directories SET clicks = clicks + 1 WHERE id = %id%
		",
		// EVENTS
		'DeleteEvent' => "
			DELETE FROM events WHERE id = %id%
		",
		'ShowEvents' => "
			SELECT id, title, location, description, eventDate, eventTime
			FROM events
			ORDER BY eventDate
		",
		'ShowEvent' => "
			SELECT title, location, description, eventDate, eventTime
			FROM events
			WHERE id = %id%
		"
	);
?>