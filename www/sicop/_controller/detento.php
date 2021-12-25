<?php

/**
 * controller para detentos
 *
 * @author Rafael
 * @since 29/02/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */
class Detento {


    /**
     * o id do detento
     * @access private
     * @var string
     */
    private $_iddet;

    /**
     * construtor da classe
     */
    public function __construct( $iddet = '' ) {

        $iddet = (int)$iddet;

        if ( !empty ( $iddet ) ) {
            $this->__set( '_iddet', $iddet );
        }

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }


    public static function ckPic( $foto_g, $foto_p, $reverse = false, $file_flag = 1, $pdf = false ) {

        $file_flag = (int)$file_flag;

        if ( empty ( $file_flag ) or $file_flag > 2 ) {
            $file_flag = 1;
        }

        $img_path_comp = '';

        if ( $pdf ) {
            $img_path_comp = SICOP_DOC_ROOT;
        }

        $img_path = $img_path_comp . SICOP_DET_IMG_PATH;
        $pasta    = SICOP_DET_FOLDER;

        if ( $file_flag == 2 ) {

            $img_path = $img_path_comp . SICOP_VISIT_IMG_PATH;
            $pasta    = SICOP_VISIT_FOLDER;

        }

        $no_photo    = 'nophoto.jpg';

        $file_foto_p = $pasta . $foto_p;

        $file_foto_g = $pasta . $foto_g;

        $foto_det    = $img_path . $foto_p;

        // se não for reverse retorna a foto pequena...
        if ( !$reverse ) {

            if ( empty( $foto_p ) || !is_file( $file_foto_p ) ) {

                $foto_det = $img_path . $foto_g;

                if ( empty( $foto_g ) || !is_file( $file_foto_g ) ) {

                    $img_path = $img_path_comp . SICOP_SYS_IMG_PATH;
                    $foto_det = $img_path . $no_photo;

                }

            }

        } else { // se for reverse retorna a foto grande

            $foto_det    = $img_path . $foto_g;

            if ( empty( $foto_g ) || !is_file( $file_foto_g ) ) {

                $foto_det = $img_path . $foto_p;

                if ( empty( $foto_p ) || !is_file( $file_foto_p ) ) {

                    $img_path = SICOP_SYS_IMG_PATH;
                    $foto_det = $img_path . $no_photo;

                }

            }

        }

        return $foto_det;

    }

    public function upFunc( $iduser ){

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        $iduser = (int)$iduser;

        if ( empty ( $iduser ) ) $iduser = 'NULL';

        // instanciando o model
        $detmodel = new DetentoModel( $iddet );

        // executando a query
        $q_det = $detmodel->upFunc( $iduser );

        return $q_det;

    }

    public function findQuali(){

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        // instanciando o model
        $detmodel = new DetentoModel( $iddet );

        // pegando os dados
        $q_det = $detmodel->getDetQuali();

        if ( !$q_det ) return FALSE;

        $dados = $q_det->fetch_object();

        $dados->matricula_sn = $dados->matricula;

        $dados->matricula = $this->formataNum( $dados->matricula );

        $dados->rg_civil_sn = $dados->rg_civil;

        $dados->rg_civil = $this->formataNum( $dados->rg_civil );

        $dados->execucao_sn = $dados->execucao;

        $dados->execucao = $this->formataNum( $dados->execucao, 3 );

        $dados->cpf_sn = $dados->cpf;

        $dados->cpf = $this->formataNum( $dados->cpf, 2 );

        $dados->nasc_f = $this->formataDataNasc( $dados->nasc_det_f, $dados->idade_det );

        $dados->cidade = !empty( $dados->cidade ) ? $dados->cidade . ' - ' . $dados->estado : '';

        $dados->primario = SicopController::tratasn( $dados->primario );

        $dados->dados_prov = !empty( $dados->dados_prov ) ? 'DADOS PROVISÓRIOS' : '';

        $dados->cond = $this->calCond();

        $dados->local_fuga = !empty( $dados->local_fuga ) ? $dados->local_fuga : 'NADA CONSTA';

        $dados->estatura = preg_replace( '/([0-9]{1})([0-9]{2})/', '\\1,\\2', $dados->estatura );

        $dados->possui_adv = SicopController::tratasn( $dados->possui_adv );

        $dados->jaleco = !empty( $dados->jaleco ) ? 'J' : '';

        $dados->calca = !empty( $dados->calca ) ? 'C' : '';

        return $dados;

    }

