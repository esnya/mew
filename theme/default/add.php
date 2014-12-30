@extends(view)

@block(title)
Add new page
@endblock

@block(content)
<form method=POST>
    <label for=form-name>Page name</label>
    <input type=text name=name id=form-name>
    <input type=submit value=Add>
</form>
@endblock
