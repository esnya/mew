
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{{title}}</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
    </head>
    <body>
        <header>
            <h1><a href="?p={{page}}">Remove "{{title}}"</a></h1>
            <nav>
                <ul>
                    <li><a href="?p=index">top</a></li>
                    <li><a href="?p={{page}}&a=edit">edit</a></li>
                    <li><a href="?a=add">add</a></li>
                    <li><a href="?p={{page}}&a=remove">remove</a></li>
                </ul>
            </nav>
        </header>
        <div id=container>
        <nav>{{sidebar}}</nav>
            <main>
                <p>Are you sure to remove?</p>
                <form method=POST>
                    <input type=hidden name=remove value=yes>
                    <input type=submit value=Remove>
                </form>
            </main>
        </div>
    </body>
</html>
