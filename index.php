<?php
try{
    Bd_Init::init()->bootstrap()->run();
}catch (Exception $e){
    header('http/1.1 500');
    echo '<pre>';
    echo '<p>'.$e->getMessage().'</p>';
    echo $e->getTraceAsString();
    echo '</pre>';
}