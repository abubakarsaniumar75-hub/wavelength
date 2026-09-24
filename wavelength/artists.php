<?php
require_once 'db.php';

$search = trim($_GET['search'] ?? '');
$country = $_GET['country'] ?? '';

try {
    // Get countries for the dropdown
    $countryStmt = $pdo->query("
        SELECT DISTINCT country
        FROM artists
        WHERE country IS NOT NULL
        ORDER BY country ASC
    ");

    $countries = $countryStmt->fetchAll();

    // Build the artist query
    $sql = "
        SELECT artist_id, name, country
        FROM artists
        WHERE 1=1
    ";

    $params = [];

    // Search by artist name
    if ($search !== '') {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%";
    }

    // Filter by country
    if ($country !== '') {
        $sql .= " AND country IN (?)";
        $params[] = $country;
    }

    $sql .= " ORDER BY name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $artists = $stmt->fetchAll();

} catch (PDOException $e) {
    $artists = [];
    $error = "Unable to load artists right now.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Wavelength | Artists</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

<!-- Navigation -->
<nav class="border-b border-slate-800 bg-slate-950">

    <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">

        <a href="index.php"
           class="text-2xl font-bold text-purple-400">
            Wavelength
        </a>

        <div class="flex gap-6 text-sm">

            <a href="index.php"
               class="text-slate-300 hover:text-white">
                Dashboard
            </a>

            <a href="artists.php"
               class="text-purple-400 font-medium">
                Artists
            </a>

            <a href="albums.php"
               class="text-slate-300 hover:text-white">
                Albums
            </a>

            <a href="playlists.php"
               class="text-slate-300 hover:text-white">
                Playlists
            </a>

            <a href="add_track.php"
               class="text-slate-300 hover:text-white">
                Add Track
            </a>

        </div>

    </div>

</nav>


<!-- Main -->
<main class="max-w-7xl mx-auto px-6 py-12">

    <div class="mb-10">

        <p class="text-purple-400 font-medium mb-2">
            MUSIC LIBRARY
        </p>

        <h1 class="text-4xl font-bold">
            Artists
        </h1>

        <p class="text-slate-400 mt-2">
            Browse and search artists in the Wavelength library.
        </p>

    </div>


    <!-- Search and Filter -->
    <form method="GET"
          action="artists.php"
          class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <!-- Search -->
            <div class="md:col-span-2">

                <label for="search"
                       class="block text-sm text-slate-400 mb-2">
                    Search artist
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search by artist name..."
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3
                           text-white outline-none focus:border-purple-500"
                >

            </div>


            <!-- Country -->
            <div>

                <label for="country"
                       class="block text-sm text-slate-400 mb-2">
                    Country
                </label>

                <select
                    id="country"
                    name="country"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-3
                           text-white outline-none focus:border-purple-500"
                >

                    <option value="">All countries</option>

                    <?php foreach ($countries as $item): ?>

                        <option
                            value="<?= htmlspecialchars($item['country']) ?>"
                            <?= $country === $item['country'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($item['country']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <div class="flex gap-3 mt-5">

            <button
                type="submit"
                class="bg-purple-600 hover:bg-purple-700 px-6 py-3 rounded-lg
                       font-medium transition"
            >
                Search
            </button>

            <a
                href="artists.php"
                class="bg-slate-800 hover:bg-slate-700 px-6 py-3 rounded-lg
                       font-medium transition"
            >
                Reset
            </a>

        </div>

    </form>


    <!-- Error -->
    <?php if (isset($error)): ?>

        <div class="bg-red-950 border border-red-800 text-red-300 rounded-xl p-4 mb-6">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- Artists Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-800">

                    <tr>

                        <th class="text-left px-6 py-4 text-sm text-slate-300">
                            ID
                        </th>

                        <th class="text-left px-6 py-4 text-sm text-slate-300">
                            Artist
                        </th>

                        <th class="text-left px-6 py-4 text-sm text-slate-300">
                            Country
                        </th>

                        <th class="text-right px-6 py-4 text-sm text-slate-300">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-800">

                    <?php if (count($artists) > 0): ?>

                        <?php foreach ($artists as $artist): ?>

                            <tr class="hover:bg-slate-800/50">

                                <td class="px-6 py-4 text-slate-400">
                                    <?= htmlspecialchars($artist['artist_id']) ?>
                                </td>

                                <td class="px-6 py-4 font-medium">
                                    <?= htmlspecialchars($artist['name']) ?>
                                </td>

                                <td class="px-6 py-4 text-slate-400">

                                    <?php if ($artist['country'] === null): ?>

                                        Unknown

                                    <?php else: ?>

                                        <?= htmlspecialchars($artist['country']) ?>

                                    <?php endif; ?>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-3">

                                        <a
                                            href="artist.php?id=<?= (int)$artist['artist_id'] ?>"
                                            class="text-purple-400 hover:text-purple-300"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="edit_artist.php?id=<?= (int)$artist['artist_id'] ?>"
                                            class="text-slate-300 hover:text-white"
                                        >
                                            Edit
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="4"
                                class="px-6 py-12 text-center text-slate-400">

                                No artists found.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

</body>
</html>