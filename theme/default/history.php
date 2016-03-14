@extends(view)
@block(title)
History of "{{title}}"
@endblock

@block(content)
@foreach(index in history)
<li><a href="?p={{page}}&a=histview&id={{index.id}}">{{index.timestamp}}</a></li>
@endforeach
@endblock