<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{{title}}</title>
        <link rel=stylesheet href=theme/{{theme}}/css/style.css>
    </head>
    <body>
        <header>
            <h1>
                <a href="?"><svg width=42 height=42 style="vertical-align: middle;">
                        <g transform="translate(0.000000,40.000000) scale(0.020,-0.020)" fill="#000000" stroke="none">
                            <path d="M587 1808 l-68 -41 -54 13 c-69 17 -114 9 -171 -29 -34 -24 -56 -31 -92 -31 -83 0 -92 -6 -92 -59 0 -52 12 -68 82 -110 42 -25 100 -87 123 -130 8 -17 20 -63 25 -103 6 -40 17 -120 24 -178 9 -60 34 -165 60 -247 67 -214 114 -470 103 -566 -5 -42 -14 -58 -49 -95 -42 -42 -43 -44 -23 -58 24 -18 117 -11 153 12 26 17 26 16 23 229 l-2 160 30 -50 c37 -63 116 -139 209 -203 l73 -50 -41 -7 c-62 -10 -94 -71 -50 -95 13 -7 160 -11 431 -11 l411 -1 37 30 c78 63 101 121 101 260 0 130 -22 216 -90 347 -98 192 -113 257 -86 380 16 75 32 113 85 198 44 73 47 83 30 121 -16 34 -35 39 -69 19 -45 -26 -112 -198 -140 -359 -27 -154 -6 -252 90 -404 59 -93 75 -155 72 -275 -1 -55 -6 -113 -10 -130 -7 -26 -10 -17 -20 60 -18 137 -60 215 -181 335 -109 108 -146 135 -356 250 -259 143 -310 182 -376 283 -46 70 -75 156 -89 260 -6 43 -17 98 -25 122 -12 40 -11 50 6 95 24 64 25 100 2 100 -10 -1 -49 -19 -86 -42z"/>
                        </g>
                    </svg></a>
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
