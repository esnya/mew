<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{{title}}</title>
        <link rel=stylesheet href="https://cdn.rawgit.com/sindresorhus/github-markdown-css/gh-pages/github-markdown.css">
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
        <link rel="shortcut icon" type="image/png" href="mew64.png">
        <link rel="icon" type="image/png" href="mew64.png">
    </head>
    <body class="markdown-body">
        <header id="header">
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
            <nav id="sidebar">{{sidebar}}</nav>
            <main>
                <div id="content">{{content}}</div>
                <footer>{{footer}}</footer>
            </main>
        </div>
        {{script}}
    </body>
</html>
