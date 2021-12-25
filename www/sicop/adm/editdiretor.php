<?php
if ( !isset( $_SESSION ) ) session_start();

/*ob_start("ob_gzhandler");*/

    require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$nivel_necessario = 3;
$n_admsist = get_session( 'n_admsist', 'int' );

if ( $n_admsist < $nivel_necessario ) {
    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso à página sem permissões.\n\n Página: $pag";
    salvaLog( $mensagem );
    exit;
}

$iddir = get_get( 'iddir', 'int' );

if ( empty( $iddir ) ) {
    $mensagem = "<span class='desc_atencao'>*** ATENÇÃO ***</span> --> Tentativa de acesso direto à página de alteração de dados do diretor.\n\n Página: $pag";
    salvaLog( $mensagem );
    echo msg_js( 'FALHA!!!', 1 );
    exit;
}

$q_diretor = "SELECT `iddiretoresn`, `diretor`, `titulo_diretor`, `setor`, `ativo` FROM `diretores_n` WHERE `iddiretoresn` = $iddir LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$q_diretor = $model->query( $q_diretor );

// fechando a conexao
$model->closeConnection();

if( !$q_diretor ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}

$d_dir = $q_diretor->fetch_assoc();

$querysetor = 'SELECT `idsetor`, `sigla_setor` FROM `sicop_setor` ORDER BY `sigla_setor`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querysetor = $model->query( $querysetor );

// fechando a conexao
$model->closeConnection();

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$trail = new Breadcrumb();
$trail->add('Alterar dados do diretor', $_SERVER['PHP_SELF'], 4);
$trail->output();
?>

            <p class="descript_page">ALTERAR DADOS DO DIRETOR</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senddiretor.php" method="post" name="editdir" id="editdir" onSubmit="return validadiretor();">

                <table class="edit">
                    <tr>
                        <td width="50">Nome:</td>
                        <td width="330">
                            <input name="diretor" type="text" class="CaixaTexto" id="diretor" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_dir['diretor'] ?>" size="60" maxlength="80" />
                        </td>

                    </tr>
                    <tr>
                        <td>Título:</td>
                        <td><input name="titulo_diretor" type="text" class="CaixaTexto" id="titulo_diretor" onkeypress="return blockChars(event,1);" value="<?php echo $d_dir['titulo_diretor'] ?>" size="60" maxlength="60" /></td>
                    </tr>
                    <tr>
                        <td>Setor:</td>
                        <td>
                            <select name="setor" class="CaixaTexto" id="setor">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ( $dados = $querysetor->fetch_assoc() ) { ?>
                                <option value="<?php echo $dados['idsetor']; ?>" <?php echo $dados['idsetor'] == $d_dir['setor'] ? 'selected="selected"' : ''; ?>><?php echo $dados['sigla_setor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Ativo:</td>
                        <td>
                            <input name="ativo" type="radio" id="ativo_0" value="1" <?php echo $d_dir['ativo'] == '1' ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="ativo" type="radio" id="ativo_1" value="0" <?php echo empty( $d_dir['ativo'] ) ? 'checked="checked"' : ''; ?>/> Não
                        </td>
                    </tr>
                </table>



                <input name="proced" type="hidden" id="proced" value="1" />
                <input name="iddir" type="hidden" id="iddir" value="<?php echo $iddir; ?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

            <script type="text/javascript">id("diretor").focus();</script>

<?php include 'footer.php'; ?>