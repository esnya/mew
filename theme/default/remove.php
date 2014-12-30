@extends(view)

@block(title)
Remove {{title}}
@endblock

@block(content)
<p>Are you sure to remove?</p>
<form method=POST>
    <input type=hidden name=remove value=yes>
    <input type=submit value=Remove>
</form>
@endblock