    public function findQualiM( $ids = array() ){

        if ( empty ( $ids ) or !is_array( $ids ) ) return FALSE;

        $ids_v = array();
        foreach ( $ids as &$value ) {

            $value = (int)$value;
            if ( empty ( $value ) ) continue;
            $ids_v[] = (int)$value;

        }

        if ( empty ( $ids_v ) ) return FALSE;

        $ids_v = implode( ',', $ids_v );

        // instanciando o model
        $detmodel = new DetentoModel();

        // pegando os dados
        $q_det = $detmodel->getDetQuali( $ids_v );

        if ( !$q_det ) return FALSE;

        $det = array();

        while ( $dados = $q_det->fetch_object() ) {

            $dados->matricula_sn = $dados->matricula;

            $dados->matricula = $this->formataNum( $dados->matricula );

            $dados->rg_civil_sn = $dados->rg_civil;

            $dados->rg_civil = $this->formataNum( $dados->rg_civil );

            $dados->execucao_sn = $dados->execucao;

            $dados->execucao = $this->formataNum( $dados->execucao, 3 );

            $dados->cpf_sn = $dados->cpf;

            $dados->cpf = $this->formataNum( $dados->cpf, 2 );

            $dados->nasc_f = $this->formataDataNasc( $dados->nasc_det_f, $dados->idade_det );

            $dados->cidade = !empty( $dados->cidade ) ? $dados->cidade . ' - ' . $dados->estado : '';

            $dados->primario = SicopController::tratasn( $dados->primario );

            $dados->dados_prov = !empty( $dados->dados_prov ) ? 'DADOS PROVISÓRIOS' : '';

            $dados->cond = $this->calCond();

            $dados->local_fuga = !empty( $dados->local_fuga ) ? $dados->local_fuga : 'NADA CONSTA';

            $dados->estatura = preg_replace( '/([0-9]{1})([0-9]{2})/', '\\1,\\2', $dados->estatura );

            $dados->possui_adv = SicopController::tratasn( $dados->possui_adv );

            $dados->jaleco = !empty( $dados->jaleco ) ? 'J' : '';

            $dados->calca = !empty( $dados->calca ) ? 'C' : '';

            $det[] = $dados;

        }

        return $det;

    }

    public function findIdent(){

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;

        // instanciando o model
        $detmodel = new DetentoModel( $iddet );

        // pegando os dados
        $q_det = $detmodel->getDetIndent();

        if ( !$q_det ) return FALSE;

        $dados = $q_det->fetch_object();

        $dados->matricula_sn = $dados->matricula;

        $dados->matricula = $this->formataNum( $dados->matricula );;

        $dados->rg_civil_sn = $dados->rg_civil;

        $dados->rg_civil = $this->formataNum( $dados->rg_civil );

        return $dados;

    }

    public function findIdentM( $ids = array() ){

        if ( empty ( $ids ) or !is_array( $ids ) ) return FALSE;

        $ids_v = array();
        foreach ( $ids as &$value ) {

            $value = (int)$value;
            if ( empty ( $value ) ) continue;
            $ids_v[] = (int)$value;

        }

        if ( empty ( $ids_v ) ) return FALSE;

        $ids_v = implode( ',', $ids_v );

        // instanciando o model
        $detmodel = new DetentoModel();

        // pegando os dados
        $q_det = $detmodel->getDetIndent( $ids_v );

        if ( !$q_det ) return FALSE;

        $det = array();

        while ( $dados = $q_det->fetch_object() ) {

            $dados->matricula = $this->formataNum( $dados->matricula );

            $dados->rg_civil = $this->formataNum( $dados->rg_civil );

            $det[] = $dados;

        }

        return $det;

    }

    public function findCartao( $ids = '' ){

        if ( empty ( $ids ) ) $ids = (int)$this->_iddet;

        if ( empty ( $ids ) ) return FALSE;

        $idd = $ids;

        if ( is_array( $ids ) ) {

            $idd = '';

            $ids_v = array();
            foreach ( $ids as &$value ) {

                $value = (int)$value;
                if ( empty ( $value ) ) continue;
                $ids_v[] = (int)$value;

            }

            if ( empty ( $ids_v ) ) return FALSE;

            $ids = implode( ',', $ids_v );

        }

        // instanciando o model
        $detmodel = new DetentoModel( $idd );

        // pegando os dados
        $q_det = $detmodel->getDetCartao( $ids );

        if ( !$q_det ) return FALSE;

        $det = array();

        while ( $dados = $q_det->fetch_object() ) {

            $dados->matricula = $this->formataNum( $dados->matricula );

            $dados->nasc_f = $this->formataDataNasc( $dados->nasc_det_f, $dados->idade_det );

            $dados->jaleco = !empty( $dados->jaleco ) ? 'J' : '';

            $dados->calca = !empty( $dados->calca ) ? 'C' : '';

            $det[] = $dados;

        }

        return $det;

    }

    public function findDetBasic( $param = '' ){

        $param = (int)$param;

        if ( empty( $param ) ) $param = (int)$this->_iddet;

        if ( empty( $param ) ) return FALSE;

        $detmodel = new DetentoModel( $param );

        $query = $detmodel->getDetBasic();

        if ( !$query ) return FALSE;

        $dados = '';
        $dados = $query->fetch_object();

        $dados->matricula_sn = $dados->matricula;

        $dados->matricula = $this->formataNum( $dados->matricula );

        $dados->rg_civil_sn = $dados->rg_civil;

        $dados->rg_civil = $this->formataNum( $dados->rg_civil );

        $dados->execucao_sn = $dados->execucao;

        $dados->execucao = $this->formataNum( $dados->execucao, 3 );

        $dados->cpf_sn = $dados->cpf;

        $dados->cpf = $this->formataNum( $dados->cpf, 2 );

        $dados->nasc_f = $this->formataDataNasc( $dados->nasc_det_f, $dados->idade_det );

        $dados->cidade = !empty( $dados->cidade ) ? $dados->cidade . ' - ' . $dados->estado : '';

        return $dados;

    }

