<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $siteName ?></title>
</head>
<body>

</body>
</html>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="<?= $router->generateUri('car.addCar') ?>">Ajouter un véhicule</a></li>
    </ul>
</nav>