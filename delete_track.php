<?php

require_once 'db.php';

$trackId = (int)($_GET['id'] ?? $_POST['track_id'] ?? 0);

if ($trackId <= 0) {
    die("Track not found.");
}

try {

    $stmt = $pdo->prepare(
        "SELECT track_id, title
         FROM tracks
         WHERE track_id = ?"
    );

    $stmt->execute([$trackId]);

    $track = $stmt->fetch();

    if (!$track) {
        die("Track not found.");
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $delete = $pdo->prepare(
            "DELETE FROM tracks
             WHERE track_id = ?"
        );

        $delete->execute([$trackId]);

        if ($delete->rowCount() > 0) {

            header("Location: index.php");
            exit;

        } else {

            die("Track could not be deleted.");

        }
    }

} catch (PDOException $e) {

    die("Something went wrong while deleting the track.");

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Delete Track - Wavelength</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-2xl mx-auto p-6">

    <div class="bg-white border rounded-lg p-5">

        <h1 class="text-xl font-bold mb-3">
            Delete Track
        </h1>

        <p class="mb-5">
            Are you sure you want to delete
            <strong>
                <?= htmlspecialchars($track['title']) ?>
            </strong>?
        </p>

        <form method="POST">

            <input
                type="hidden"
                name="track_id"
                value="<?= (int)$track['track_id'] ?>"
            >

            <button
                type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded"
            >
                Yes, Delete
            </button>

            <a
                href="index.php"
                class="border px-4 py-2 rounded ml-2"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

</body>

</html>