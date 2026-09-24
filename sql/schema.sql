-- =========================================================
-- WAVELENGTH - MUSIC LIBRARY MANAGER
-- Database: music_streaming
-- =========================================================


-- =========================================================
-- 1. CREATE DATABASE
-- =========================================================

CREATE DATABASE IF NOT EXISTS music_streaming;

USE music_streaming;


-- =========================================================
-- 2. DROP TABLES
-- =========================================================
-- Drop child tables first because of foreign keys.

DROP TABLE IF EXISTS playlist_tracks;
DROP TABLE IF EXISTS playlists;
DROP TABLE IF EXISTS tracks;
DROP TABLE IF EXISTS albums;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS artists;


-- =========================================================
-- 3. ARTISTS TABLE
-- =========================================================

CREATE TABLE artists (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(50)
);


-- =========================================================
-- 4. ALBUMS TABLE
-- =========================================================

CREATE TABLE albums (
    album_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    release_year YEAR,
    genre VARCHAR(50),

    CONSTRAINT fk_albums_artist
        FOREIGN KEY (artist_id)
        REFERENCES artists(artist_id)
);


-- =========================================================
-- 5. TRACKS TABLE
-- =========================================================

CREATE TABLE tracks (
    track_id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    duration_seconds INT,
    stream_count INT DEFAULT 0,

    CONSTRAINT fk_tracks_album
        FOREIGN KEY (album_id)
        REFERENCES albums(album_id)
);


-- =========================================================
-- 6. USERS TABLE
-- =========================================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    join_date DATE
);


-- =========================================================
-- 7. PLAYLISTS TABLE
-- =========================================================

CREATE TABLE playlists (
    playlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    playlist_name VARCHAR(100) NOT NULL,

    CONSTRAINT fk_playlists_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);


-- =========================================================
-- 8. PLAYLIST_TRACKS TABLE
-- =========================================================
-- Junction table between playlists and tracks.

