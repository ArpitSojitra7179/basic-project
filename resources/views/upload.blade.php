<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
</head>
<body>

<h2>Upload Image</h2>
<form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image">
    <button type="submit">Upload Image</button>
</form>

<hr>

<h2>Upload Document</h2>
<form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="document">
    <button type="submit">Upload File</button>
</form>

</body>
</html>
