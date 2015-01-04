@extends(common)

@block(title)
Upload Markdown
@endblock

@block(content)
<form method=POST enctype=multipart/form-data>
<input type=file name=files[] multiple>
<input type=submit>
</form>
@endblock
