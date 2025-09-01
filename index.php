<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'website de livros');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}