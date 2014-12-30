@extends(view)

@block(title)
404 Not Found
@endblock

@block(content)
<form method=POST>
    <p>A page "{{page}}" doesn't exists.</p>
    <p>Add and edit "<a href="?p={{page}}&a=edit">{{page}}</a>" ?</p>
</form>
@endblock
