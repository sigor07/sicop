<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag  = link_pag();
$tipo = '';
$motivo_pag = 'ALTERAÇÃO DE DADOS DO USUÁRIO';

$n_admsist   = get_session( 'n_admsist', 'int' );
$n_admsist_n = 3;

if ( $n_admsist < $n_admsist_n ) {

    require 'cab_simp.php';
    $tipo = 0;
    include '../init/msgnopag.php';

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'perm';
    $msg['entre_ch'] = $motivo_pag;
    get_msg( $msg, 1 );

    exit;

}

$iduser = get_get( 'iduser', 'int' );

if ( empty( $iduser ) ) {

    // montar a mensagem q será salva no log
    $msg = array();
    $msg['tipo']  = 'atn';
    $msg['text']  = "Tentativa de acesso direto à página ( IDENTIFICADOR DO USUÁRIO EM BRANCO - $motivo_pag ).";
    get_msg( $msg, 1 );

    echo msg_js( '', 1 );

    exit;

}

$query_user = "SELECT
                 `sicop_users`.`nomeuser`,
                 `sicop_users`.`nome_cham`,
                 `sicop_users`.`usuario`,
                 `sicop_users`.`email`,
                 `sicop_users`.`cargo`,
                 `sicop_users`.`cod_setor`,
                 `sicop_users`.`iniciais`,
                 `sicop_users`.`rsuser`,
                 `sicop_users`.`ativo`,
                 `sicop_users`.`numlogins`,
                 `sicop_users`.`prelastlogin`,
                 `sicop_users`.`datalastlogin`
               FROM
                 `sicop_users`
               WHERE
                 `sicop_users`.`iduser` = $iduser
               LIMIT 1";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query_user = $model->query( $query_user );

// fechando a conexao
$model->closeConnection();

if ( !$query_user ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "Falha na consulta ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$cont = $query_user->num_rows;

if ( $cont < 1 ) {

    // montar a mensagem q será salva no log
    $msg = array( );
    $msg['tipo'] = 'err';
    $msg['text'] = "A consulta retornou 0 ocorrências ( $motivo_pag ).";
    $msg['linha'] = __LINE__;
    get_msg( $msg, 1 );

    echo msg_js( 'FALHA!', 1 );
    exit;

}

$d_user = $query_user->fetch_assoc();

$mensagem = "Acesso à página $pag";
salvaLog($mensagem);


$querysetor = 'SELECT `idsetor`, `sigla_setor` FROM `sicop_setor` ORDER BY `sigla_setor`';

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$querysetor = $model->query( $querysetor );

// fechando a conexao
$model->closeConnection();

if( !$querysetor ) {

    echo msg_js( 'FALHA!!!', 1 );
    exit;

}



$desc_pag = 'Alterar usuário';

// adicionando o javascript
$cab_js   = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
$pag_atual = $_SERVER['PHP_SELF'];
$qs = $_SERVER['QUERY_STRING'];

if ( !empty( $qs ) ) $pag_atual .=  '?' . $qs;

$trail = new Breadcrumb();
$trail->add( $desc_pag, $pag_atual, 4 );
$trail->output();
?>

            <p class="descript_page">ALTERAR DADOS DO USUÁRIO</p>

            <form action="<?php echo SICOP_ABS_PATH ?>send/senduser.php" method="post" name="edituser" id="edituser">

                <p class="table_leg">Dados do usuário:</p>

                <table width="529" class="edit">
                    <tr>
                        <td width="108" align="left">Nome do usuário:</td>
                        <td width="415" align="left">
                            <input name="nomeuser" type="text" class="CaixaTexto" id="nomeuser" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_user['nomeuser'] ?>" size="80" maxlength="80" />
                            <script type="text/javascript">id("nomeuser").focus();</script>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Primeiro nome:</td>
                        <td align="left"><input name="nome_cham" type="text" class="CaixaTexto" id="nome_cham" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_user['nome_cham'] ?>" size="30" maxlength="20" /></td>
                    </tr>
                    <tr>
                        <td align="left">Nome de acesso:</td>
                        <td align="left">
                            <input name="usuario" type="text" class="CaixaTexto" id="usuario" onblur="lowerMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_user['usuario'] ?>" size="20" maxlength="15" />
                        </td>
                    </tr>
                    <tr>
                        <td height="20" align="left">Senha:</td>
                        <td align="left"><?php if ($n_admsist >= 4) { ?> <a href="resetsenha.php?iduser=<?php echo $iduser; ?>">Definir senha padrão</a><?php }; ?></td>
                    </tr>
                    <tr>
                        <td align="left">E-mail:</td>
                        <td align="left">
                            <input name="email" type="text" class="CaixaTexto" id="email" onblur="lowerMe(this); remacc(this);" value="<?php echo $d_user['email'] ?>" size="50" maxlength="50" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Cargo:</td>
                        <td align="left">
                            <input name="cargo" type="text" class="CaixaTexto" id="cargo" onblur="upperMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_user['cargo'] ?>" size="30" maxlength="30" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Setor:</td>
                        <td align="left">
                            <select name="cod_setor" class="CaixaTexto" id="cod_setor">
                                <option value="" selected="selected">Selecione...</option>
                                <?php while ($dados = $querysetor->fetch_assoc()) { ?>
                                <option value="<?php echo $dados['idsetor']; ?>" <?php echo $dados['idsetor'] == $d_user['cod_setor'] ? 'selected="selected"' : ''; ?>><?php echo $dados['sigla_setor']; ?></option>
                                <?php }; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Iniciais:</td>
                        <td align="left">
                            <input name="iniciais" type="text" class="CaixaTexto" id="iniciais" onblur="lowerMe(this); remacc(this);" onkeypress="return blockChars(event,1);" value="<?php echo $d_user['iniciais'] ?>" size="10" maxlength="6" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">R.S.:</td>
                        <td align="left">
                            <input name="rsuser" type="text" class="CaixaTexto" id="rsuser" onkeypress="return blockChars(event,2);" value="<?php echo $d_user['rsuser'] ?>" size="20" maxlength="12" />
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Ativo:</td>
                        <td align="left">
                            <input name="ativo" type="radio" id="ativo_0" value="1" <?php echo $d_user['ativo'] == 1 ? 'checked="checked"' : ''; ?>/> Sim  &nbsp;&nbsp;
                            <input name="ativo" type="radio" id="ativo_1" value="0" <?php echo empty($d_user['ativo']) ? 'checked="checked"' : ''; ?>/> Não</td>
                    </tr>
                </table>

                <input name="iduser" type="hidden" id="iduser" value="<?php echo $iduser; ?>" />
                <input type="hidden" name="proced" id="proced" value="1" />

                <div class="form_bts">
                    <input class="form_bt" type="submit" name="atualizar" onclick="return validacadastrauser();" value="Atualizar" />
                    <input class="form_bt" type="button" name="" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>


<?php include 'footer.php';?>