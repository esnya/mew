<?php
$config = [
    'theme' => 'default',   // Theme name / テーマ名
    'index' => 'index', // Top page name / トップページ名
    'sidebar' => 'sidebar', // Sidebar page name / サイドバー名

    'admin' => [
        'admin',
        hash('sha256', 'admin'), // sha256 hashed
    ],  // Admin's name and password / 管理者名とパスワード

    'filetype' => [
        'image/png',
        'image/bmp',
        'image/jpeg',
    ],  // Arrowed uploading file types / アップロードを許可するファイルタイプ
    'maxsize' => 512 * 1024,    // Maximum file size allows to uplaod / アップロードを許可する最大ファイルサイズ

    'tag' => [
        'whitelist' => null,    // Set array to use whitelis / 配列を設定することでホワイトリスト制に
        'blacklist' => ['script', 'iframe'],
    ],  // HTML tags can be used on markdown / Markdown中に使用可能なHTMLタグ

    'debug' => false,    // Debug mode / デバッグモード
];
