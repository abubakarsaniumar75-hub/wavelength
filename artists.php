<?php

require_once 'db.php';

$search = trim($_GET['search'] ?? '');
$country = $_GET['country'] ?? '';

try {

    // Get countries for the dropdown
    $countryStmt = $pdo->query(
        "SELECT DISTINCT country
         FROM artists
         WHERE country IS NOT NULL
         ORDER BY country ASC"
    );

    $countries = $countryStmt->fetchAll();

    // Build artists query
    $sql = "SELECT artist_id, name, country
            FROM artists
            WHERE 1=1";

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

    die("Something went wrong while loading the artists.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Wavelength - Artists</title>

    <!-- Small amount of Tailwind -->
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


    <!-- Artists -->
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Artists
        </h2>


        <!-- Search and Filter -->

        <form method="GET" class="mb-5">

            <div class="flex flex-wrap gap-2">

                <input
                    type="text"
                    name="search"
                    placeholder="Search artist..."
                    value="<?= htmlspecialchars($search) ?>"
                    class="border rounded px-3 py-2"
                >

                <select
                    name="country"
                    class="border rounded px-3 py-2"
                >

                    <option value="">
                        All Countries
                    </option>

                    <?php foreach ($countries as $row): ?>

                        <option
                            value="<?= htmlspecialchars($row['country']) ?>"
                            <?= $country === $row['country'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($row['country']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                >
                    Search
                </button>

                <a
                    href="artists.php"
                    class="border px-4 py-2 rounded"
                >
                    Reset
                </a>

            </div>

        </form>


        <!-- Artists Table -->

        <div class="overflow-x-auto">

            <table class="w-full border-collapse">

                <thead>

                    <tr class="bg-gray-100">

                        <th class="border p-2 text-left">
                            ID
                        </th>

                        <th class="border p-2 text-left">
                            Name
                        </th>

                        <th class="border p-2 text-left">
                            Country
                        </th>

                        <th class="border p-2 text-left">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if (count($artists) > 0): ?>

                    <?php foreach ($artists as $artist): ?>

                        <tr>

                            <td class="border p-2">
                                <?= htmlspecialchars($artist['artist_id']) ?>
                            </td>

                            <td class="border p-2">
                                <?= htmlspecialchars($artist['name']) ?>
                            </td>

                            <td class="border p-2">

                                <?php
                                if ($artist['country'] !== null) {
                                    echo htmlspecialchars($artist['country']);
                                } else {
                                    echo "Unknown";
                                }
                                ?>

                            </td>

                            <td class="border p-2">

                                <a
                                    href="artist.php?id=<?= (int)$artist['artist_id'] ?>"
                                    class="text-blue-600 hover:underline mr-3"
                                >
                                    View
                                </a>

                                <a
                                    href="edit_artist.php?id=<?= (int)$artist['artist_id'] ?>"
                                    class="text-green-600 hover:underline"
                                >
                                    Edit
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
                            No artists found.
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