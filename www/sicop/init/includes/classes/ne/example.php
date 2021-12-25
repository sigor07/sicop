<?
   /*    
    *    Incluindo a classe
    */
        require("numeroExtenso.class.php");

   /*    
    *    Instanciando objeto
    */
        $ne = new numeroExtenso;
   
   /*    
    *    Escrevendo valores por extenso
    *    escrever($numero,$caps)
    *    =======================
    *
    *    Método usado para escrever o numero por extenso.
    *
    *    Parâmetros:    
    *                $numero        => É o número a ser escrito por extenso;
    *                $caps        => Indica se haverá "Capitalize" no texto:
    *                                    $caps=1 (default) / Capitalize
    *                                    $caps=0 Escreve em minúsculas;
    */
        echo $ne->escrever(10001) . "<br>";
        echo $ne->escrever(500,0) . "<br>";
        echo $ne->escrever(49) . "<br>";
        echo $ne->escrever(620) . "<br>";
        echo $ne->escrever(1080,0) . "<br>";
        echo $ne->escrever(57810) . "<br>";
?>