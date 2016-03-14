@extends(common)

@block(headerlink)
?p={{page}}
@endblock

@block(actions)
<li><a href="?p={{page}}&a=edit">edit</a></li>
<li><a href="?p={{page}}&a=history">history</a></li>
<li><a href="?p={{page}}&a=remove">remove</a></li>
<li><a href="?p={{page}}&c=markdown">markdown</a></li>
@endblock

@block(footer)
<ul class=file-list>
    @foreach(file in files)
    <li><a href="{{file.url}}">{{file.name}}</a> ({{file.type}}, {{file.size}}bytes)</li>
    @endforeach
</ul>
@endblock
