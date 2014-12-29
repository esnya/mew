<!DOCTYPE html>
<html lang=ja>
    <head>
        <meta charset=UTF-8>
        <title>Add</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
    </head>
    <body>
        <header>
            <h1>Add new page</h1>
            <nav>
                <ul>
                    <li><a href="?p=index">top</a></li>
                    <li><a href="?p={{page}}&a=edit">edit</a></li>
                    <li><a href="?a=add">add</a></li>
                    <li><a href="?a=remove">remove</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <form method=POST>
                <input type=text name=name>
                <input type=submit>
            </form>
        </main>
    </body>
</html>
