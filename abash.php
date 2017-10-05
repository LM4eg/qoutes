<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP learning</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>

<div class="container">

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="abash/abash.php">Project name</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="abash/abash.php">Home</a></li>
                    <li><a href="abash/abash.php">Admin</a></li>
                    <li><a href="abash/abash.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <h1>PHP learning</h1>

    <div class="col-md-3">
        <h2>Login form</h2>
        <form action="abash.php?action=login" method="POST" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 control-label">Login:</label><input name="username" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label">Password:</label><input name="password" type="password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Log in" class="btn btn-default">
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <?php
        //Открываем сессию
        session_start();

        //Подключение к БД
        $link = mysqli_connect("localhost", "qoutes", "password", "qoutes");
        if(!$link) {
            die('Failed connect to DB');
        }


        //TODO: Регистрация юзера
        function register($link, $id, $username, $password){

        }
        //Логин юзера
        function login($link, $username, $password){
                $username = $_POST['username'];
                $password = $_POST['password'];

                $query = "SELECT * FROM users WHERE nickname = '$username' AND password = MD5('$password')";
                $result = mysqli_query($link, $query);

                    if (mysqli_num_rows($result) == 1 ) {
                        $row = mysqli_fetch_assoc($result);
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['nickname'] = $row['nickname'];
                        echo "Всё ок";
                    }
                    else {
                        $_SESSION['message'] = "Please enter a valid username or password";
                        echo "Не ок";
                    }
        }
        //TODO: Сброс пароля
        function resetpass($link, $id, $username, $password){

        }

        //Вывод всех цитат. SELECT * FROM table_name LIMIT 2,3 для TODO: пагинация
        function quote_all($link)
        {
            //Запрос
            $query = "SELECT * FROM quote ORDER BY id DESC";
            $result = mysqli_query($link, $query);

            if (!$result)
                die(mysqli_error($link));

            while ($row = mysqli_fetch_assoc($result)) {
                $datetime = date('d-m-Y G:i', $row["added"]);
                printf('<div class="col-md-4"><a href="?id=%s">#%s</a></div><div class="col-md-4"><p class="text-center">%s</p></div><div class="col-md-4"><p class="text-right">- %s +</p></div>
<div class="col-md-12 well">%s</div>', $row["id"], $row["id"], $datetime, $row["rate"], $row["post"]); //сделано уёбищно, но работает. TODO: Переписать это говно.
            }
        }

        if (!isset($_GET['id'])) {
            quote_all($link);
        }

        //Вывод определенной цитаты.
        function quote_get($link, $id)
        {
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id']; //Надо делать так для числовых переменных (закрывает уязвимость)
                $query = sprintf("SELECT * FROM quote WHERE id=%d", (int)$id);
                $result = mysqli_query($link, $query);

                $row = mysqli_fetch_assoc($result);
                $datetime = date('d-m-Y G:i', $row["added"]);
                printf('<div class="col-md-4">#%s</div><div class="col-md-4"><p class="text-center">%s</p></div><div class="col-md-4"><p class="text-right">- %s +</p></div>
<div class="col-md-12 well">%s</div>', $row["id"], $datetime, $row["rate"], $row["post"]); //сделано уёбищно, но работает. TODO: Переписать это говно.
            }
        }

        quote_get($link, $_GET['id']);

        //Добавление цитаты В БД
        function quote_add($link, $post, $added, $rate)
        {
            //Убираем пробелы
            $post = trim($post);
            $post = htmlentities($post);

        //Проверяем цитату на пустоту
            if ($post == '')
                return false;

            //Запрос (вставляем в таблицу цитату, дату и рейтинг)
            $t = "INSERT INTO quote (post, added, rate) VALUES ('%s', '%s', '%s')";

            $query = sprintf($t,
                mysqli_real_escape_string($link, $post),
                mysqli_real_escape_string($link, $added),
                mysqli_real_escape_string($link, $rate));

            $result = mysqli_query($link, $query);

            if (!$result)
                die(mysqli_error($link));
            return true;
        }

        //TODO: Редактирование поста
        function quote_edit($link, $id, $quote, $added, $rate)
        {
        //Подготовка
            $title = trim($title);
            $content = trim($content);
            $added = trim($date);
            $id = (int)$id;

            if ($title == '')
                return false;

            //Запрос
            $sql = "UPDATE quote SET title='%s', content='%s', added='%s' WHERE id='%d'";

            $query = sprintf($sql, mysqli_real_escape_string($link, $title),
                mysqli_real_escape_string($link, $content),
                mysqli_real_escape_string($link, $added),
                $id);
            $result = mysqli_query($link, $query);

            if (!$result)
                die(mysqli_error($link));

            return mysqli_affected_rows($link);
        }

        //Проверка на action, если quote_add, то вызывает функцию для добавления поста в БД. Если login, то пытаемся залогинить
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
        } else {
            $action = "";
        }
        if ($action == "quote_add") {
            if (!empty($_POST)) {
                quote_add($link, $_POST['quote'], time(), "0");
            }
        }
        if ($action == "login") {
            if (!empty($_POST)) {
                login($link, $_POST['username'], $_POST['password']);
            }
        }
        ?>
    </div>
    <div class="col-md-6">
        <h2>Add quote</h2>
        <form action="abash.php?action=quote_add" method="POST" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 control-label">Email:</label><input type="email" class="form-control">
            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label">Quote:</label><textarea class="form-control" rows="5" name="quote" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit quote" class="btn btn-default">
            </div>
        </form>
    </div>
    <div class="col-md-12">
        <footer>
            <p>© 2017 Ed Deline</p>
        </footer>
    </div>
</div>
</body>

</html>
