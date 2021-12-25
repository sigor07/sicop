<?php
if ( !isset( $_SESSION ) ) session_start();

$secretaria    = $_SESSION['secretaria'];
$coordenadoria = $_SESSION['coordenadoria'];
$unidadecurto  = $_SESSION['unidadecurto'];

?>
<div class="imagem_d">
<img src="<?php echo SICOP_SYS_IMG_PATH; ?>brasao.png" alt="" width="82" height="90" />
</div>

<div class="paragrafo">
<p align="center" class="paragrafo12"><?php echo $secretaria ?></p>
<p align="center" class="paragrafo12"><?php echo $coordenadoria ?></p>
<p align="center" class="paragrafo12"><?php echo $unidadecurto ?></p>
</div>

<div class="imagem_e">
<img src="<?php echo SICOP_SYS_IMG_PATH; ?>bandeira.png" alt="" width="120" height="76"  />
</div>