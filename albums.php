<?php

require_once 'db.php';

$minYear = $_GET['min_year'] ?? '';
$maxYear = $_GET['max_year'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$allowedSorts = [
    'title',
    'release_year',
    'genre'
];

if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'title';
}

try {

    // Get available genres
    $genreStmt = $pdo->query(
        "SELECT DISTINCT genre
         FROM albums
         WHERE genre IS NOT NULL
         ORDER BY genre ASC"
    );

    $genres = $genreStmt->fetchAll();

    // Main albums query
    $sql = "
        SELECT
            albums.album_id,
            albums.title,
            albums.release_year,
            albums.genre,
            artists.name AS artist_name
        FROM albums
        INNER JOIN artists
            ON albums.artist_id = artists.artist_id
        WHERE 1=1
    ";

    $params = [];

    // Year range
    if ($minYear !== '' && is_numeric($minYear)) {
        $sql .= " AND albums.release_year >= ?";
        $params[] = (int)$minYear;
    }

    if ($maxYear !== '' && is_numeric($maxYear)) {
        $sql .= " AND albums.release_year <= ?";
        $params[] = (int)$maxYear;
    }

    // Genre filter
    if ($genre !== '') {
        $sql .= " AND albums.genre IN (?)";
        $params[] = $genre;
    }

    // Sorting
    $sql .= " ORDER BY albums.$sort ASC LIMIT 50";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $albums = $stmt->fetchAll();

} catch (PDOException $e) {

    die("Something went wrong while loading the albums.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Wavelength - Albums</title>

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


    <!-- Albums -->
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Albums
        </h2>


        <!-- Filters -->
        <form method="GET" class="mb-5">

            <div class="flex flex-wrap gap-2">

                <input
                    type="number"
                    name="min_year"
                    placeholder="From year"
                    value="<?= htmlspecialchars($minYear) ?>"
                    class="border rounded px-3 py-2 w-32"
                >

                <input
                    type="number"
                    name="max_year"
                    placeholder="To year"
                    value="<?= htmlspecialchars($maxYear) ?>"
                    class="border rounded px-3 py-2 w-32"
                >

                <select
                    name="genre"
                    class="border rounded px-3 py-2"
                >

                    <option value="">
                        All Genres
                    </option>

                    <?php foreach ($genres as $row): ?>

                        <option
                            value="<?= htmlspecialchars($row['genre']) ?>"
                            <?= $genre === $row['genre'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($row['genre']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>


                <select
                    name="sort"
                    class="border rounded px-3 py-2"
                >

                    <option
                        value="title"
                        <?= $sort === 'title' ? 'selected' : '' ?>
                    >
                        Title
                    </option>

                    <option
                        value="release_year"
                        <?= $sort === 'release_year' ? 'selected' : '' ?>
                    >
                        Release Year
                    </option>

                    <option
                        value="genre"
                        <?= $sort === 'genre' ? 'selected' : '' ?>
                    >
                        Genre
                    </option>

                </select>


                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Filter
                </button>


                <a
                    href="albums.php"
                    class="border px-4 py-2 rounded"
                >
                    Reset
                </a>

            </div>

        </form>


        <!-- Albums Table -->

        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                <thead>

                    <tr class="bg-gray-100">

                        <th class="border p-2 text-left">
                            Album
                        </th>

                        <th class="border p-2 text-left">
                            Artist
                        </th>

                        <th class="border p-2 text-left">
                            Year
                        </th>

                        <th class="border p-2 text-left">
                            Genre
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($albums) > 0): ?>

                    <?php foreach ($albums as $album): ?>

                        <tr>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['title']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['artist_name']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['release_year']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($album['genre']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="4"
                            class="border p-4 text-center"
                        >
                            No albums found.
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