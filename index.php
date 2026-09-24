<?php

require_once 'db.php';

try {

    $artistCount = $pdo->query(
        "SELECT COUNT(*) FROM artists"
    )->fetchColumn();

    $albumCount = $pdo->query(
        "SELECT COUNT(*) FROM albums"
    )->fetchColumn();

    $trackCount = $pdo->query(
        "SELECT COUNT(*) FROM tracks"
    )->fetchColumn();

    $streamCount = $pdo->query(
        "SELECT COALESCE(SUM(stream_count), 0) FROM tracks"
    )->fetchColumn();

    $averageDuration = $pdo->query(
        "SELECT AVG(duration_seconds) FROM tracks"
    )->fetchColumn();

    $longestTrack = $pdo->query(
        "SELECT title, duration_seconds
         FROM tracks
         ORDER BY duration_seconds DESC
         LIMIT 1"
    )->fetch();

    $shortestTrack = $pdo->query(
        "SELECT title, duration_seconds
         FROM tracks
         ORDER BY duration_seconds ASC
         LIMIT 1"
    )->fetch();

    $latestAlbum = $pdo->query(
        "SELECT albums.title, albums.release_year,
                artists.name AS artist_name
         FROM albums
         INNER JOIN artists
         ON albums.artist_id = artists.artist_id
         ORDER BY albums.release_year DESC
         LIMIT 1"
    )->fetch();

} catch (PDOException $e) {
    die("Something went wrong while loading the dashboard.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Wavelength - Dashboard</title>

    <!-- Small amount of Tailwind styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="max-w-5xl mx-auto p-6">

        <!-- Header -->
        <div class="bg-white border rounded-lg p-5 mb-5">
            <h1 class="text-2xl font-bold">Wavelength</h1>
            <p class="text-gray-500">Music Library Manager</p>
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

            <a href="add_track.php"
               class="text-blue-600 hover:underline">
                Add Track
            </a>

        </nav>

        <!-- Dashboard -->
        <div class="bg-white border rounded-lg p-5">

            <h2 class="text-xl font-semibold mb-4">
                Dashboard
            </h2>

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">

                <div class="border rounded p-4">
                    <p class="text-sm text-gray-500">Artists</p>
                    <p class="text-xl font-bold">
                        <?= htmlspecialchars($artistCount) ?>
                    </p>
                </div>

                <div class="border rounded p-4">
                    <p class="text-sm text-gray-500">Albums</p>
                    <p class="text-xl font-bold">
                        <?= htmlspecialchars($albumCount) ?>
                    </p>
                </div>

                <div class="border rounded p-4">
                    <p class="text-sm text-gray-500">Tracks</p>
                    <p class="text-xl font-bold">
                        <?= htmlspecialchars($trackCount) ?>
                    </p>
                </div>

                <div class="border rounded p-4">
                    <p class="text-sm text-gray-500">Streams</p>
                    <p class="text-xl font-bold">
                        <?= htmlspecialchars($streamCount) ?>
                    </p>
                </div>

            </div>

            <!-- Other information -->
            <h3 class="font-semibold mb-2">
                Library Information
            </h3>

            <div class="space-y-2">

                <p>
                    <strong>Average Track Duration:</strong>
                    <?= htmlspecialchars(number_format((float)$averageDuration, 2)) ?>
                    seconds
                </p>

                <p>
                    <strong>Longest Track:</strong>

                    <?php if ($longestTrack): ?>

                        <?= htmlspecialchars($longestTrack['title']) ?>
                        -
                        <?= htmlspecialchars($longestTrack['duration_seconds']) ?>
                        seconds

                    <?php else: ?>

                        No tracks available.

                    <?php endif; ?>
                </p>

                <p>
                    <strong>Shortest Track:</strong>

                    <?php if ($shortestTrack): ?>

                        <?= htmlspecialchars($shortestTrack['title']) ?>
                        -
                        <?= htmlspecialchars($shortestTrack['duration_seconds']) ?>
                        seconds

                    <?php else: ?>

                        No tracks available.

                    <?php endif; ?>
                </p>

                <p>
                    <strong>Latest Album:</strong>

                    <?php if ($latestAlbum): ?>

                        <?= htmlspecialchars($latestAlbum['title']) ?>
                        by
                        <?= htmlspecialchars($latestAlbum['artist_name']) ?>
                        (<?= htmlspecialchars($latestAlbum['release_year']) ?>)

                    <?php else: ?>

                        No albums available.

                    <?php endif; ?>
                </p>

            </div>

        </div>

    </div>

</body>
</html>