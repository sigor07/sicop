<?
/**
 * Show Progress Bar
 * Função para mostrar uma barra de progresso.
 *
 * @param int $width        -> Largura total da barra (em pixels)
 * @param float $percent    -> Porcentagem a ser exibida
 * @param str $type        -> Cor da barra: green / red / blue (Padrão: green)
 * @param str $color        -> Cor do texto da barra (Padrão: #000)
 * @return str            -> Retorna uma string com todo o código da barra formatada
 */
function show_prog_bar($width, $percent, $type = 'green', $color = '#000') {
    $font =            'Tahoma';
    $font_size =        '8px';
    $font_weight =        'bold';    // bold, normal
    $imgs_folder =        'images/';

    // == Don't edit below ==
    $percent = min($percent, 300);
    $width -= 2;
    $result = (($percent*$width) / 100);
    if ($result > $width){
        $result = $width;
    }
    $return = '';
    $return .= '<div name="progress">';
    $return .= '<div style="background: url(\''.$imgs_folder.'/progress.gif\') no-repeat; height: 13px; width: 1px; display: block; float: left"><!-- --></div>';
    $return .= '<div style="background: url(\''.$imgs_folder.'/bg.gif\'); height: 13px; width: '.$width.'px; display: block; float: left">';

    $return .= '<span style="background: url(\''.$imgs_folder.'/on_'.strtolower($type).'.gif\'); display: block; float: left; width: '.$result.'px; height: 11px; margin: 1px 0; font-size: '.$font_size.'; font-family: \''.$font.'\'; line-height: 11px; font-weight: '.$font_weight.'; text-align: right; color: '.$color.'; letter-spacing: 1px;">&nbsp;'.$percent.'%&nbsp;</span>';

    $return .= '</div>';
    $return .= '<div style="background: url(\''.$imgs_folder.'/progress.gif\') no-repeat; height: 13px; width: 1px; display: block; float: left"><!-- --></div>';
    $return .= '</div>';
    return $return;
}
?>

<?php

    $porcentna = 300/768*100;
    $porcentna = round($porcentna, 0);
    
    if ($porcentna <= 90){
        $corna = 'green';
    }elseif (($porcentna >90) && ($porcentna <= 100)){
        $corna = 'blue';
    }else{
        $corna = 'red';
    }
?>
  <html>
  <head><title>Teste</title>
  <body>
  <?php print show_prog_bar(200, rand(1, 100));?><br />
  <?php print show_prog_bar(200, rand(1, 100), 'red');?><br />
  <?php print show_prog_bar(200, rand(1, 100), 'blue', 'black');?><br />
  <?php print show_prog_bar(200, $porcentna, $corna, 'black');?><br />
  <?php print show_prog_bar(200, $porcentna, $corna, 'black');?>
  </body>
  </html>