@extends(view)
@block(title)
Edit "{{title}}"
@endblock

@block(content)
<form method="post">
    <div>
        <h1>Edit</h1>
        <textarea name=code>{{code}}</textarea>
    </div>
    <div>
        <input type="submit">
    </div>
</form>
<form action="?p={{page}}&a=upload" method=POST enctype=multipart/form-data>
    <h1>Upload File</h1>
    <input type=file name=file>
    <input type=submit>
</form>
<div>
    <h1>Preview</h1>
    <div id=preview>{{content}}</div>
</div>
@endblock

@block(script)
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>window.page = '{{page}}';</script>
<script src="script/edit.js"></script>
@endblock
