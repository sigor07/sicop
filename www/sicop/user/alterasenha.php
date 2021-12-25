<?php
if ( !isset( $_SESSION ) ) session_start();

require '../init/config.php';
require 'incl_complete.php';

$pag = link_pag();
$tipo = '';

$iduser = get_session( 'user_id', 'int' );

$mensagem = "Acesso à página $pag";
salvaLog( $mensagem );

$desc_pag = 'Alterar senha';

$query = "SELECT
            `sicop_users`.`iduser`,
            `sicop_users`.`nomeuser`
          FROM
            `sicop_users`
          WHERE
            `sicop_users`.`iduser` = $iduser";

// instanciando o model
$model = SicopModel::getInstance();

// executando a query
$query = $model->query( $query );

// fechando a conexao
$model->closeConnection();

$dados = $query->fetch_assoc();

// adicionando o javascript
$cab_js = 'valida.js';
set_cab_js( $cab_js );

require 'cab.php';
?>

            <p class="descript_page">ALTERAÇÃO DE SENHA</p>

            <table class="lista_busca">
                <tr bgcolor="#FAFAFA">
                    <td width="70" height="20">Usuário:</td>
                    <td width="275"><?php echo $dados['nomeuser'] ?></td>
                </tr>
                <tr bgcolor="#FAFAFA">
                    <td height="20">ID:</td>
                    <td><?php echo $dados['iduser'] ?></td>
                </tr>
            </table>

            <form action="<?php echo SICOP_ABS_PATH ?>send/sendaltsenha.php" method="post" name="senha" id="senha" onSubmit="return validasenha();">

                <table class="edit">
                    <tr >
                        <td width="135" height="20">Digite a nova senha:</td>
                        <td width="225"><input name="nova_senha" type="password" class="CaixaTexto" id="nova_senha" size="20" maxlength="15" /> De 6 à 15 carac.</td>

                    </tr>
                    <tr >
                        <td height="20">Redigite a nova senha:</td>
                        <td><input name="conf_senha" type="password" class="CaixaTexto" id="conf_senha" size="20" maxlength="15" /></td>
                    </tr>
                    <tr >
                        <td height="20">Digite a senha atual:</td>
                        <td><input name="senha_atual" type="password" class="CaixaTexto" id="senha_atual" size="20" maxlength="15" /></td>
                    </tr>
                </table>

                <script type="text/javascript">id('nova_senha').focus();</script>
                <input name="iduser" type="hidden" id="iduser" value="<?php echo $dados['iduser'] ?>" />

                <div class="form_bts">
                    <input class="form_bt" name="atualizar" type="submit" value="Atualizar" />
                    <input class="form_bt" name="" type="button" onclick="history.go(-1)" value="Cancelar" />
                </div>

            </form>

<?php include 'footer.php'; ?>