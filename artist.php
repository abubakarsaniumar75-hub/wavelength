<?php

require_once 'db.php';

$artistId = (int)($_GET['id'] ?? 0);

if ($artistId <= 0) {
    die("Artist not found.");
}

try {

    // Get artist information
    $artistStmt = $pdo->prepare(
        "SELECT artist_id, name, country
         FROM artists
         WHERE artist_id = ?"
    );

    $artistStmt->execute([$artistId]);

    $artist = $artistStmt->fetch();

    if (!$artist) {
        die("Artist not found.");
    }


    // Get albums and number of tracks
    $albumStmt = $pdo->prepare(
        "SELECT
            albums.album_id,
            albums.title,
            albums.release_year,
            albums.genre,
            COUNT(tracks.track_id) AS track_count
         FROM albums
         INNER JOIN artists
            ON albums.artist_id = artists.artist_id
         LEFT JOIN tracks
            ON albums.album_id = tracks.album_id
         WHERE artists.artist_id = ?
         GROUP BY
            albums.album_id,
            albums.title,
            albums.release_year,
            albums.genre
         HAVING COUNT(tracks.track_id) >= 1
         ORDER BY albums.release_year DESC"
    );

    $albumStmt->execute([$artistId]);

    $albums = $albumStmt->fetchAll();


    // Get total streams
    $streamStmt = $pdo->prepare(
        "SELECT COALESCE(SUM(tracks.stream_count), 0)
         FROM tracks
         INNER JOIN albums
            ON tracks.album_id = albums.album_id
         WHERE albums.artist_id = ?"
    );

    $streamStmt->execute([$artistId]);

    $totalStreams = $streamStmt->fetchColumn();

} catch (PDOException $e) {

    die("Something went wrong while loading the artist.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        <?= htmlspecialchars($artist['name']) ?> - Wavelength
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-5xl mx-auto p-6">

    <!-- Header -->
    <div class="bg-white border rounded-lg p-5 mb-5">

        <h1 class="text-2xl font-bold">
            Wavelength
        </h1>

        <p class="text-gray-500">
            Music Library Manager
        </p>

    </div>


    <!-- Navigation -->
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

    </nav>


    <!-- Artist Information -->
    <div class="bg-white border rounded-lg p-5 mb-5">

        <h2 class="text-xl font-semibold mb-3">
            <?= htmlspecialchars($artist['name']) ?>
        </h2>

        <p>
            <strong>Country:</strong>

            <?= $artist['country']
                ? htmlspecialchars($artist['country'])
                : 'Unknown'
            ?>
        </p>

        <p class="mt-2">
            <strong>Total Streams:</strong>
            <?= htmlspecialchars($totalStreams) ?>
        </p>

    </div>


    <!-- Albums -->
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Albums
        </h2>

        <?php if (count($albums) > 0): ?>

            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="bg-gray-100">

                            <th class="border p-2 text-left">
                                Album
                            </th>

                            <th class="border p-2 text-left">
                                Year
                            </th>

                            <th class="border p-2 text-left">
                                Genre
                            </th>

                            <th class="border p-2 text-left">
                                Tracks
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($albums as $album): ?>

                        <tr>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['title']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['release_year']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['genre']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['track_count']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p>No albums with tracks found for this artist.</p>

        <?php endif; ?>

    </div>


    <p class="mt-5">

        <a href="artists.php"
           class="text-blue-600 hover:underline">
            ← Back to Artists
        </a>

    </p>

</div>

</body>
</html>