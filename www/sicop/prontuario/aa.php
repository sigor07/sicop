<!DOCTYPE HTML PUBLIC "-//W3C//DTD 
HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" 
content="text/html; charset=iso-8859-1">
<title>Dica de Contexto </title>

<style type="text/css">
<!--
  .formata { /* esta classe é somente 
               para formatar a fonte */
  font: 12px arial, verdana, helvetica, sans-serif; 
  }
body {
    background-color: #F0F0F0;
    font-family: Verdana, Geneva, sans-serif;
    font-size: 10px;
    width: 950px;
    margin-right: auto;
    margin-bottom: auto;
    margin-left: auto;
}
.paragrafo9 {
    font-family: Arial, Verdana, Tahoma, sans-serif;
    font-size: 9px;
}
.paragrafo10 {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 10px;
    line-height: 5px;
}
.paragrafo10negrito {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 10px;
    font-weight: bold;
}
.paragrafo12 {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 12px;
}
.paragrafo12negrito {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 12px;
    font-weight: bold;
}
.paragrafo12Italico {
    background-color: #F0F0F0;
    font-family: Verdana, Geneva, sans-serif;
    font-size: 12px;
    font-style: italic;
}

.paragrafo14 {
    background-color: #F0F0F0;
    font-family: Verdana, Geneva, sans-serif;
    font-size: 14px;
}

.paragrafo14Italico {
    background-color: #F0F0F0;
    font-family: Verdana, Geneva, sans-serif;
    font-size: 14px;
    font-style: italic;
}

.par_curto {
    line-height: 0px;
}

.CaixaTexto {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 9px;
    background-color: #F8F8F8;
    padding: 3px;
    border: 1px solid #E5E5E5;
}
.CaixaTextoC {
    font-family: Verdana, Geneva, sans-serif;
    font-size: 9px;
    background-color: #F8F8F8;
    padding: 3px;
    border: 1px solid #E5E5E5;
    text-align: center;
}

/* LINKS */
a:link {
    color: #009; 
    text-decoration: none;
}
a:visited {
    color: #009;
    text-decoration: none;
}
a:hover {
    color: #090;
    text-decoration: underline;
    
}

a.dcontexto{
  position:relative; 
  padding:0;
  color:#039;
  text-decoration:none;
  z-index:24;
}
a.dcontexto:hover{
    color: #090;
    text-decoration: underline;
    background: transparent;
    z-index:25; 
}

a.dcontexto span.dc{
    display: none
}

a.dcontexto:hover span.dc{ 
  display:block;
  position:absolute;
  width:230px; 
  top:3em;
  text-align:justify;
  text-decoration: none;
  left:5px;
  padding:5px 10px;
  border:1px solid #999;
  background: #DFDFDF /*e0ffff*/; 
  color:#000;
}

.texto_busca{
    font-weight: bold;
}

div.dcontexto{
  position:relative; 
  padding:0;
  text-decoration:none;
  z-index:24;
}
div.dcontexto:hover{
    background:transparent;
    z-index:25; 
  }
div.dcontexto span{display: none}
div.dcontexto:hover span{
    display:block;
    position:absolute;
    width:200px;
    top:3em;
    text-align:justify;
    text-decoration: none;
    padding:5px 10px;
    border:1px solid #999;
    background: #DFDFDF /*e0ffff*/;
    color:#000;
    right: 0px;
}


div#box_p{
    width: 940px;
    padding: 5px 2px 5px 2px;
    background: #C8C8C8;
    margin:0 auto;
}

ul#menu_pl {
    /*width:300px;*/
    margin:0;
    padding:0 5px 0 5px;
    list-style-type:none;
    float: left;
}
ul#menu_pl li {
    padding-bottom: 2px;
    padding-top: 2px;
}

ul#menu_pr {
    /*width:800px;*/
    margin:0;
    padding:0 5px 0 5px;
    text-align: right;
    list-style-type:none;
    /*float: right;*/
}
ul#menu_pr li {
    padding-bottom: 2px;
    padding-top: 2px;
}

div#box_inf{
    width: 940px;
    padding: 5px 2px 5px 2px;
    background: #E6E6E6;
    text-align: center;
    margin: 3px auto;
}

div#irtopo {
    position:fixed;
    bottom:0px;
    right:0px;
    text-align: right;
}

div.linha{
    width: 744px;
    padding: 5px;
    margin:0 auto;
}

div.linha_rol{
    width: 785px;
    padding: 5px;
    margin:0 auto;
}

div#grupo{
    width: 400px;
    padding: 5px;
    margin:0 auto;
}

ul#menu {
    width:150px;
    /*border:1px solid #003399;
    background:#FADE8B;*/
    margin:0;
    padding:0;
    padding-left: 5px;
    list-style-type:none;
}

ul#menu li {
    border-bottom:1px solid #A4A0F5;
    padding-bottom: 2px;
    padding-top: 2px;
}

ul#menu li.sub {
    margin-top: 10px;
}
ul#menu li.sub div {
    display:block;
    height:1%;
    text-decoration:none;
    padding-left: 5px;
    border-left:10px solid #CCC;
}

ul#menu li a:link, ul#menu li a:visited {
    display:block;
    height:1%;
    text-decoration:none;
    border-left:10px solid #FAFAFA;
    padding-left:5px;
}
ul#menu li a:hover {
    background-color: #CCC;
    color:#090;
    border-left:10px solid #666;
}

div.lista_inc a:link, .lista_inc a:visited {
    display:block;
    width:16px;
    height:14px;
    text-decoration:none;
}

div.lista_inc a:hover {
    background-color: #CCC;
    color:#090;
    text-decoration: underline;
}

