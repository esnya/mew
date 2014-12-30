<?php
$config = [
    'theme' => 'default',   // Theme name / テーマ名
    'index' => 'index', // Top page name / トップページ名
    'sidebar' => 'sidebar', // Sidebar page name / サイドバー名

    'admin' => [
        'admin',
        hash('sha256', 'admin'), // sha256 hashed
    ],  // Admin's name and password / 管理者名とパスワード

    'debug' => false,    // Debug mode / デバッグモード
];
