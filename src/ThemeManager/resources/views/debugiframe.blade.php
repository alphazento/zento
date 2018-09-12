<!DOCTYPE html>
<html lang="en">
<?php
    $config = session()->get('s3theme-config');
?>
<style>
.form-card {
    font-size:18px;
    font-weight:bold;
}

.form-card>input, .form-card>select {
    font-size:18px;
    border:2px solid green;
}

.form-card>button {
    font-weight:bold;
    font-size:18px;
    background-color:yellow;
}
</style>

<head>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>

<body>
    <p>
    <strong>AWS S3 Resource Setting</strong></p>
    <form method='post' class="form-card">
        {{ csrf_field() }}

        <label> Bucket Name :</label>
        <input type="text" name="bucket" value="{{ $config['bucket'] }}"><br><br>

        <label>Theme Folder:</label>
        <input type="text"  name="theme" value="{{ $config['theme'] }}"><br><br>

        <label>Block Hint:</label>
        <select name="block-debug-hint">
            <option value=0 >Disable</option>
            <option value=1 {{ Session::get('block-debug-hint') ? 'selected' : '' }}>Enable</option>
        </select><br><br>
        <button type="submit">Apply</button>
    </form>
</body>
</html>