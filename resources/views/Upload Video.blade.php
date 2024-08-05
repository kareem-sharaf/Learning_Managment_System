
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
</head>
<body>
    <form action="{{ route('upload.video') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div>
            <label for="tags">Tags (optional, separate by comma)</label>
            <input type="text" id="tags" name="tags">
        </div>
        <div>
            <label for="video">Video File</label>
            <input type="file" id="video" name="video" required>
        </div>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
