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

        $pic_size = 56;

        // Logo
        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'bandeira_sap.jpg';
        $this->Image( $image_file, 134, 5, $pic_size, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        $this->SetX( 10 );
        $this->SetY( 26 );

        $this->Cell( 170, 0.5, '', 'T', 1, 'R', 0, '', 0, true, 'T', 'M' );

        // configurar a fonte
        $this->SetFont( 'helvetica', 'B', 10 );

        // Title
        $coordenadoria = SicopController::getSession ( 'coordenadoria' );
        $unidadecurto  = SicopController::getSession ( 'unidadelongo' );

        $this->SetTextColor(38, 38, 38);

        $this->Cell( 170, 4, $coordenadoria, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );

        $this->SetTextColor(0, 0, 0);
        $this->Cell( 170, 6, $unidadecurto, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );


    }

    // rodapé
    public function Footer() {

        // Position at 15 mm from bottom
        $this->SetY( -15 );

        // configura a fonte
        $this->SetFont( 'helvetica', 'N', 6 );

        $endereco = SicopController::getSession ( 'endereco' );
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

        $txt = $pag_num . "usuário: $iduser; computador: $maquina; em " . date( 'd/m/Y \à\s H:i' );

        $this->Cell( $cell_w, '', $txt, 0, 1, 'R', 0, '', 0, false, 'T', 'M' );

        $this->Cell( 170, 0.5, '', 'T', 1, 'R', 0, '', 0, true, 'T', 'M' );

        // configura a fonte
        $this->SetFont( 'helvetica', 'N', 8 );
        //$this->Cell( 165, 4, $unidadecurto, 0, 1, 'C', 0, '', 0, false, 'T', 'C' );
        //$this->Cell( 165, 10, $endereco, 1, 1, 'C', 0, '', 0, false, 'T', 'C' );
        //$this->writeHTML( '<p align="center">' . $endereco . '</p>', true, 0, true, true );
        $this->writeHTML( $endereco, true, 0, true, true, 'C' );

        //$this->SetAlpha(0.2);

        // Logo
        //$image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'bandeira_rodape.jpg';
        //$this->Image( $image_file, 0, 240, 75, '', 'JPG', '', 'T', false, 72, '', false, false, 0, false, false, false );

        //$image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'bandeira_rodape.png';
        //$this->Image( $image_file, 0, 240, 75, '', 'PNG', '', 'T', false, 72, '', false, false, 0, false, false, false );


    }

    /**
     * para adicionar o '*** CONTINUA ***' com as bordas tracejadas
     */
    public function add_continue_list() {

        // CONFIGURAR A FONTE
        $this->SetFont( 'helvetica', 'BI', 9.5 );

        // CONFIGURAR A BORDA
        $border = array( 'TB' => array( 'width' => 0.4, 'dash' => 4 ) );
        $txt    = '*** CONTINUA ***';
        $this->Cell( 0, 6, $txt, $border, 1, 'C', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar o '*** FIM DA LISTA ***' com as bordas tracejadas
     */
    public function add_end_list() {

        // CONFIGURAR A FONTE
        $this->SetFont( 'helvetica', 'BI', 9.5 );

        // CONFIGURAR A BORDA
        $border = array( 'TB' => array( 'width' => 0.4, 'dash' => 4 ) );
        $txt    = '*** FIM DA LISTA ***';
        $this->Cell( 0, 6, $txt, $border, 1, 'C', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar o '*** CONTINUA ***' com as bordas tracejadas e com as quebras de linha e de página
     */
    public function add_page_continue() {

        $this->ln( 3 );

        // ADICIONAR O *** CONTINUA ***
        $this->add_continue_list();

        // adicionar uma página
        $this->AddPage();

        $this->ln( 3 );

    }

    public function addCompareceu() {

        // imagem compareceu
        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'compareceu.jpg';
        $this->Image( $image_file, 150, 239, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

    }

    public function addDevAss() {

        // imagem compareceu
        $image_file = SICOP_DOC_ROOT . SICOP_SYS_IMG_PATH . 'devass.jpg';
        $this->Image( $image_file, 150, 255, 40, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

    }

    /**
     * para montar o quadrado dos dados do detento, usando nas listas
     * @param $i (int) o número da sequencia da lista
     * @param $altura (int) a altura dos quadros
     */
    public function add_det_grid( $i, $altura = 12 ) {

        $font = $this->font;

        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $font, 'N', 9.5 );

        // DESENHAR OS QUADRADOS PRINCIPAIS
        $border = array( 'TBRL' => array( 'width' => 0.3, 'dash' => 0 ) );
        $this->Cell( 10, $altura, $i, $border, 0, 'C', 0, '', 0, false, 'T', 'M' );
        $this->Cell( 0, $altura, '', $border, 0, 'C', 0, '', 0, false, 'T', 'M' );

        $cur_y = $this->GetY();
        $this->SetY( $cur_y + 0.7 );
        $this->SetX( 30 );

    }

    /**
     * para adicionar a foto do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     */
    public function add_det_pic( $det ) {

        $foto_g   = $det['foto_det_g'];
        $foto_p   = $det['foto_det_p'];

        $foto_det = ck_pic( $foto_g, $foto_p, false, 1, true );

        $cur_y = $this->GetY();

        $this->Image( $foto_det, 162, $cur_y + 0.4, 25, 33, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        $this->SetY( $cur_y );

    }

    /**
     * para adicionar o nome do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     */
    public function add_det_name( $det ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', 9.5 );

        // LEGENDA DETENTO
        $this->SetX( 30 );
        $this->Cell( 15, 0, SICOP_DET_DESC_FU . ':', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $this->font, 'N', 9.5 );

        // CAMPO NOME DO DETENTO
        $this->SetX( 45 );
        $txt = abrevia_texto( $det['nome_det'], 35 );
        $this->Cell( 85, 0, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar a matrícula do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_matr( $det, $x = 121, $fs = 9.5 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA MATRÍCULA
        $this->SetX( $x );
        $this->Cell( 17, 0, 'Matrícula:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $this->font, 'N', $fs );

        // CAMPO MATRÍCULA
        $matr = !empty( $det['matricula'] ) ? formata_num( $det['matricula'] ) : 'N/C' ;
        $this->SetX( $x + 17 );
        $this->Cell( 21, 0, $matr, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar o rg do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_rg( $det, $x = 160, $fs = 9.5 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA RG
        $this->SetX( $x );
        $this->Cell( 9, 0, 'R.G:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $this->font, 'N', $fs );

        // CAMPO RG
        $rg = !empty( $det['rg_civil'] ) ? formata_num( $det['rg_civil'] ) : 'N/C';
        $this->SetX( $x + 8 );
        $this->Cell( 10, 0, $rg, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar a execução do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_exec( $det, $x = 160, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA
        $this->SetX( $x );
        $this->Cell( 9, 0, 'Execução:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $this->font, 'N', $fs );

        // CAMPO
        $rg = !empty( $det['execucao'] ) ? number_format( $det['execucao'], 0, '', '.' ) : 'N/C';

        $add_x = ( $fs == 7 ? 13 : 18 );
        $this->SetX( $x + $add_x );

        $this->Cell( 10, 0, $rg, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar o raio e a cela do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_rc( $det, $x = 168, $fs = 9.5 ) {

        // CONFIGURAR A FONTE PARA NEGRITO
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA RAIO
        $this->SetX( $x );
        $this->Cell( 9, 0, SICOP_RAIO_AB . ':', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // LEGENDA CELA
        $this->SetX( $x + 12 );
        $this->Cell( 9, 0, SICOP_CELA_AB . ':', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );


        // CONFIGURAR A FONTE PARA NORMAL
        $this->SetFont( $this->font, 'N', $fs );

        // CAMPO RAIO
        $this->SetX( $x + 4 );
        $this->Cell( 10, 0, $det['raio'], 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CAMPO CELA
        $this->SetX( $x + 16 );
        $this->Cell( 10, 0, $det['cela'], 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar o nome dos pais do detento nas listas
     * adciona "PAI e MAE", na mesma linha
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_filiacao( $det, $x = 30, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA FILIAÇÃO
        $this->SetX( $x );
        $this->Cell( 15, 0, 'Filiação:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'N', $fs );

        // FILIAÇÃO
        $this->SetX( $x + 11 );
        $this->Cell( 128, 0, $det['pai_det'] . ' e ' . $det['mae_det'], 0, 0, 'L', 0, '', 1, false, 'T', 'M' );

    }

    /**
     * para adicionar o nome dos pais do detento nas listas
     * adciona "PAI <br/> MAE", em linhas diferentes (ml = multi_line)
     * @param $det (array) o array contendo os dados do detento
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_filiacao_ml( $det, $fs = 9.5 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA FILIAÇÃO
        $this->SetX( 30 );
        $this->Cell( 15, 0, 'Filiação:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'N', $fs );

        // FILIAÇÃO
        $this->SetX( 45 );
        $this->Cell( 128, 0, $det['pai_det'], 0, 1, 'L', 0, '', 1, false, 'T', 'M' );

        $this->SetX( 45 );
        $this->Cell( 128, 0, $det['mae_det'], 0, 0, 'L', 0, '', 1, false, 'T', 'M' );

    }

    /**
     * para adicionar a hora da audiência do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_hora_aud( $det, $x = 160, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        $hora_aud = $det['aud_hora_f'];

        if ( !empty( $hora_aud ) ) {
            // LEGENDA E CAMPO HORÁRIO
            $this->SetX( $x );
            $this->Cell( 15, 0, 'Horário: ' . $det['aud_hora_f'] , 0, 0, 'L', 0, '', 0, false, 'T', 'M' );
        }

    }

    /**
     * para adicionar o artigo do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_art( $det, $x = 30, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA ARTIGO
        $this->SetX( $x );
        $this->Cell( 10, 0, 'Artigo(s):', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'N', $fs );

        // para mudar a posição do campo, conforme o tamanho da fonte
        $add_x = ( $fs == 7 ? 12 : 16 );
        $this->SetX( $x + $add_x );

        // CAMPO ARTIGO
        $this->Cell( 52, 0, $det['artigo'], 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar a condenação do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_cond( $det, $x = 83, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // LEGENDA CONDENAÇÃO
        $this->SetX( $x );
        $this->Cell( 17, 0, 'Condenação:', 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'N', $fs );

        // CAMPO CONDENAÇÃO
        $iddet = !empty( $det['cod_detento'] ) ? $det['cod_detento'] : $det['iddetento'];
        $cond  = cal_cond( $iddet );
        $cond  = empty ( $cond ) ? 'N/C' : $cond ;

        // para mudar a posição do campo, conforme o tamanho da fonte
        $add_x = ( $fs == 7 ? 17 : 22 );
        $this->SetX( $x + $add_x );

        $this->Cell( 59, 0, $cond, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }

    /**
     * para adicionar a finalidade da apresentação do detento nas listas
     * @param $det (array) o array contendo os dados do detento
     * @param $x (float) o valor a abcissa
     * @param $det (float) o tamanho da fonte
     */
    public function add_det_finalidade( $det, $x = 160, $fs = 7 ) {

        // CONFIGURAR A FONTE
        $this->SetFont( $this->font, 'B', $fs );

        // CAMPO FINALIDADE TEM QUE SER EM NEGRITO
        $txt = !empty( $det['tipo'] ) ? $det['tipo'] : '';
        $this->SetX( $x );
        $this->Cell( 9, 0, $txt, 0, 0, 'L', 0, '', 0, false, 'T', 'M' );

    }



    public function add_det( $det, $i ) {

        // 1ª LINHA

        // montando os quadrados principais
        $this->add_det_grid( $i );

        // adicionando o nome do detento
        $this->add_det_name( $det );

        // adicionando a matrícula do detento
        $this->add_det_matr( $det );

        // adicionando o RG do detento
        $this->add_det_rg( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------


        // 2ª LINHA

        // adicionando a filiação do detento
        $this->add_det_filiacao( $det );

        // adicionando o horário da apresentação do detento
        $this->add_det_hora_aud( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------


        // 3ª LINHA

        // adicionando os artigos do detento
        $this->add_det_art( $det );

        // adicionando a condenação do detento
        $this->add_det_cond( $det );

        // adicionando a finalidade da apresentação do detento
        $this->add_det_finalidade( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------

    }


    public function add_det_os( $det, $i ) {

        // 1ª LINHA

        // montando os quadrados principais
        $this->add_det_grid( $i );

        // adicionando o nome do detento
        $this->add_det_name( $det );

        // adicionando a matrícula do detento
        $this->add_det_matr( $det, 131 );

        // adicionando o raio e a cela do detento
        $this->add_det_rc( $det, 168 );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------


        // 2ª LINHA

        // adicionando a filiação do detento
        $this->add_det_filiacao( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------


        // 3ª LINHA

        // adicionando os artigos do detento
        $this->add_det_art( $det );

        // adicionando a condenação do detento
        $this->add_det_cond( $det );

        // adicionando a finalidade da apresentação do detento
        $this->add_det_finalidade( $det, 168 );

        // adicionando uma quebra de linha
        $this->Ln();

        //-------------------------------------------
    }

    public function add_det_foto( $det, $i ) {

        // montando os quadrados principais
        $this->add_det_grid( $i, 35 );

        // adicionando a foto do detento
        $this->add_det_pic( $det );

        // adicionando o nome do detento
        $this->add_det_name( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando a matrícula do detento
        $this->add_det_matr( $det, 30 );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando o RG do detento
        $this->add_det_rg( $det, 30 );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando a execução do detento
        $this->add_det_exec( $det, 30, 9.5 );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando a filiação do detento em duas linhas
        $this->add_det_filiacao_ml( $det );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando os artigos do detento
        $this->add_det_art( $det, 30, 9.5 );

        // adicionando uma quebra de linha
        $this->Ln();

        // adicionando a condenação do detento
        $this->add_det_cond( $det, 30, 9.5 );

        // adicionando uma quebra de linha
        $this->Ln();

    }

    public function getQualiDetBasic( $iddet ){

        $det = new Detento( $iddet );
        $d_det = $det->findDetBasic();

        $cell_h = 6;

        // nome
        $txt = '<b>Nome:</b> ' . $d_det->nome_det;
        $this->MultiCell( 150, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Matrícula
        $txt = '<b>Matrícula:</b> ' . $d_det->matricula;
        $this->MultiCell( 40, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // RG
        $txt = '<b>RG:</b> ' . $d_det->rg_civil;
        $this->MultiCell( 40, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // Execução
        $txt = '<b>Execução:</b> ' . $d_det->execucao;
        $this->MultiCell( 40, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // CPF
        $txt = '<b>CPF:</b> ' . $d_det->cpf;
        $this->MultiCell( 50, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Pai
        $txt = '<b>Pai:</b> ' . $d_det->pai_det;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Mae
        $txt = '<b>Mãe:</b> ' . $d_det->mae_det;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Cidade
        $txt = '<b>Cidade:</b> ' . $d_det->cidade;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

    }

    public function getQualiDetFoto( $d_det ){

        $cell_h = 6;

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
        $this->Image( $foto_det, 159 , $cur_y + 1, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false );

        // resetando o Y
        $this->SetY( $cur_y );

        /**
         * ------------------------------------------------------------------------
         */

        // nome
        $txt = '<b>Nome:</b> ' . $d_det->nome_det;// . ' ' . $d_det->nome_det . ' ' . $d_det->nome_det;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Artigo
        $txt = '<b>Artigo:</b> ' . $d_det->artigo;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Nascimento
        $txt = '<b>Nascimento:</b> ' . $d_det->nasc_f;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Cidade
        $txt = '<b>Cidade:</b> ' . $d_det->cidade;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Pai
        $txt = '<b>Pai:</b> ' . $d_det->pai_det;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // Mae
        $txt = '<b>Mãe:</b> ' . $d_det->mae_det;
        $this->MultiCell( 85, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // resetando o Y
        //$y = $this->GetY();

        // setando o Y
        $this->SetY( $cur_y );

        // setando o X
        $this->SetX( 105 );

        // Matrícula
        $txt = '<b>Matrícula:</b> ' . $d_det->matricula;
        $this->MultiCell( 50, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( 105 );

        // RG
        $txt = '<b>RG:</b> ' . $d_det->rg_civil;
        $this->MultiCell( 50, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( 105 );

        // Execução
        $txt = '<b>Execução:</b> ' . $d_det->execucao;
        $this->MultiCell( 50, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // setando o X
        $this->SetX( 105 );

        // CPF
        $txt = '<b>CPF:</b> ' . $d_det->cpf;
        $this->MultiCell( 50, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        $this->SetY( $cur_y + 42 );

//        // CPF
//        $txt = '<b>CPF:</b> ' . $d_det->cpf;
//        $this->MultiCell( 170, $cell_h, $txt, 1, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );


    }

    public function getHeight( $txt ){

        // store current object
        $this->startTransaction();

        // store starting values
        $start_y = $this->GetY();
        $start_page = $this->getPage();

        // call your printing functions with your parameters
        $this->MultiCell( 85, 5, $txt, 1, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // get the new Y
        $end_y = $this->GetY();
        $end_page = $this->getPage();

        // calculate height
        $height = 0;
        if ( $end_page == $start_page ) {

            $height = $end_y - $start_y;

        } else {
            for ( $page = $start_page; $page <= $end_page; ++$page ) {
                $this->setPage( $page );
                if ( $page == $start_page ) {
                    // first page
                    $height = $this->h - $start_y - $this->bMargin;
                } elseif ( $page == $end_page ) {
                    // last page
                    $height = $end_y - $this->tMargin;
                } else {
                    $height = $this->h - $this->tMargin - $this->bMargin;
                }
            }
        }

        // restore previous object
        //$this =
        //$this->rollbackTransaction();

        return $height;

    }

    public function getOfApr( $d_aud, $d_of_model, $diretor ){

        $font = $this->font;

        // configurar a fonte
        $this->SetFont( $font, 'N', 9.5 );

        $cell_h = 6;

        $this->getQualiDetFoto( $d_aud );

        // adicionando uma quebra de linha
        $this->Ln();

        // tratamento superior
        $txt = PAR_INDET_INI . $d_of_model->dest_sup . PAR_INDET_FIM;
        $this->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();
        $this->Ln( $cell_h );

        $idmodel = $d_of_model->idmodel;

        $txt = PAR_INDET_INI . $d_of_model->corpo . ', em ' . $d_aud->data_aud_f . ' às ' . $d_aud->hora_aud_f . 'h';

        if ( $idmodel == 1 or $idmodel == 2 or $idmodel == 3 or $idmodel == 5 ) {

            if ( !empty( $d_of_model->referente ) )
                $txt .= ', ' . $d_of_model->referente;

            if ( !empty( $d_aud->num_processo ) )
                $txt .= ' ' . $d_aud->num_processo ;


        } else if ( $idmodel == 4 ) {

            $txt .= ', para a realização de ' . $d_aud->local_aud;

            if ( !empty( $d_aud->num_processo ) )
                $txt .= ', ' . $d_of_model->referente . ' ' . $d_aud->num_processo ;


        } else if ( $idmodel == 6 ) {

            $txt .= ', para a realização de ' . $d_aud->local_aud;

        } else if ( $idmodel == 7 ) {

            $txt .= ', a fim de ser ' . $d_aud->local_aud;

            if ( !empty( $d_aud->num_processo ) )
                $txt .= ', ' . $d_of_model->referente . ' ' . $d_aud->num_processo ;

        } else if ( $idmodel == 8 ) {

            $txt .= ', ' . $d_of_model->referente . ' ' . $d_aud->num_processo;

        }

        $txt .= '.' . PAR_INDET_FIM;

        // corpo
        $this->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();
        $this->Ln( $cell_h );

        // protestos
        $txt = PAR_INDET_INI . $d_of_model->prostetos . PAR_INDET_FIM;
        $this->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();
        $this->Ln( $cell_h );

        // tratamento
        $txt = PAR_INDET_INI . $d_of_model->tratamento . PAR_INDET_FIM;
        $this->MultiCell( 170, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );


        // adicionando uma quebra de linha
        $this->Ln();
        //$this->Ln( $cell_h );

        // posicionando o ponteiro
        $this->SetY( $this->GetY() + 20 );

        // configurar a fonte
        $this->SetFont( $font, 'I', 9.5 );

        // nome do diretor
        $txt = $diretor->_nome;
        $this->MultiCell( 170, $cell_h, $txt, 0, 'C', false, '', '', '', true, 0, true, true, 0, 'C' );
        //$this->Cell( 0, $cell_h, $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

        // adicionando uma quebra de linha
        $this->Ln();

        // configurar a fonte
        $this->SetFont( $font, 'N', 9.5 );

        // titulo do diretor
        $txt = $diretor->_titulo;
        $this->MultiCell( 170, $cell_h, $txt, 0, 'C', false, '', '', '', true, 0, true, true, 0, 'C' );
        //$this->Cell( 0, $cell_h, $txt, 0, 1, 'C', 0, '', 0, false, 'T', 'M' );

        // compareceu
        $this->addCompareceu();

        $this->SetY( -40 );

        $txt = '';
        if ( $idmodel == 1 or $idmodel == 2 or $idmodel == 3 or $idmodel == 5  or $idmodel == 8 ) {
            $txt = nl2br( $d_of_model->dest_inf ) . ' ' . $d_aud->local_aud;
        } else {
            $txt = nl2br( $d_of_model->dest_inf );
        }

        $txt .= ' de';

        // destino
        //$txt = PAR_INDET_INI . $d_of_model->tratamento . PAR_INDET_FIM;
        $this->MultiCell( 115, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

        // adicionando uma quebra de linha
        $this->Ln();

        // cidade
        $txt = $d_aud->cidade_aud;
        $this->MultiCell( 115, $cell_h, $txt, 0, 'L', false, '', '', '', true, 0, true, true, 0, 'C' );

    }




}
?>