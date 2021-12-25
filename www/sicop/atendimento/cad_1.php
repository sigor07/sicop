<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>form</title>
        <!--<link href="css/reset.css" rel="stylesheet" type="text/css" />-->
        <link href="stl.css" rel="stylesheet" type="text/css" />

    </head>
    <body >

        <div class="no_print">

            <p class="descript_page">CADASTRAR ANTENDENTE</p>

            <div class="block_form">

                <div class="form">
                    <form action="<?php echo SICOP_ABS_PATH ?>send/sendati.php" method="post" name="add_atend" id="add_atend" >
                        <div class="linha">
                            <div class="add_atend_name_leg">
                                Nome:
                            </div>
                            <div class="add_atend_name_field">
                                <input name="local_apr" type="text" class="CaixaTexto" id="local_apr" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                            </div>
                        </div>
                        <div class="linha">
                            <div class="add_atend_name_leg">
                                Nome:
                            </div>
                            <div class="add_atend_name_field">
                                <input name="local_apr" type="text" class="CaixaTexto" id="local_apr" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                            </div>
                        </div>
                        <input name="iddet" type="hidden" id="iddet" value="<?php echo $iddet; ?>" />
                        <input name="proced" type="hidden" id="proced" value="3" />


                        <div class="form_bts">
                            <input name="atualizar" type="submit" value="Cadastrar" />&nbsp;&nbsp;&nbsp;
                            <input name="" type="button" onClick="history.go(-1)" value="Cancelar" />
                        </div>

                    </form><!-- /form name="add_atend" -->

                </div><!-- /div class="form" -->

            </div><!-- /div class="block_form" -->

            <br/><br/><br/>

            <table class="edit">
                <tr>
                    <td class="table_form_label">
                        Nome:
                    </td>
                    <td class="table_form_field">
                        <input name="local_apr" type="text" class="CaixaTexto" id="local_apr" onkeypress="return blockChars(event, 4);" size="60" maxlength="50" />
                    </td>
                </tr>
                <tr>
                    <td class="table_form_label">
                        Nome:
                    </td>
                    <td class="table_form_div">
                        <div class="table_cell_div">
                            <input name="local_apr" type="text" class="CaixaTexto" id="local_apr" onkeypress="return blockChars(event, 4);" size="20" maxlength="50" />
                        </div>
                        <div class="table_cell_div" >
                            <span>asdf adsfadsf</span>
                        </div>
                    </td>
                </tr>
            </table>

<?php include 'footer.php'; ?>