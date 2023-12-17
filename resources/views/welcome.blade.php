<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
        }

        .fullscreen-container {
            height: 100%;
            background: linear-gradient(to bottom, #000000, #1C2335 30%, #162F6E 70%, #003DDF, #1C2335);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content {
            color: #fff;
            /* Cor do texto dentro da div */
        }

        .link-sem-decoracao {
            text-decoration: none;
            color: white;
        }
    </style>
</head>

<body class="fullscreen-container">

    <div class="content">
        <h1>Essa é uma Api do Sat Cartões Beneficios</h1>
        <h3><a class="link-sem-decoracao" href="">Clique aqui para ir a pagina de login</a> </h3>
    </div>

</body>

</html>