ul#menuok {
    width:300px;
    /*border:1px solid #003399;
    background:#FADE8B;*/
    margin:auto;
    padding:0;
    padding-left: 5px;
    list-style-type:none;
    }
ul#menuok li {
    border-bottom:1px solid #A4A0F5;
    padding-bottom: 2px;
    padding-top: 2px;
}
ul#menuok li a:link, ul#menuok li a:visited {
    display:block;
    height:1%;
    text-decoration:none;
    border-left:10px solid #FAFAFA;
    padding-left:5px;
}
ul#menuok li a:hover {
    background-color: #CCC;
    color:#090;
    border-left:10px solid #666;
}    

table.bordasimples {border-collapse: collapse;}

table.bordasimples tr td {border:1px solid #CCC;}

div#msg {
    padding: 5px;
}

div#msg_log {
    padding: 10px 5px;
}

div.espaco_table {
    padding-right: 3px;
    padding-left: 3px;
}

td.espaco_td {
    padding-right: 3px;
    padding-left: 3px;
}

table.space td{
    padding-right: 3px;
    padding-left: 3px;
}

/*CCCCCC*/
/*FAFAFA*/

table#fixa {
    table-layout: fixed;
    word-wrap:break-word;
}

table.fixa {
    table-layout: fixed;
    word-wrap:break-word;
}

table tr.add th, .add { /* COR ESCURA */
    background-color:#FAFAFA;
}

table tr.marked th, table tr.marked { /* COR QUANDO MARCADA NO CHECK BOX */
    background-color:#CDCEE0;
}

.add:hover, .hover { /* COR AO PASSAR MOUSE */
    background-color:#CCCCCC;
}




table tr.odd th, .odd { /* COR ESCURA*/
    background-color:#E9E9E9;
}

table tr.even th, .even { /* COR CLARA */
    background-color:#FAFAFA;
}

table tr.marked th, table tr.marked { /* COR QUANDO MARCADA NO CHECK BOX FFCC99*/
    background-color:#CDCEE0;
}

.odd:hover, .even:hover, .hover { /* COR AO PASSAR MOUSE */
    background-color:#CCCCCC;
}

table tr.odd:hover th, table tr.even:hover th, table tr.hover th { /* COR AO PASSAR MOUSE */
    background-color:#CCCCCC;
}

.desc_erro {
    font-weight:bold;
    color:#F00;
    text-align:center;
}

.desc_erro_php {
    font-weight:bold;
    color:#F00;
    text-align:center;
}

.desc_atencao {
    font-weight:bold;
    color:#F00;
}

/*  a.dcontexto{
  position:relative; 
  font:12px arial, verdana, helvetica, sans-serif; 
  padding:0;
  color:#039;
  text-decoration:none;
  border-bottom:2px dotted #039;
  cursor:help; 
  z-index:24;
  }
  a.dcontexto:hover{
  background:transparent;
  z-index:25; 
  }
  a.dcontexto span.dc{display: none}
  a.dcontexto:hover span.dc{ 
  display:block;
  position:absolute;
  width:230px; 
  top:3em;
  text-align:justify;
  left:0;
  font: 12px arial, verdana, helvetica, sans-serif; 
  padding:5px 10px;
  border:1px solid #999;
  background:#e0ffff; 
  color:#000;
  }
*/  -->
</style>

</head>
<body>
<p class="formata">
Este é um texto qualquer destinado a demonstrar
 a obtenção da dica de contexto com uso das 
<a href="#" class="dcontexto">CSS E
do documento web..bla..bla..bla... Este é o parágrafo seguinte
<span class="dc"><strong>CSS</strong>
Sigla para a palavra inglesa "Cascading Style Sheet"
foi traduzido para <strong>Folhas de Estilo em 
Cascata </strong>.Uma técnica de projetar páginas Web, 
separando conteúdo da apresentação.</span></a> 
e com isto fornecer a você mais uma ferramenta 
para construção de suas páginas web </p>
<p class="formata">Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... <a href="#"  class="dcontexto">Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte</a>
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla...  </p>


<table width="927" align="center" id="fixa">
  <tr>
        <th width="35" align="center">N</th>
        <th width="250" align="center">Detento </th>
        <th width="90" align="center">Matr&iacute;cula</th>
        <th width="90" align="center">Inquérito </th>
        <th width="100" align="center">Processo</th>
        <th width="75" align="center">Vara </th>
    <th width="229" align="center">Comarca</th>
    <th width="22" align="center">&nbsp;</th>
  </tr>
<tr bgcolor="#FAFAFA" >
        <td align="left"></td>
        <td><a href="1111" class="dcontexto"> adsfasdf asdf ads <span class="dc"><strong>Pai:</strong> aaaaaaaaaaaaaaa <br /><strong>Mãe:</strong> bbbbbbbbbbbbbbbbb <br /><strong>Raio:</strong> eeeeeeeeeeee <br /><strong>Cela:</strong> aaaaaaaaaaaa <br /><strong>Situação atual:</strong> aaaaaaaaaaaaaaa></span></a></td>
      <td align="center">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
    </tr>

</table>
<p class="formata">
 com uso das 
<a href="#" class="dcontexto">CSS E
do documento web..bla..bla..bla... Este é o parágrafo seguinte
<span class="dc"><strong>CSS</strong>
Sigla para a palavra inglesa "Cascading Style Sheet"
foi traduzido para <strong>Folhas de Estilo em 
Cascata </strong>.Uma técnica de projetar páginas Web, 
separando conteúdo da apresentação.</span></a> 
e com isto fornecer a você mais uma ferramenta 
para construção de suas páginas web </p>
<p class="formata">Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... <a href="#"  class="dcontexto">Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte</a>
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla... Este é o parágrafo seguinte 
no fluxo normal 
do documento web..bla..bla..bla...  </p>
</body>
</html>
