#!/usr/bin/php
<?php

require_once 'products.php';

define('MIN_AGE', 7);
define('MAX_AGE', 150);

$cart = [];
$userName = null;
$userAge = null;

function printHeader() {
    echo "################################\n";
    echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
    echo "################################\n";
}

function showMenu() {
    echo "1 Вибрати товари\n";
    echo "2 Отримати підсумковий рахунок\n";
    echo "3 Налаштувати свій профіль\n";
    echo "0 Вийти з програми\n";
    echo "Введіть команду: ";
}

function showProducts($products) {
    echo "№  НАЗВА                     ЦІНА\n";
    foreach ($products as $key => $item) {
        printf("%-3d%-25s%5d\n", $key, $item['name'], $item['price']);
    }
    echo "   -----------\n";
    echo "0  ПОВЕРНУТИСЯ\n";
}

function showCart($cart, $products) {
    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ\n";
        return;
    }
    echo "У КОШИКУ:\n";
    echo "НАЗВА                     КІЛЬКІСТЬ\n";
    foreach ($cart as $id => $qty) {
        printf("%-25s%5d\n", $products[$id]['name'], $qty);
    }
}

function selectProducts(&$cart, $products) {
    while (true) {
        showProducts($products);
        echo "Виберіть товар: ";
        $choice = trim(fgets(STDIN));

        if (!is_numeric($choice)) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
            continue;
        }

        $choice = (int)$choice;

        if ($choice === 0) {
            break;
        }

        if (!isset($products[$choice])) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
            continue;
        }

        echo "Вибрано: {$products[$choice]['name']}\n";
        echo "Введіть кількість, штук: ";
        $quantity = trim(fgets(STDIN));

        if (!is_numeric($quantity) || (int)$quantity < 0 || (int)$quantity > 99) {
            echo "ПОМИЛКА! КІЛЬКІСТЬ ПОВИННА БУТИ ВІД 0 ДО 99\n";
            continue;
        }

        $quantity = (int)$quantity;

        if ($quantity === 0) {
            unset($cart[$choice]);
            echo "ВИДАЛЯЮ З КОШИКА\n";
        } else {
            $cart[$choice] = $quantity;
        }

        showCart($cart, $products);
    }
}

function printReceipt($cart, $products) {
    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ\n";
        return;
    }

    echo "№  НАЗВА                     ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
    $number = 1;
    $total = 0;
    foreach ($cart as $id => $qty) {
        $product = $products[$id];
        $price = $product['price'];
        $cost = $price * $qty;
        printf("%-3d%-25s%5d%10d%10d\n", $number++, $product['name'], $price, $qty, $cost);
        $total += $cost;
    }
    echo "РАЗОМ ДО CПЛАТИ: {$total}\n";
}

function setupProfile(&$userName, &$userAge) {
    do {
        echo "Ваше імʼя: ";
        $name = trim(fgets(STDIN));
        if (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄ]/u', $name)) {
            echo "ПОМИЛКА! Імʼя має містити хоча б одну літеру.\n";
        }
    } while (!preg_match('/[a-zA-Zа-яА-ЯіІїЇєЄ]/u', $name));

    do {
        echo "Ваш вік: ";
        $age = trim(fgets(STDIN));
        if (!is_numeric($age) || (int)$age < MIN_AGE || (int)$age > MAX_AGE) {
            echo "ПОМИЛКА! Вік повинен бути від " . MIN_AGE . " до " . MAX_AGE . " років.\n";
        }
    } while (!is_numeric($age) || (int)$age < MIN_AGE || (int)$age > MAX_AGE);

    $userName = $name;
    $userAge = (int)$age;
}

printHeader();
while (true) {
    showMenu();
    $command = trim(fgets(STDIN));

    switch ($command) {
        case '1':
            selectProducts($cart, $products);
            break;
        case '2':
            printReceipt($cart, $products);
            break;
        case '3':
            setupProfile($userName, $userAge);
            break;
        case '0':
            exit(0);
        default:
            echo "ПОМИЛКА! Введіть правильну команду\n";
    }
}
?>
