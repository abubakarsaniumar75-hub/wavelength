<?php

require_once 'db.php';

$playlistId = (int)($_GET['id'] ?? 0);

if ($playlistId <= 0) {
    die("Playlist not found.");
}

try {


    $playlistStmt = $pdo->prepare(
        "SELECT
            playlists.playlist_id,
            playlists.playlist_name,
            users.username
         FROM playlists
         INNER JOIN users
            ON playlists.user_id = users.user_id
         WHERE playlists.playlist_id = ?"
    );

    $playlistStmt->execute([$playlistId]);

    $playlist = $playlistStmt->fetch();

    if (!$playlist) {
        die("Playlist not found.");
    }

    $trackStmt = $pdo->prepare(
        "SELECT
            tracks.track_id,
            tracks.title AS track_title,
            artists.name AS artist_name,
            albums.title AS album_title,
            tracks.duration_seconds,
            tracks.stream_count,
            playlist_tracks.added_date
         FROM playlist_tracks
         INNER JOIN tracks
            ON playlist_tracks.track_id = tracks.track_id
         INNER JOIN albums
            ON tracks.album_id = albums.album_id
         INNER JOIN artists
            ON albums.artist_id = artists.artist_id
         WHERE playlist_tracks.playlist_id = ?
         ORDER BY playlist_tracks.added_date ASC"
    );

    $trackStmt->execute([$playlistId]);

    $tracks = $trackStmt->fetchAll();


    // Calculate total duration
    $durationStmt = $pdo->prepare(
        "SELECT COALESCE(SUM(tracks.duration_seconds), 0)
         FROM playlist_tracks
         INNER JOIN tracks
            ON playlist_tracks.track_id = tracks.track_id
         WHERE playlist_tracks.playlist_id = ?"
    );

    $durationStmt->execute([$playlistId]);

    $totalSeconds = (int)$durationStmt->fetchColumn();

    $minutes = floor($totalSeconds / 60);
    $seconds = $totalSeconds % 60;

} catch (PDOException $e) {

    die("Something went wrong while loading the playlist.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        <?= htmlspecialchars($playlist['playlist_name']) ?> - Wavelength
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-5xl mx-auto p-6">

  
    <div class="bg-white border rounded-lg p-5 mb-5">

        <h1 class="text-2xl font-bold">
            Wavelength
        </h1>

        <p class="text-gray-500">
            Music Library Manager
        </p>

    </div>

    <nav class="bg-white border rounded-lg p-4 mb-5">

        <a href="index.php"
           class="mr-4 text-blue-600 hover:underline">
            Dashboard
        </a>

        <a href="artists.php"
           class="mr-4 text-blue-600 hover:underline">
            Artists
        </a>

        <a href="albums.php"
           class="mr-4 text-blue-600 hover:underline">
            Albums
        </a>

        <a href="playlists.php"
           class="mr-4 text-blue-600 hover:underline">
            Playlists
        </a>

        <a href="add_track.php"
           class="text-blue-600 hover:underline">
            Add Track
        </a>

    </nav>
    <div class="bg-white border rounded-lg p-5 mb-5">

        <h2 class="text-xl font-semibold">
            <?= htmlspecialchars($playlist['playlist_name']) ?>
        </h2>

        <p class="mt-2">
            <strong>Owner:</strong>
            <?= htmlspecialchars($playlist['username']) ?>
        </p>

        <p class="mt-2">
            <strong>Total Tracks:</strong>
            <?= count($tracks) ?>
        </p>

        <p class="mt-2">
            <strong>Total Duration:</strong>
            <?= $minutes ?> minutes
            <?= $seconds ?> seconds
        </p>

    </div>
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Tracks
        </h2>


        <?php if (count($tracks) > 0): ?>

            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="bg-gray-100">

                            <th class="border p-2 text-left">
                                Track
                            </th>

                            <th class="border p-2 text-left">
                                Artist
                            </th>

                            <th class="border p-2 text-left">
                                Album
                            </th>

                            <th class="border p-2 text-left">
                                Duration
                            </th>

                            <th class="border p-2 text-left">
                                Streams
                            </th>

                            <th class="border p-2 text-left">
                                Added
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($tracks as $track): ?>

                        <?php
                        $trackMinutes = floor(
                            $track['duration_seconds'] / 60
                        );

                        $trackSeconds =
                            $track['duration_seconds'] % 60;
                        ?>

                        <tr>

                            <td class="border p-2">
                                <?= htmlspecialchars($track['track_title']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($track['artist_name']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($track['album_title']) ?>
                            </td>

                            <td class="border p-2">
                                <?= $trackMinutes ?>:
                                <?= str_pad(
                                    $trackSeconds,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                ) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($track['stream_count']) ?>
                            </td>

                            <td class="border p-2">
                                <?= $track['added_date']
                                    ? htmlspecialchars($track['added_date'])
                                    : 'Unknown'
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-gray-500">
                This playlist has no tracks yet.
            </p>

        <?php endif; ?>

    </div>


    <!-- Back -->
    <p class="mt-5">

        <a
            href="playlists.php"
            class="text-blue-600 hover:underline"
        >
            ← Back to Playlists
        </a>

    </p>

</div>

</body>
</html>