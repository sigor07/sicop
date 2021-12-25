<html >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


        <title>Criar e formatar formulários HTML</title>
        <style >
            body {
                font-family: Verdana, Arial, Helvetica, sans-serif;
                font-size: 11px;
            }
            #form1 legend {
                color: #333366;
                font-weight: bold;
            }
            #form1 fieldset {
                padding: 20px 10px 20px 10px;
                width: 400px;
            }
            #form1 label {
                color: #FF9900;
            }
            .linha {
                vertical-align: middle;
                padding-top: 10px;
                padding-left: 10px;
            }

            input,select,textarea{
                background-color: #f4f4f4;
            }
        </style>

    </head>

    <body>

        <form id="form1" name="form1" method="post" action="">

            <fieldset>
                <legend>Preencha os dados abaixo:</legend>

                <div class="linha">
                    <label>Nome: <br />
                        <input name="nome" type="text" id="nome" size="40" />
                    </label>
                </div>

                <div class="linha">
                    <label>
                        Email:
                        <input name="email" type="text" id="email" size="40" />
                    </label>
                </div>

                <div class="linha">
                    <label>
                        Sexo:
                        <input name="masc" type="radio" value="masculino" />Masculino
                    </label>
                    <label>
                        <input name="fem" type="radio" value="feminino" />Feminino
                    </label>
                </div>

                <div class="linha">
                    <label>Estado:
                        <select name="estado" id="estado">
                            <option>SP</option>
                            <option>MG</option>
                            <option>RJ</option>
                            <option>ES</option>
                        </select>
                    </label>
                </div>

                <div class="linha">
                    <label>Comentários: <br />
                        <textarea name="comentarios" cols="30" rows="4" id="comentarios"></textarea>
                    </label>
                </div>

                <div class="linha">
                    <label><input name="newsletter" type="checkbox" id="newsletter" value="sim" />
                        Quero receber a newsletter deste site
                    </label>
                </div>

                <div class="linha">
                    <label><input name="Enviar" type="submit" id="Enviar" value="Enviar" />
                    </label>
                </div>

            </fieldset>

        </form>
    </body>
</html>