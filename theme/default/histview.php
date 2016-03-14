@extends(view)
@block(title)
History of "{{title}}" at "{{timestamp}}"
@endblock

@block(content)
<textarea readonly name="code">
{{data}}
</textarea>
@endblock