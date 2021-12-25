<?php
   
   function montaCombo($nomeCombo) {
      $sql    = "SELECT idnivel, descnivel FROM cdriousernivel ORDER BY idnivel";
      $result = mysql_query($sql);

      echo "<select name ='$nomeCombo' class='CaixaTexto'>";
      while($dados = mysql_fetch_assoc($result)) {
         echo "<option value =".$dados['idnivel'].">";
         echo $dados['descnivel'];
         echo "</option>"; 
      }
      echo "</select>";
   }
   // salve esse arquivo como funcao.php e chame por include na página que irá usá-lo
   // ou escreva-o na própria página que precisa usar o combo
?>