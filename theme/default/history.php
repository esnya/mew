@extends(view)
@block(title)
History of "{{title}}"
@endblock

@block(content)
<ol>
@foreach(index in history)
<li><a href="?p={{page}}&a=histview&id={{index.id}}">{{index.name}} - {{index.timestamp}}</a></li>
@endforeach
</ol>
@endblock