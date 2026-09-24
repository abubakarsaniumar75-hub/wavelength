<?php

require_once 'db.php';

$search = trim($_GET['search'] ?? '');

try {

    $sql = "
        SELECT
            playlists.playlist_id,
            playlists.playlist_name,
            users.username,
            COUNT(playlist_tracks.track_id) AS track_count
        FROM playlists
        INNER JOIN users
            ON playlists.user_id = users.user_id
        LEFT JOIN playlist_tracks
            ON playlists.playlist_id = playlist_tracks.playlist_id
    ";

    $params = [];

    if ($search !== '') {
        $sql .= " WHERE playlists.playlist_name LIKE ?";
        $params[] = "%$search%";
    }

    $sql .= "
        GROUP BY
            playlists.playlist_id,
            playlists.playlist_name,
            users.username
        ORDER BY playlists.playlist_name ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $playlists = $stmt->fetchAll();

} catch (PDOException $e) {

    die("Something went wrong while loading the playlists.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Wavelength - Playlists</title>

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

        <a href="add_track.php"
           class="text-blue-600 hover:underline">
            Add Track
        </a>

    </nav>


    <!-- Playlist section -->
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Playlists
        </h2>


        <!-- Search -->
        <form method="GET" class="mb-5">

            <div class="flex gap-2">

                <input
                    type="text"
                    name="search"
                    placeholder="Search playlist..."
                    value="<?= htmlspecialchars($search) ?>"
                    class="border rounded px-3 py-2"
                >

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Search
                </button>

                <a
                    href="playlists.php"
                    class="border px-4 py-2 rounded"
                >
                    Reset
                </a>

            </div>

        </form>


        <!-- Playlist table -->

        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                <thead>

                    <tr class="bg-gray-100">

                        <th class="border p-2 text-left">
                            Playlist
                        </th>

                        <th class="border p-2 text-left">
                            Owner
                        </th>

                        <th class="border p-2 text-left">
                            Tracks
                        </th>

                        <th class="border p-2 text-left">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($playlists) > 0): ?>

                    <?php foreach ($playlists as $playlist): ?>

                        <tr>

                            <td class="border p-2">
                                <?= htmlspecialchars($playlist['playlist_name']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($playlist['username']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($playlist['track_count']) ?>
                            </td>

                            <td class="border p-2">

                                <a
                                    href="playlist.php?id=<?= (int)$playlist['playlist_id'] ?>"
                                    class="text-blue-600 hover:underline"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="4"
                            class="border p-4 text-center"
                        >
                            No playlists found.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>