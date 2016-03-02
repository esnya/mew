<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{{title}}</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
        <link rel="shortcut icon" type="image/svg+xml" href="mew.svg">
        <link rel="icon" type="image/svg+xml" href="mew.svg">
    </head>
    <body>
        <header>
            <h1>
                <a href="?"><img src="mew.svg"></a>
                <a href="{{headerlink}}">
                    {{title}}
                </a>
            </h1>
            <nav>
                <ul>
                    <li><a href="?">top</a></li>
                    {{actions}}
                    <li><a href="?a=add">add</a></li>
                    <li><a href="?c=markdown&a=zip">zip</a></li>
                    <li><a href="?c=markdown&a=upload">upload</a></li>
                </ul>
            </nav>
        </header>
        <div id=container>
            <nav>{{sidebar}}</nav>
            <main>
                <div>{{content}}</div>
                <footer>{{footer}}</footer>
            </main>
        </div>
        {{script}}
    </body>
</html>