CREATE TABLE playlist_tracks (
    playlist_id INT NOT NULL,
    track_id INT NOT NULL,
    added_date DATE,

    PRIMARY KEY (playlist_id, track_id),

    CONSTRAINT fk_playlist_tracks_playlist
        FOREIGN KEY (playlist_id)
        REFERENCES playlists(playlist_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_playlist_tracks_track
        FOREIGN KEY (track_id)
        REFERENCES tracks(track_id)
        ON DELETE CASCADE
);


-- =========================================================
-- 9. SAMPLE ARTISTS
-- =========================================================

INSERT INTO artists (artist_id, name, country) VALUES
(1, 'Davido', 'Nigeria'),
(2, 'Wizkid', 'Nigeria'),
(3, 'Rema', 'Nigeria'),
(4, 'Omah Lay', 'Nigeria'),
(5, 'Taylor Swift', 'USA');


-- =========================================================
-- 10. SAMPLE ALBUMS
-- =========================================================

INSERT INTO albums
    (album_id, artist_id, title, release_year, genre)
VALUES
(1, 1, 'Timeless', 2023, 'Afrobeats'),
(2, 1, 'A Better Time', 2020, 'Afrobeats'),
(3, 2, 'Made in Lagos', 2020, 'Afrobeats'),
(4, 2, 'Sounds From the Other Side', 2017, 'Pop'),
(5, 3, 'Rave & Roses', 2022, 'Afrobeats'),
(6, 3, 'HEIS', 2024, 'Afrobeats'),
(7, 4, 'Boy Alone', 2022, 'Afrobeats'),
(8, 4, 'What Have We Done', 2020, 'Afrobeats');


-- =========================================================
-- 11. SAMPLE TRACKS
-- =========================================================

INSERT INTO tracks
    (track_id, album_id, title, duration_seconds, stream_count)
VALUES
(1, 1, 'Over Dem', 195, 1200),
(2, 1, 'Unavailable', 220, 2500),
(3, 1, 'Feel', 210, 1800),
(4, 1, 'Champion Sound', 240, 900),

(5, 2, 'FEM', 210, 2200),
(6, 2, 'The Best', 230, 1500),
(7, 2, 'Jowo', 205, 1900),
(8, 2, 'Risky', 215, 1700),

(9, 3, 'Essence', 245, 3000),
(10, 3, 'Ginger', 230, 2800),
(11, 3, 'Blessed', 200, 1200),
(12, 3, 'Reckless', 190, 1000),

(13, 4, 'Come Closer', 210, 2600),
(14, 4, 'Daddy Yo', 215, 1800),
(15, 4, 'Sweet One', 200, 900),
(16, 4, 'Naughty Ride', 225, 800),

(17, 5, 'Calm Down', 239, 3500),
(18, 5, 'Time N Affection', 210, 1400),
(19, 5, 'Addicted', 205, 1000),
(20, 5, 'Hold Me', 220, 1100),

(21, 6, 'HEIS', 200, 2000),
(22, 6, 'Ozeba', 190, 1700),
(23, 6, 'Benin Boys', 230, 2500),

(24, 7, 'Soso', 220, 2700),
(25, 7, 'Understand', 210, 2300),
(26, 7, 'Attention', 200, 1500),
(27, 7, 'Woman', 215, 1900),

(28, 8, 'Bad Influence', 210, 2900),
(29, 8, 'Damn', 205, 1800),
(30, 8, 'You', 200, 1200);


-- =========================================================
-- 12. SAMPLE USERS
-- =========================================================

INSERT INTO users
    (user_id, username, email, join_date)
VALUES
(1, 'Alice', 'alice@beats.com', '2026-01-10'),
(2, 'Bob', 'bob@beats.com', '2026-01-15'),
(3, 'Charlie', 'charlie@beats.com', '2026-02-01'),
(4, 'David', 'david@beats.com', '2026-02-10'),
(5, 'Sarah', 'sarah@beats.com', '2026-02-20');


-- =========================================================
-- 13. SAMPLE PLAYLISTS
-- =========================================================

INSERT INTO playlists
    (playlist_id, user_id, playlist_name)
VALUES
(1, 1, 'Afrobeats Hits'),
(2, 1, 'Morning Vibes'),
(3, 2, 'Workout Music'),
(4, 2, 'Chill Songs'),
(5, 3, 'My Favorites'),
(6, 4, 'Party Playlist'),
(7, 4, 'Late Night'),
(8, 5, 'Weekend Vibes');


-- =========================================================
-- 14. SAMPLE PLAYLIST TRACKS
-- =========================================================

INSERT INTO playlist_tracks
    (playlist_id, track_id, added_date)
VALUES

-- Afrobeats Hits
(1, 1, '2026-03-01'),
(1, 2, '2026-03-01'),
(1, 9, '2026-03-02'),
(1, 17, '2026-03-02'),
(1, 24, '2026-03-03'),

-- Morning Vibes
(2, 3, '2026-03-04'),
(2, 10, '2026-03-04'),
(2, 18, '2026-03-05'),
(2, 25, '2026-03-05'),

-- Workout Music
(3, 5, '2026-03-06'),
(3, 7, '2026-03-06'),
(3, 13, '2026-03-07'),
(3, 21, '2026-03-07'),

-- Chill Songs
(4, 9, '2026-03-08'),
(4, 11, '2026-03-08'),
(4, 24, '2026-03-09'),
(4, 27, '2026-03-09'),

-- My Favorites
(5, 2, '2026-03-10'),
(5, 17, '2026-03-10'),
(5, 28, '2026-03-11'),

-- Party Playlist
(6, 6, '2026-03-12'),
(6, 14, '2026-03-12'),
(6, 17, '2026-03-13'),
(6, 23, '2026-03-13'),

-- Late Night
(7, 4, '2026-03-14'),
(7, 12, '2026-03-14'),
(7, 26, '2026-03-15'),

-- Weekend Vibes
(8, 8, '2026-03-16'),
(8, 15, '2026-03-16'),
(8, 29, '2026-03-17');


-- =========================================================
-- END OF SCHEMA
-- =========================================================