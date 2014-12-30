@extends(view)

@block(title)
404 Not Found
@endblock

@block(content)
<p>A page "{{page}}" doesn't exists.</p>
<form action="?a=add" method=POST>
    <input type=hidden name=name value="{{page}}">
    <p><input type=submit value='Add "{{page}}"'></p>
</form>
@endblock