    public function dadosDetF( $param = '' ){

        $param = (int)$param;

        if ( empty( $param ) ) $param = (int)$this->_iddet;

        if ( empty( $param ) ) return FALSE;

        $detmodel = new DetentoModel( $param );

        $detquery = $detmodel->getDadosDet( $param );

        if ( !$detquery ) return FALSE;

        $dados = '';
        $dados = $detquery->fetch_object();

        $quebra = PHP_EOL;

        $idd       = $dados->iddetento;
        $nome_det  = $dados->nome_det;
        $matricula = $this->formataNum( $dados->matricula );

        $nome_det  = SicopController::linkPag( $nome_det, "detento/detalhesdet.php?iddet=$idd" );

        $detento   = '[ ' . SICOP_DET_DESC_U . ' ]' . $quebra;
        $detento  .= "<b>ID:</b> $idd; <b>Nome:</b> $nome_det; <b>Matrícula:</b> $matricula";

        return $detento;

    }

    public function dadosDetFM( $ids ){

        if ( empty ( $ids ) ) return FALSE;

        if ( is_array( $ids ) ) {

            $ids_v = array();
            foreach ( $ids as &$value ) {

                $value = (int)$value;
                if ( empty ( $value ) ) continue;
                $ids_v[] = (int)$value;

            }

            if ( empty ( $ids_v ) ) return FALSE;

            $ids = implode( ',', $ids_v );

        }

        $detmodel = new DetentoModel();
        $q_det    = $detmodel->getDadosDet( $ids );

        if ( !$q_det ) return FALSE;

        $quebra  = PHP_EOL;
        $dados   = '';
        $detento = '[ ' . SICOP_DET_DESC_U . '(S) ]' . $quebra;
        while ( $dados = $q_det->fetch_object() ) {

            $idd       = $dados->iddetento;
            $nome_det  = $dados->nome_det;
            $matricula = $this->formataNum( $dados->matricula );

            $nome_det  = SicopController::linkPag( $nome_det, "detento/detalhesdet.php?iddet=$idd" );


            $detento  .= "<b>ID:</b> $idd; <b>Nome:</b> $nome_det; <b>Matrícula:</b> $matricula" . $quebra;

        }

        return $detento;

    }

    public function calPeriodo( $ano = 0, $mes = 0, $dia = 0 ) {

        $p = array( );

        $p['ano'] = $ano;
        $p['mes'] = $mes;
        $p['dia'] = $dia;
        $ac_mes = 0;
        $ac_ano = 0;

        if ( $p['dia'] > 29 ) {
            $ac_mes = floor( $p['dia'] / 30 );
            $p['dia'] = $p['dia'] % 30;
        }

        $p['mes'] = $p['mes'] + $ac_mes;

        if ( $p['mes'] > 11 ) {
            $ac_ano = floor( $p['mes'] / 12 );
            $p['mes'] = $p['mes'] % 12;
        }

        $p['ano'] = $p['ano'] + $ac_ano;

        $pf = '';

        if ( !empty( $p['ano'] ) ) {
            $pf .= $p['ano'] . ' ano(s)';
        }

        if ( !empty( $p['mes'] ) ) {
            if ( !empty( $p['ano'] ) ) {
                $pf .= ', ' . $p['mes'] . ' mes(es)';
            } else {
                $pf .= $p['mes'] . ' mes(es)';
            }
        }

        if ( !empty( $p['dia'] ) ) {
            if ( !empty( $p['mes'] ) || !empty( $p['ano'] ) ) {
                $pf .= ', ' . $p['dia'] . ' dia(s)';
            } else {
                $pf .= $p['dia'] . ' dia(s)';
            }
        }

        return $pf;
    }

    public function calCond() {

        $iddet = $this->_iddet;

        if ( empty ( $iddet ) ) return FALSE;


        $detmodel = new DetentoModel( $iddet );

        $detquery = $detmodel->getPenas();

        if ( !$detquery ) return FALSE;

        $dados = '';
        $dados = $detquery->fetch_object();

        return $this->calPeriodo( $dados->ano, $dados->mes, $dados->dia );

    }

    public function formataNum( $num, $type = 1 ) {

        switch ( $type ) {

            case 1:
                return !empty( $num ) ? SicopController::formataNum( $num ) : '';
                break;

            case 2:
                return !empty( $num ) ? SicopController::formataNum( $num, 2 ) : '';
                break;

            case 3:
                $num = (int)$num;
                return !empty( $num ) ? number_format( $num, 0, '', '.' ) : 'N/C';
                break;

            default:
                break;

        }

    }

    public function formataDataNasc( $data, $idade = '' ) {

        $data_nasc = !empty( $data ) ? $data  : '';

        if ( !empty ( $data_nasc ) and !empty ( $idade ) ) {
            $data_nasc .= ' - ' . $idade . ' anos';
        }

        return $data_nasc;

    }



}

?>
