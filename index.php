<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'filmes');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}