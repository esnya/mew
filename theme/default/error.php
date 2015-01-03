@extends(view)

@block(title)
500 Internal Server Error
@endblock

@block(content)
<p>{{msg}}</p>
<pre>{{stacktrace}}</pre>
@endblock
