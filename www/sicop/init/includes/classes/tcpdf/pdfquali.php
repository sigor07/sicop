<?php

/**
 * extensão da classe tcpdf contendo o cabeçalho e rodapé da unidade.
 * em arquivos que contenham cabeçalho e rodapé, deve-se chamar este arquivo
 * ao invés do tcpdf.php
 */

require_once('config/lang/bra.php');
require_once('tcpdf.php');

// estende a classe TCPDF para criar um cabeçalho e rodapé personalizados
class PDF extends TCPDF {

    /**
     * a fonte usada
     * @access private
     * @var string
     */
    private $font = 'helvetica';


    //cabeçalho
    public function Header() {

        $font = $this->font;

        // Logo
        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'brasao.jpg';
        $this->Image( $image_file, 20, 7, 23, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'logo_sap.jpg';
        $this->Image( $image_file, 170, 10, 28, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        $this->SetX( 10 );
        $this->SetY( 8 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 10 );

        // Title
        $secretaria    = SicopController::getSession ( 'secretaria' );
        $coordenadoria = SicopController::getSession ( 'coordenadoria' );
        $unidadecurto  = SicopController::getSession ( 'unidadecurto' );

        $this->Cell( 190, 6, $secretaria, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );
        $this->Cell( 190, 6, $coordenadoria, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );
        $this->Cell( 190, 6, $unidadecurto, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );

        $endereco = SicopController::getSession ( 'endereco' );

        $this->SetFont( 'helvetica', 'N', 8 );
        $this->MultiCell( 100, 7, $endereco, 0, 'C', 0, '', 55, 27 );


    }

    // rodapé
    public function Footer() {

        // Position at 15 mm from bottom
        $this->SetY( -15 );

        // configura a fonte
        $this->SetFont( 'helvetica', 'N', 6 );


        $iduser   = SicopController::getSession ( 'user_id' );
        $ip       = $_SERVER['REMOTE_ADDR'];
        $maquina  = substr( $ip, strrpos( $ip, '.' ) + 1 );

        if ( !defined( 'ADD_NUM_PAG' ) ) define( 'ADD_NUM_PAG', FALSE );

        $pag_num = '';
        $cell_w  = 0;

        if ( ADD_NUM_PAG ) {
            $pag_num = 'Página ' . $this->getPage() . '/' . $this->getAliasNbPages() . '; ';
            $cell_w  = 174.4; // aumento do tamanho da celula por que, por um erro desconhecido, é colocado um espaçamento no fim da linha
        }

        $txt = SICOP_SYS_NAME . ' - ' . $pag_num . "usuário: $iduser; computador: $maquina; em " . date( 'd/m/Y \à\s H:i' );

        $this->Cell( $cell_w, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * desenha a planilha das digitais
     * $mao int a mão que vai ser desenhada $mao = 1 => mão direita; $mao = 2 => mão esquerda
     * $fim_pag bool se a planilha vai ser colocada no fim da página. se for false, a planilha vai acompanhar o fluxo do texto
     */
    public function getDigital( $mao = 1, $fim_pag = true ) {

        $font = $this->font;

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        /**
         * ------------------------------------------------------------------------
         * QUADROS DA DIGITAL
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        /**
         * se $fim_pag for false, vai pegar o Y atual
         * se não ele vai colocar à 67mm do fim da página
         */
        $fim_pag = (bool)$fim_pag;
        $y = -67;
        if ( !$fim_pag ) $y = $this->GetY();

        $this->SetY( $y );

        // definindo o valor de X
        $x = 10;

        // posicionando o ponteiro
        $this->SetX( $x );

        for ( $index = 0; $index < 15; $index++ ) {

            // DESENHAR O QUADRADO DOS IDENTIFICADORES
            $this->Cell( 12, 10, '', 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        }


        // quebra de linha
        $this->Ln();

        // posicionando o ponteiro
        $this->SetX( $x );

        for ( $i = 1; $i < 6; $i++ ) {

            $txt = '';
            switch ( $i ){
                case 1:
                    $txt = 'Polegar';
                    break;
                case 2:
                    $txt = 'Indicador';
                    break;
                case 3:
                    $txt = 'Médio';
                    break;
                case 4:
                    $txt = 'Anular';
                    break;
                case 5:
                    $txt = 'Mínimo';
                    break;


            }

            // DESENHAR O QUADRADO DAS DIGITAIS
            $this->Cell( 36, 40, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'T' );

        }



        /**
         * ------------------------------------------------------------------------
         * IMAGEM MÃO DIREITA/ESQUERDA
         * ------------------------------------------------------------------------
         */

        // setando o Y
        $this->SetY( $y );

        // posicionando o ponteiro
        $this->SetX( 190 );

        // DESENHAR O QUADRADO DA FOTO
        $this->Cell( 10, 50, '', 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        // pegando o Y atual
        $cur_y = $this->GetY();

        /**
         * $mao = 1 => mão direita
         * $mao = 2 => mão esquerda
         */

        $file_name = 'mao_d.png';

        if ( $mao == 2 ) $file_name = 'mao_e.png';

        // imagem
        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . $file_name;

        // colocando a imagem
        $this->Image( $image_file, 192, $cur_y + 8, '', 30, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        // quebra de linha
        if ( !$fim_pag ) $this->Ln(42);

    }

    public function getQuali( $d_det, $servidor = '' ) {

        $font = $this->font;

        // configurar a fonte
        $this->SetFont( $font, 'B', 14 );

        $txt = 'FICHA QUALIFICATIVA';
        $this->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

        if ( $d_det->tipo_mov_in == 2 ) {

            // configurar a fonte
            $this->SetFont( $font, 'B', 10 );

            $txt = SICOP_DET_DESC_U . ' EM TRANSITO NA UNIDADE';
            $this->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

        }

        // posicionando o ponteiro
        $this->SetX( 156.5 );

        // DESENHAR O QUADRADO DA FOTO
        $border = array( 'TBRL' => array( 'width' => 0.3, 'dash' => 0 ) );
        $this->Cell( 43.5, 52, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );


        // definindo a altura das celulas
        $cell_h = 6.5;

        // largura da celula da esquerda
        $cell_l_w = 97;

        // largura da celula grande da direita
        $cell_r_g_w = 93;

        // largura da celula pequena da direita
        $cell_r_p_w = 49.5;

        // largura da celula de linha inteira
        $cell_w = 190;




        /**
         * ------------------------------------------------------------------------
         * FOTO
         * ------------------------------------------------------------------------
         */

        $foto_g   = $d_det->foto_det_g;
        $foto_p   = $d_det->foto_det_p;

        $foto_det = Detento::ckPic( $foto_g, $foto_p, false, 1, true );

        // pegando o Y atual
        $cur_y = $this->GetY();

        // colocando a foto
        $this->Image( $foto_det, 160, $cur_y + 1, 37, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        // resetando o Y
        $this->SetY( $cur_y );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * NOME
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Nome:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 20 );

        // texto
        $txt = $d_det->nome_det;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * MATRÍCULA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Matrícula:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 121 );

        // texto
        $txt = $d_det->matricula;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();


        /**
         * -------------------------------- linha 2 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * ARTIGO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Artigo:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 20 );

        // texto
        $txt = $d_det->artigo;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * RG
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'RG:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 113 );

        // texto
        $txt = $d_det->rg_civil;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 3 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * VULGO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Vulgo(s):';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 23 );

        // texto
        $txt = $d_det->vulgo;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * EXECUÇÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Execução:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 122 );

        // texto
        $txt = $d_det->execucao;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 4 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * NACIONALIDADE
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Nacionalidade:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 31 );

        // texto
        $txt = $d_det->nacionalidade;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * CPF
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'CPF:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 115 );

        // texto
        $txt = $d_det->cpf;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();


        /**
         * -------------------------------- linha 5 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * DATA DE NASCIMENTO / IDADE
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Data de nascimento:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 39 );

        // texto
        $txt = $d_det->nasc_f;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * ESTADO CIVIL
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Estado Civil:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 125 );

        // texto
        $txt = $d_det->est_civil;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();


        /**
         * -------------------------------- linha 6 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CIDADE - ESTADO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Cidade:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 21 );

        // texto
        $txt = $d_det->cidade;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * PRIMÁRIO / REINCIDENTE
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Primário:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 120 );

        // texto
        $txt = $d_det->primario;
        $this->Cell( 35.5, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 7 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * PAI
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Pai:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 16 );

        // texto
        $txt = $d_det->pai_det;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * DADOS PROVISÓRIOS
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = $d_det->dados_prov;
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();


        /**
         * -------------------------------- linha 8 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * MÃE
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Mãe:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 17 );

        // texto
        $txt = $d_det->mae_det;
        $this->Cell( 87, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * EM BRANCO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = '';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 9 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * PROFISSÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Profissão:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 25 );

        // texto
        $txt = $d_det->profissao;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * INSTRUÇÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Instrução:';
        $this->Cell( $cell_r_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 122 );

        // texto
        $txt = $d_det->escolaridade;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 10 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * PROCEDÊNCIA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Procedência:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 29 );

        // texto
        $txt = $d_det->procedencia;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * INCLUSÃO | PRISÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Inclusão:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 120 );

        // texto
        $txt = $d_det->data_incl_f;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 156.5 );

        // legenda
        $txt = 'Prisão:';
        $this->Cell( 43.5, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 167 );

        // texto
        $txt = $d_det->data_prisao;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );



        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 11 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CONDENAÇÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Condenado a:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 30 );

        // texto
        $txt = $d_det->cond;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * SITUAÇÃO PROCESSUAL
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Sit. processual:';
        $this->Cell( $cell_r_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 129 );

        // texto
        $txt = $d_det->sit_proc;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();




        /**
         * -------------------------------- linha 12 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * PRISÕES ANTERIORES
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // texto
        $txt = '<b>Prisões anteriores:</b> ' . $d_det->prisoes_ant;

        //MultiCell( $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false ) {
        $this->MultiCell( $cell_w, $cell_h, $txt, 1, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 13 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * FUGA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Fuga(s):';
        $this->Cell( 170, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 22 );

        // texto
        $txt = $d_det->local_fuga;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * PESO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 180 );

        // legenda
        $txt = 'Peso:';
        $this->Cell( '', $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 189 );

        // texto
        $txt = $d_det->peso . ' kg';
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 14 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CUTIS
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Cutis:';
        $this->Cell( 30, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 19 );

        // texto
        $txt = $d_det->cutis;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CABELOS
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 40 );

        // legenda
        $txt = 'Cabelos:';
        $this->Cell( 67, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 53 );

        // texto
        $txt = $d_det->cabelos;
        $this->Cell( 82, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * OLHOS
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Olhos:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 117 );

        // texto
        $txt = $d_det->olhos;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * ESTATURA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 156.5 );

        // legenda
        $txt = 'Estatura:';
        $this->Cell( 43.5, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 170 );

        // texto
        $txt = $d_det->estatura;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 15 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * SUBTITULO - SINAIS PARTICULARES
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // set color for background
        $this->SetFillColor(192,192,192);

        // texto
        $txt = 'SINAIS PARTICULARES';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'C', 1, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 16 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * DEFEITOS FÍSICOS
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Defeito(s) físico(s):';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 36 );

        // texto
        $txt = $d_det->defeito_fisico;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 17 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * NASCIMENTO
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Nascimento:';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 28 );

        // texto
        $txt = $d_det->sinal_nasc;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 18 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CICATRIZ
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Cicatriz(es):';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 27 );

        // texto
        $txt = $d_det->cicatrizes;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 19 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * TATUAGENS
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Tatuagem(ns):';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 30 );

        // texto
        $txt = $d_det->tatuagens;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 20 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * SUBTITULO - OUTRAS INFORMAÇÕES
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // set color for background
        $this->SetFillColor(192,192,192);

        // texto
        $txt = 'OUTRAS INFORMAÇÕES';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'C', 1, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 21 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * ENDEREÇO
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Endereço:';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 25 );

        // texto
        $txt = $d_det->resid_det;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 22 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * CASO EMERGÊNCIA
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Em caso de emergência, avisar:';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 54 );

        // texto
        $txt = $d_det->caso_emergencia;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 23 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * TATUAGENS
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Outros artigos:';
        $this->Cell( 190, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 31 );

        // texto
        $txt = $d_det->obs_artigos;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        /**
         * -------------------------------- linha 24 -------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * TATUAGENS
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'b', 8 );

        // legenda
        $txt = 'Possui advogado particular:';
        $this->Cell( $cell_l_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 49 );

        // texto
        $txt = $d_det->possui_adv;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         * RELIGIÃO
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 107 );

        // legenda
        $txt = 'Religião:';
        $this->Cell( $cell_r_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 120 );

        // texto
        $txt = $d_det->religiao;
        $this->Cell( '', $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * JALECO / CALÇA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 156.5 );

        // texto
        $txt = $d_det->jaleco . $d_det->calca;
        $this->Cell( 43.5, $cell_h, $txt, 1, 0, 'R', 0, '', 0, false, 'T', 'M' );


        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();



        if ( $d_det->tipo_mov_in != 2 ) {

            /**
             * ------------------------------- Declaração -----------------------------
             */

            /**
             * ------------------------------------------------------------------------
             * DECLARAÇÃO
             * ------------------------------------------------------------------------
             */


            // posicionando o ponteiro
            $this->SetX( 10 );

            // pegando o Y atual
            $cur_y = $this->GetY();

            // setando o Y
            $this->SetY( $cur_y + 1 );

            // configurar a fonte
            $this->SetFont( $font, 'N', 7 );

            // texto
            $txt = 'Declaro estar ciente das normas deste estabelecimento penal; de que recebi todos os pertences autorizados e os demais serão devolvidos a
                    Cadeia/Delegacia/Unidade de origem; de que recebi o uniforme padrão e deverei devolvê-lo quando sair desta unidade, seja por liberdade ou
                    transferência.';

            //MultiCell( $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false ) {
            $this->MultiCell( $cell_w, 7, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

            /**
             * ------------------------------------------------------------------------
             */

            // quebra de linha
            $this->Ln(6);

        }




        /**
         * --------------------------------- Data ---------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * DATA
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // legenda
        $cidade   = SicopController::getSession( 'cidade' );
        $txt = $cidade . ', ' . date( 'd/m/Y' );
        $this->Cell( 190, 5, $txt, 0, 0, 'R', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln();




        /**
         * ----------------------------- assinaturas ------------------------------
         */

        // pegando o Y atual
        $cur_y = $this->GetY();

        /**
         * ------------------------------------------------------------------------
         * ASSINATURA DO FUNCIONÁRIO
         * ------------------------------------------------------------------------
         */

        // posicionando o ponteiro
        $this->SetX( 10 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // linha
        $txt = '';
        $this->Cell( 70, 1, $txt, 'B', 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();

        // responsavel
        $txt = 'Servidor Responsável';
        $this->Cell( 70, 4, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();

        // responsavel
        $txt = 'Nome: ' . $servidor;
        $this->Cell( 70, 4, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * ASSINATURA DO DETENTO
         * ------------------------------------------------------------------------
         */


        // posicionando o ponteiro
        $this->SetY( $cur_y );

        // posicionando o ponteiro
        $this->SetX( 130 );


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // linha
        $txt = '';
        $this->Cell( 70, 1, $txt, 'B', 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();

        // posicionando o ponteiro
        $this->SetX( 130 );

        // responsavel
        $txt = 'O detento';
        $this->Cell( 70, 4, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();

        /**
         * ------------------------------------------------------------------------
         */

    }

    public function getFichaIdent( $d_det ) {

        // altura das celulas
        $cell_h = 6.5;

        // altura das celulas do identificador e pesquisador
        $cell_g_h = $cell_h * 2.5;

        // largura das celulas
        $cell_w = 37;

        // largura das celulas menores
        $cell_p_w = $cell_w / 2;

        // largura das celulas maiores
        $cell_g_w = 91;



        $font = $this->font;

        // configurar a fonte
        $this->SetFont( $font, 'B', 14 );

        $txt = 'FICHA DE IDENTIFICAÇÃO';
        $this->Cell( 0, '', $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

        $y_line_1 = $this->GetY();

        /**
         * ------------------------------------------------------------------------
         * DATA DA FICHA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Data da ficha';
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Origem (Unidade)';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Registro geral';
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // quebra de linha
        $this->Ln();


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // texto
        $txt = date( 'd/m/Y' );
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = SicopController::getSession ( 'unidadecurto' );
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = $d_det->rg_civil;
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // quebra de linha
        $this->Ln();


        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // legenda
        $txt = 'Sexo';
        $this->Cell( $cell_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Cor';
        $this->Cell( $cell_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Motivo da Identificação (Civil ou Criminal)';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Data de nascimento';
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // quebra de linha
        $this->Ln();


        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // posicionando o ponteiro
        $this->SetX( 10 );

        // texto
        $txt = SICOP_DET_SEX_F;
        $this->Cell( $cell_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = $d_det->cutis;
        $this->Cell( $cell_p_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = 'Artigo: ' . $d_det->artigo;
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        $txt = $d_det->nasc_det_f;
        $this->Cell( $cell_w, $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // quebra de linha
        $this->Ln();


        /**
         * ------------------------------------------------------------------------
         * NOME
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        $txt = 'Nome: ';
        $this->Cell( $cell_w + $cell_g_w , $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // posicionando o ponteiro
        $this->SetX( 20 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        $txt = $d_det->nome_det;
        $this->Cell( 118, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


        /**
         * ------------------------------------------------------------------------
         * MATRÍCULA
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        $txt = 'Matrícula: ';
        $this->Cell( $cell_w , $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // posicionando o ponteiro
        $this->SetX( 152 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        $txt = $d_det->matricula;
        $this->Cell( $cell_w, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();


        /**
         * ------------------------------------------------------------------------
         * PAI
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        $txt = 'Pai: ';
        $this->Cell( $cell_w + $cell_g_w , $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // posicionando o ponteiro
        $this->SetX( 16 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        $txt = $d_det->pai_det;
        $this->Cell( 118, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln();

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * MAE
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        $txt = 'Mãe: ';
        $this->Cell( $cell_w + $cell_g_w , $cell_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // posicionando o ponteiro
        $this->SetX( 17 );

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        $txt = $d_det->mae_det;
        $this->Cell( 118, $cell_h, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        /**
         * ------------------------------------------------------------------------
         */

        /**
         * ------------------------------------------------------------------------
         * QUADRADOS DO INDENTIFICADOR, PESQUISADOR E ASSINATURA
         * ------------------------------------------------------------------------
         */

        // pegando o Y atual para retorna-lo depois
        $cur_y = $this->GetY();

        // definindo x
        // 2x altura da celula normal + celula grande + 10 da margem
        $x = $cell_w * 2 + $cell_g_w + 10;

        // posicionando o ponteiro
        $this->SetXY( $x, $y_line_1 );

        // quadrado do identificador
        $txt = 'Identificador';
        $this->Cell( 25, $cell_g_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'T' );


         // quebra de linha
        $this->Ln();


        // posicionando o ponteiro
        $this->SetX( $x );

        // quadrado do pesquisador
        $txt = 'Pesquisador';
        $this->Cell( 25, $cell_g_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'T' );


         // quebra de linha
        $this->Ln();


        // alterando o valor de X
        $x = $x - $cell_w;

        // posicionando o ponteiro
        $this->SetX( $x );

        // quadrado da assinatura
        $txt = 'Assinatura do identificado';
        $this->Cell( 25 + $cell_w, $cell_h * 2, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'T' );

        // resetando o Y
        $this->SetY( $cur_y );

        /**
         * ------------------------------------------------------------------------
         */


        /**
         * ------------------------------------------------------------------------
         * PLANILHAS SUPERIORES
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'B', 8 );

        $cell_g_w = 75;

        $cell_p_w = 40;

        $cell_h   = 5;

        // quebra de linha
        $this->Ln(5);

        // linha
        $txt = '';
        $this->Cell( 190, 1, $txt, 'B', 0, 'L', 0, '', 0, false, 'T', 'M' );

        // quebra de linha
        $this->Ln(6);

        $txt = 'MÃO ESQUERDA';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        $txt = 'POLEGARES';
        $this->Cell( $cell_p_w, $cell_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        $txt = 'MÃO DIREITA';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( $font, 'N', 7 );

        // altura das celulas
        $cell_h   = 60;

        // quebra de linha
        $this->Ln();

        // pegando o Y atual para retorna-lo depois
        $cur_y = $this->GetY();

        // quadrado da mão esquerda
        $txt = '';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );

        // pegando o X atual
        $cur_x = $this->GetX();

        // quadrado do polegar direito
        $txt = 'DIREITO';
        $this->Cell( $cell_p_w , $cell_h / 2, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'T' );

        // quadrado da mão direita
        $txt = '';
        $this->Cell( $cell_g_w, $cell_h, $txt, 1, 0, 'C', 0, '', 0, false, 'T', 'M' );


        $y = $cur_y + $cell_h / 2;

        // posicionando o ponteiro
        $this->SetXY( $cur_x, $y );

        // quadrado do polegar direito
        $txt = 'ESQUERDO';
        $this->Cell( $cell_p_w , $cell_h / 2, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'T' );


        /**
         * ------------------------------------------------------------------------
         */

        // quebra de linha
        $this->Ln(30);

        $txt = '';
        $this->Cell( 190, 7, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


        /**
         * ------------------------------------------------------------------------
         */


    }

    public function getCartaoIdent( $d_det ) {

        $font = $this->font;

        // altura das celulas
        $cell_h = 5;

        // altura da celula principal
        $cell_main_h = 54;

        // altura das celulas do identificador e pesquisador
        $cell_w = 95;

        // pegando o Y inicial
        $y_ini = $this->GetY();

        // pegando o X inicial
        $x_ini = $this->GetX();


        // desenhando o quadrado principal
        $txt = '';
        $this->Cell( $cell_w, $cell_main_h, $txt, 1, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // pegando o Y final
        $y_fim = $this->GetY();

        // pegando o X final
        $x_fim = $this->GetX();

        /**
         * ------------------------------------------------------------------------
         * FOTO
         * ------------------------------------------------------------------------
         */

        $foto_g   = $d_det->foto_det_g;
        $foto_p   = $d_det->foto_det_p;

        $foto_det = Detento::ckPic( $foto_g, $foto_p, false, 1, true );

        // pegando o Y atual
        $cur_y = $this->GetY();

        // pegando o X atual
        $cur_x = $this->GetX();

        // colocando a foto
        $this->Image( $foto_det, $cur_x - 34 , $cur_y + 1, 33, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        // resetando o Y
        $this->SetY( $cur_y );

        /**
         * ------------------------------------------------------------------------
         */

        // configurar a fonte
        $this->SetFont( $font, 'N', 8 );

        // setando o Y
        $this->SetY( $y_ini + 1 );

        // setando o X
        $this->SetX( $x_ini );

        // nome
        $txt = '<b>Nome:</b> ' . $d_det->nome_det;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // matricula
        $txt = '<b>Matrícula:</b> ' . $d_det->matricula;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // matricula
        $txt = '<b>Nascimento:</b> ' . $d_det->nasc_f;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // inclusão
        $txt = '<b>Inclusão:</b> ' . $d_det->data_incl_f;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // procedência
        $txt = '<b>Procedência:</b> ' . $d_det->procedencia;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // Vulgo
        $txt = '<b>Vulgo(s):</b> ' . $d_det->vulgo;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // Pai
        $txt = '<b>Pai:</b> ' . $d_det->pai_det;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( $x_ini );

        // Mãe
        $txt = '<b>Mãe:</b> ' . $d_det->mae_det;
        $this->MultiCell( 60, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // quebra de linha
        $this->Ln();

        // setando o Y
        $this->SetY( $y_ini + 48 );

        // setando o X
        $this->SetX( $x_ini );

        // configurar a fonte
        $this->SetFont( $font, 'B', 10 );



        // desenhando o quadrado principal
        $txt = 'RAIO:______________  CELA:______________   ' . $d_det->jaleco . $d_det->calca ;
        $this->Cell( $cell_w, $cell_h, $txt, 0, 0, 'C', 0, '', 0, false, 'T', 'M' );

        // setando o Y
        $this->SetY( $y_fim );

        // setando o X
        $this->SetX( $x_fim );



    }

}
?>