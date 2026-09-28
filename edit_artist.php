<?php

require_once 'db.php';

$artistId = (int)($_GET['id'] ?? $_POST['artist_id'] ?? 0);

if ($artistId <= 0) {
    die("Artist not found.");
}

$message = '';
$error = '';

try {

    $stmt = $pdo->prepare(
        "SELECT artist_id, name, country
         FROM artists
         WHERE artist_id = ?"
    );

    $stmt->execute([$artistId]);

    $artist = $stmt->fetch();

    if (!$artist) {
        die("Artist not found.");
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name'] ?? '');
        $country = trim($_POST['country'] ?? '');

        if ($name === '') {

            $error = "Artist name is required.";

        } else {

            $countryValue = $country === '' ? null : $country;

            $update = $pdo->prepare(
                "UPDATE artists
                 SET name = ?, country = ?
                 WHERE artist_id = ?"
            );

            $update->execute([
                $name,
                $countryValue,
                $artistId
            ]);

            if ($update->rowCount() > 0) {

                $message = "Artist updated successfully.";

            } else {

                $message = "No changes were made.";

            }

            $artist['name'] = $name;
            $artist['country'] = $countryValue;
        }
    }

} catch (PDOException $e) {

    $error = "Something went wrong while updating the artist.";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Edit Artist - Wavelength</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-3xl mx-auto p-6">

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
           class="text-blue-600 hover:underline">
            Playlists
        </a>

    </nav>

    <div class="bg-white border rounded-lg p-5">

        <h2 class="text-xl font-semibold mb-4">
            Edit Artist
        </h2>

        <?php if ($message !== ''): ?>

            <div class="bg-green-50 border border-green-300
                        text-green-700 p-3 rounded mb-4">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>
        <?php if ($error !== ''): ?>

            <div class="bg-red-50 border border-red-300
                        text-red-700 p-3 rounded mb-4">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <input
                type="hidden"
                name="artist_id"
                value="<?= (int)$artist['artist_id'] ?>"
            >



            <div class="mb-4">

                <label class="block font-medium mb-1">
                    Artist Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars($artist['name']) ?>"
                    class="border rounded px-3 py-2 w-full"
                    required
                >

            </div>

            <div class="mb-5">

                <label class="block font-medium mb-1">
                    Country
                </label>

                <input
                    type="text"
                    name="country"
                    value="<?= htmlspecialchars($artist['country'] ?? '') ?>"
                    class="border rounded px-3 py-2 w-full"
                    placeholder="Example: Nigeria"
                >

            </div>


            <button
                type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded"
            >
                Update Artist
            </button>

            <a
                href="artists.php"
                class="border px-5 py-2 rounded ml-2"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>