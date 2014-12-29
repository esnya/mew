<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Editing "{{title}}"</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
    </head>
    <body>
        <header>
            <h1><a href="?p={{page}}">{{title}}</a></h1>
            <nav>
                <ul>
                    <li><a href="?p=index">top</a></li>
                    <li>edit</li>
                    <li><a href="?a=add">add</a></li>
                    <li><a href="?p={{page}}&a=remove">remove</a></li>
                </ul>
            </nav>
        </header>
        <div id=container>
            <nav>{{sidebar}}</nav>
            <main>
                <form method="post">
                    <div>
                        <textarea name=code>{{code}}</textarea>
                    </div>
                    <div>
                        <input type="submit">
                    </div>
                </form>
                <div>
                    <h1>Preview</h1>
                    <div id=preview>{{content}}</div>
                </div>
            </main>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script>
            $(function () {
                'use strict';
                $('[name=code]').bind({
                    focus: function () {
                        var input = $(this);
                        if (!input.data('timer')) {
                            input.data('old', input.val());
                            input.data('timer', setInterval(function () {
                                if (input.val() != input.data('old') && !input.data('loading')) {
                                    input.data('loading', input.val());
                                    var data = {code: input.val()};
                                    $.post('?p={{page}}&a=preview', data).then(
                                        function (html) {
                                            input.data('old', data.code);
                                            $('#preview').html(html);
                                        }
                                    ).done(function () {
                                        input.data('loading', false);
                                    });
                                }
                            }, 1000));
                        }
                    },
                    blur: function () {
                        var input = $(this);
                        if (input.data('timer')) {
                            clearInterval(input.data('timer'));
                            input.data('timer', null);
                        }
                    }
                });
            });
        </script>
    </body>
</html>
