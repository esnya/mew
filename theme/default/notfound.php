<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>404 Not Found</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
    </head>
    <body>
        <header>
            <h1>404 Not Found</h1>
            <nav>
                <ul>
                    <li><a href="?p=index">top</a></li>
                    <li><a href="?p={{page}}&a=edit">edit</a></li>
                    <li><a href="?a=add">add</a></li>
                    <li><a href="?a=remove">remove</a></li>
                </ul>
            </nav>
        </header>
        <div id=container>
            <nav>{{sidebar}}</nav>
            <main>
                <form method=POST>
                    <p>A page "{{page}}" doesn't exists.</p>
                    <p>Add and edit "<a href="?p={{page}}&a=edit">{{page}}</a>" ?</p>
                </form>
            </main>
        </div>
    </body>
</html>

