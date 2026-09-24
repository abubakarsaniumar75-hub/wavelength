<?php

require_once 'db.php';

$message = '';
$error = '';

try {

    // Get all albums for the dropdown
    $albumStmt = $pdo->query(
        "SELECT
            albums.album_id,
            albums.title AS album_title,
            artists.name AS artist_name
         FROM albums
         INNER JOIN artists
            ON albums.artist_id = artists.artist_id
         ORDER BY albums.title ASC"
    );

    $albums = $albumStmt->fetchAll();


    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = trim($_POST['title'] ?? '');
        $duration = $_POST['duration_seconds'] ?? '';
        $streamCount = $_POST['stream_count'] ?? 0;
        $albumId = (int)($_POST['album_id'] ?? 0);


        // Validate title
        if ($title === '') {
            $error = "Track title is required.";
        }

        // Validate duration
        elseif (
            !filter_var(
                $duration,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            )
        ) {
            $error = "Duration must be a positive number.";
        }

        // Validate album
        elseif ($albumId <= 0) {
            $error = "Please select an album.";
        }

        else {

            // Check that the album exists
            $checkAlbum = $pdo->prepare(
                "SELECT album_id
                 FROM albums
                 WHERE album_id = ?"
            );

            $checkAlbum->execute([$albumId]);

            if (!$checkAlbum->fetch()) {

                $error = "Selected album does not exist.";

            } else {

                // Validate streams
                $streamCount = filter_var(
                    $streamCount,
                    FILTER_VALIDATE_INT
                );

                if ($streamCount === false || $streamCount < 0) {
                    $error = "Stream count must be 0 or greater.";
                } else {

                    // Insert track
                    $insert = $pdo->prepare(
                        "INSERT INTO tracks
                        (title, duration_seconds, stream_count, album_id)
                        VALUES (?, ?, ?, ?)"
                    );

                    $insert->execute([
                        $title,
                        (int)$duration,
                        $streamCount,
                        $albumId
                    ]);

                    $newTrackId = $pdo->lastInsertId();

                    $message =
                        "Track added successfully. Track ID: "
                        . $newTrackId;

                    // Clear form values
                    $title = '';
                    $duration = '';
                    $streamCount = 0;
                    $albumId = 0;
                }
            }
        }
    }

} catch (PDOException $e) {

    $error = "Something went wrong while adding the track.";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Add Track - Wavelength</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-3xl mx-auto p-6">

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


    <!-- Form -->
    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Add New Track
        </h2>


        <!-- Success message -->

        <?php if ($message !== ''): ?>

            <div class="border border-green-300 bg-green-50
                        text-green-700 p-3 rounded mb-4">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <!-- Error message -->

        <?php if ($error !== ''): ?>

            <div class="border border-red-300 bg-red-50
                        text-red-700 p-3 rounded mb-4">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- Track title -->

            <div class="mb-4">

                <label class="block font-medium mb-1">
                    Track Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="<?= htmlspecialchars($title ?? '') ?>"
                    class="border rounded px-3 py-2 w-full"
                    placeholder="Enter track title"
                    required
                >

            </div>


            <!-- Duration -->

            <div class="mb-4">

                <label class="block font-medium mb-1">
                    Duration (seconds)
                </label>

                <input
                    type="number"
                    name="duration_seconds"
                    value="<?= htmlspecialchars($duration ?? '') ?>"
                    min="1"
                    class="border rounded px-3 py-2 w-full"
                    placeholder="Example: 210"
                    required
                >

            </div>


            <!-- Stream count -->

            <div class="mb-4">

                <label class="block font-medium mb-1">
                    Stream Count
                </label>

                <input
                    type="number"
                    name="stream_count"
                    value="<?= htmlspecialchars($streamCount ?? 0) ?>"
                    min="0"
                    class="border rounded px-3 py-2 w-full"
                >

            </div>


            <!-- Album -->

            <div class="mb-5">

                <label class="block font-medium mb-1">
                    Album
                </label>

                <select
                    name="album_id"
                    class="border rounded px-3 py-2 w-full"
                    required
                >

                    <option value="">
                        Select an album
                    </option>

                    <?php foreach ($albums as $album): ?>

                        <option
                            value="<?= (int)$album['album_id'] ?>"
                            <?= (isset($albumId) &&
                                $albumId == $album['album_id'])
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars($album['album_title']) ?>
                            -
                            <?= htmlspecialchars($album['artist_name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- Submit -->

            <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded"
            >
                Add Track
            </button>

            <a
                href="index.php"
                class="border px-5 py-2 rounded ml-2"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>