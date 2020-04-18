<?php

/*
    Для работы скрипта потребуется:
        - PHP >= 7.0.0
        - Sqlite >= 3.17
    Отдельный вебсервер не требуется, достаточно встроенного в PHP
    Достаточно просто запустить из консоли "php -S {Адрес сервера}:{Порт} {Путь к файлу index.php}"
    Например: "php -S localhost:8080 /var/www/index.php"
    Пример на windows: "Z:\php\php.exe -S localhost:8080 C:\www\index.php"
    
    В отдельных случаях, когда дополнительные библиотеки на PHP отключены, в файле php.ini нужно раскомментировать три строчки:
    ;extension=pdo_sqlite
    ;extension=sqlite3
    
    Если взяли чистый php, то вероятно php.ini может отсутствовать. Тогда в папке берем файл php.ini-production (который находится в той же директории, что и php.exe), в нем проводим вышеописанные манипуляции и переименовываем в php.ini

*/

const DB_CONNECT = "sqlite:main.sqlite";
const SQL_CREATE = "CREATE TABLE IF NOT EXISTS phonebook (id INTEGER PRIMARY KEY AUTOINCREMENT, name, surname, patronymic, birthday, phone)";
const SQL_SELECT = "SELECT * FROM phonebook";
const SQL_INSERT = "INSERT INTO phonebook (name, surname, patronymic, birthday, phone) VALUES (:name, :surname, :patronymic, :birthday, :phone)";
const SQL_UPDATE = "UPDATE phonebook SET id = :id, name = :name, surname = :surname, patronymic = :patronymic, birthday = :birthday, phone = :phone WHERE id = :id";
const SQL_DELETE = "DELETE FROM phonebook WHERE id = :id";

$db = new PDO(DB_CONNECT);
$db->exec(SQL_CREATE);

if (isset($_REQUEST["insert"])) {
    $sqlStmt = $db->prepare(SQL_INSERT);
    $sqlStmt->execute($_REQUEST["data"]);
} elseif (isset($_REQUEST["update"])) {
    $sqlStmt = $db->prepare(SQL_UPDATE);
    $sqlStmt->execute($_REQUEST["data"]);
} elseif (isset($_REQUEST["delete"])) {
    $sqlStmt = $db->prepare(SQL_DELETE);
    $sqlStmt->execute($_REQUEST["data"]);
}

$sqlStmt = $db->prepare(SQL_SELECT);
$sqlStmt->execute();
$phonebook = $sqlStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Телефонный справочник</title>
</head>

<body>
    <h1 class="text-center">Телефонный справочник</h1>

    <div class="container">
        <div class="row">
            <div class="col">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Фамилия</th>
                            <th>Отчество</th>
                            <th>Дата рождения</th>
                            <th>Телефон</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phonebook as $phoneBookRow): ?>
                        <tr>
                            <td><button type="button" data-raw='<?=json_encode($phoneBookRow)?>' class="btn btn-primary" data-toggle="modal" data-target="#editUserModalDialog">Редактировать</button></td>
                            <td><button type="submit" class="btn btn-primary" data-toggle="modal" form="deleteUserModalDialog" name="data[id]" value="<?=$phoneBookRow["id"]?>">Удалить</button></td>
                            <?php foreach($phoneBookRow as $phoneBookRowValue): ?>
                            <td>
                                <?=$phoneBookRowValue?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModalDialog">Добавить нового пользователя</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModalDialog" tabindex="-1" role="dialog" aria-labelledby="insertModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertModalLongTitle">Добавить нового пользователя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                </div>
                <div class="modal-body">
                    <form id="insertUserForm" method="post">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" name="data[name]" id="name" placeholder="Имя">
                        </div>
                        <div class="form-group">
                            <label for="surname">Фамилия</label>
                            <input type="text" class="form-control" name="data[surname]" id="surname" placeholder="Фамилия">
                        </div>
                        <div class="form-group">
                            <label for="patronymic">Отчество</label>
                            <input type="text" class="form-control" name="data[patronymic]" id="patronymic" placeholder="Отчество">
                        </div>
                        <div class="form-group">
                            <label for="birthday">Дата рождения</label>
                            <input type="date" class="form-control" name="data[birthday]" id="birthday" placeholder="Дата рождения">
                        </div>
                        <div class="form-group">
                            <label for="phone">Номер телефона</label>
                            <input type="tel" class="form-control" name="data[phone]" id="phone" placeholder="Телефон">
                        </div>
                        <input type="hidden" name="insert" value="" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary" form="insertUserForm">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModalDialog" tabindex="-1" role="dialog" aria-labelledby="updateModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLongTitle">Редактировать пользователя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm" method="post">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" name="data[name]" id="edit-name" placeholder="Имя">
                        </div>
                        <div class="form-group">
                            <label for="surname">Фамилия</label>
                            <input type="text" class="form-control" name="data[surname]" id="edit-surname" placeholder="Фамилия">
                        </div>
                        <div class="form-group">
                            <label for="patronymic">Отчество</label>
                            <input type="text" class="form-control" name="data[patronymic]" id="edit-patronymic" placeholder="Отчество">
                        </div>
                        <div class="form-group">
                            <label for="birthday">Дата рождения</label>
                            <input type="date" class="form-control" name="data[birthday]" id="edit-birthday" placeholder="Дата рождения">
                        </div>
                        <div class="form-group">
                            <label for="phone">Номер телефона</label>
                            <input type="tel" class="form-control" name="data[phone]" id="edit-phone" placeholder="Телефон">
                        </div>
                        <input type="hidden" name="data[id]" id="edit-id" value="" />
                        <input type="hidden" name="update" value="" />
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary" form="updateUserForm">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteUserModalDialog" method="post">
        <input type="hidden" name="delete" />
    </form>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        $('#editUserModalDialog').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var rawData = button.data('raw');
            var modal = $(this);
            modal.find('#edit-id').val(rawData.id);
            modal.find('#edit-name').val(rawData.name);
            modal.find('#edit-surname').val(rawData.surname);
            modal.find('#edit-patronymic').val(rawData.patronymic);
            modal.find('#edit-birthday').val(rawData.birthday);
            modal.find('#edit-phone').val(rawData.phone);
        })
    </script>
</body>

</html> 
